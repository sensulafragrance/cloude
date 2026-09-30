/**
 * Nexus Beauty front-end. Plain JavaScript; jQuery is only used to listen to WooCommerce events.
 * Everything is progressive: without JS, links and forms still work.
 */
(function () {
	'use strict';

	var N = window.NEXUS || {};
	var T = N.i18n || {};
	var $ = function (s, r) { return (r || document).querySelector(s); };
	var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
	var jq = window.jQuery;
	var body = document.body;

	function fmt(n) {
		return (N.currency || 'Rs') + ' ' + Number(n).toLocaleString(undefined, { maximumFractionDigits: 2 });
	}
	function sprintf(s) {
		var a = Array.prototype.slice.call(arguments, 1), i = 0;
		return String(s).replace(/%(\d+\$)?[sd]/g, function () { return a[i++]; }).replace('%%', '%');
	}
	function post(action, data) {
		var fd = new FormData();
		fd.append('action', action);
		fd.append('nonce', N.nonce);
		Object.keys(data || {}).forEach(function (k) {
			var v = data[k];
			if (Array.isArray(v)) { v.forEach(function (x) { fd.append(k + '[]', x); }); } else { fd.append(k, v); }
		});
		return fetch(N.ajax, { method: 'POST', body: fd, credentials: 'same-origin' }).then(function (r) { return r.json(); });
	}
	function getCookie(name) {
		var m = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
		return m ? decodeURIComponent(m[1]) : '';
	}
	function setCookie(name, value, days) {
		document.cookie = name + '=' + encodeURIComponent(value) + '; path=/; max-age=' + (days * 86400) + '; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : '');
	}
	function store(key, val) {
		try {
			if (val === undefined) { return JSON.parse(localStorage.getItem(key)); }
			localStorage.setItem(key, JSON.stringify(val));
		} catch (e) { return null; }
	}
	function decode(html) {
		var t = document.createElement('textarea');
		t.innerHTML = html;
		return t.value;
	}

	/* ---------- Toast ---------- */
	var toastEl = $('.nx-toast'), toastT;
	function toast(msg) {
		if (!toastEl) { return; }
		toastEl.textContent = msg;
		toastEl.classList.add('is-on');
		clearTimeout(toastT);
		toastT = setTimeout(function () { toastEl.classList.remove('is-on'); }, 2400);
	}

	/* ---------- Announcement bar ---------- */
	var rot = $('[data-nx-rotate]');
	if (rot && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		var msgs = $$('p', rot), mi = 0;
		if (msgs.length > 1) {
			setInterval(function () {
				msgs[mi].hidden = true;
				mi = (mi + 1) % msgs.length;
				msgs[mi].hidden = false;
			}, 4500);
		}
	}

	/* ---------- Drawers (menu, bag, filters) ---------- */
	var scrim = $('.nx-scrim'), lastFocus = null, openId = null;
	function openDrawer(id) {
		var el = document.getElementById(id);
		if (!el) { return false; }
		closeDrawer();
		lastFocus = document.activeElement;
		openId = id;
		el.removeAttribute('inert');
		el.setAttribute('aria-hidden', 'false');
		el.classList.add('is-open');
		if (scrim) { scrim.hidden = false; requestAnimationFrame(function () { scrim.classList.add('is-open'); }); }
		body.classList.add('nx-lock');
		$$('[data-nx-open="' + id + '"]').forEach(function (b) { b.setAttribute('aria-expanded', 'true'); });
		var f = $('button, a[href], input, select', el);
		if (f) { setTimeout(function () { f.focus({ preventScroll: true }); }, 50); }
		return true;
	}
	function closeDrawer() {
		if (!openId) { return; }
		var el = document.getElementById(openId);
		if (el) {
			el.classList.remove('is-open');
			if (el.id !== 'nx-filters') { el.setAttribute('aria-hidden', 'true'); el.setAttribute('inert', ''); }
		}
		$$('[data-nx-open="' + openId + '"]').forEach(function (b) { b.setAttribute('aria-expanded', 'false'); });
		openId = null;
		if (scrim) { scrim.classList.remove('is-open'); setTimeout(function () { if (!openId) { scrim.hidden = true; } }, 250); }
		body.classList.remove('nx-lock');
		if (lastFocus && lastFocus.focus) { lastFocus.focus({ preventScroll: true }); }
	}
	var onCartPage = body.classList.contains('woocommerce-cart') || body.classList.contains('woocommerce-checkout');
	document.addEventListener('click', function (e) {
		var o = e.target.closest('[data-nx-open]');
		if (o) {
			if (o.getAttribute('data-nx-open') === 'nx-cart' && onCartPage) { return; }
			if (openDrawer(o.getAttribute('data-nx-open'))) { e.preventDefault(); }
			return;
		}
		if (e.target.closest('[data-nx-close]')) { closeDrawer(); }
	});
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') { closeDrawer(); closeSearch(); }
	});

	/* ---------- Search with live results ---------- */
	var sp = $('#nx-search'), sBtn = $('[data-nx-search]'), sIn = $('[data-nx-live-search]'), sRes = $('[data-nx-results]'), sT, sCtl;
	function closeSearch() {
		if (sp && !sp.hidden) { sp.hidden = true; if (sBtn) { sBtn.setAttribute('aria-expanded', 'false'); } }
	}
	if (sp && sBtn) {
		sBtn.addEventListener('click', function () {
			sp.hidden = !sp.hidden;
			sBtn.setAttribute('aria-expanded', String(!sp.hidden));
			if (!sp.hidden && sIn) { sIn.focus(); }
		});
		document.addEventListener('click', function (e) {
			if (!sp.hidden && !e.target.closest('#nx-search') && !e.target.closest('[data-nx-search]')) { closeSearch(); }
		});
	}
	if (sIn && sRes && N.storeApi) {
		sIn.addEventListener('input', function () {
			clearTimeout(sT);
			var q = sIn.value.trim();
			if (q.length < 2) { sRes.innerHTML = ''; return; }
			sT = setTimeout(function () {
				if (sCtl) { sCtl.abort(); }
				sCtl = window.AbortController ? new AbortController() : null;
				fetch(N.storeApi + (N.storeApi.indexOf('?') > -1 ? '&' : '?') + 'per_page=6&search=' + encodeURIComponent(q), { signal: sCtl ? sCtl.signal : undefined })
					.then(function (r) { return r.json(); })
					.then(function (items) {
						if (!Array.isArray(items) || !items.length) { sRes.innerHTML = '<p class="nx-search__empty">' + T.noResults + '</p>'; return; }
						sRes.innerHTML = items.map(function (p) {
							var img = p.images && p.images[0] ? '<img src="' + p.images[0].thumbnail + '" alt="" width="48" height="48" loading="lazy">' : '<span class="nx-search__ph"></span>';
							var pr = p.prices ? Number(p.prices.price) / Math.pow(10, p.prices.currency_minor_unit || 0) : 0;
							var a = document.createElement('a');
							a.className = 'nx-search__row';
							a.href = p.permalink;
							a.innerHTML = img + '<span class="nx-search__name"></span><span class="nx-search__price">' + fmt(pr) + '</span>';
							a.querySelector('.nx-search__name').textContent = decode(p.name);
							return a.outerHTML;
						}).join('') + '<a class="nx-search__all" href="' + (N.home || '/') + '?s=' + encodeURIComponent(q) + '&post_type=product">' + T.viewAll + ' →</a>';
					})
					.catch(function () {});
			}, 250);
		});
	}

	/* ---------- Cart: open the bag after adding, live quantity, fragments ---------- */
	var quiet = false;
	function applyFragments(fr) {
		if (!fr) { return; }
		Object.keys(fr).forEach(function (sel) {
			$$(sel).forEach(function (el) { el.outerHTML = fr[sel]; });
		});
	}
	function afterCartChange(res, message) {
		if (!res || !res.fragments) { toast(T.error); return; }
		applyFragments(res.fragments);
		quiet = true;
		if (jq) { jq(document.body).trigger('added_to_cart', [res.fragments, res.cart_hash]); }
		quiet = false;
		if (message) { toast(message); }
	}
	if (jq) {
		jq(document.body).on('added_to_cart', function () {
			if (quiet) { openDrawer('nx-cart'); return; }
			toast(T.added);
			if (!onCartPage) { openDrawer('nx-cart'); }
		});
	}
	document.addEventListener('click', function (e) {
		var b = e.target.closest('[data-nx-qty]');
		if (!b) { return; }
		var wrap = b.closest('.nx-mq'), n = parseInt($('.nx-mq__n', wrap).textContent, 10) || 0;
		wrap.classList.add('is-busy');
		post('nexus_update_qty', { key: wrap.getAttribute('data-key'), qty: Math.max(0, n + parseInt(b.getAttribute('data-nx-qty'), 10)) })
			.then(function (r) { afterCartChange(r); })
			.catch(function () { wrap.classList.remove('is-busy'); toast(T.error); });
	});

	/* ---------- Bundles: frequently bought together and kits ---------- */
	$$('[data-nx-bundle]').forEach(function (bx) {
		var boxes = $$('input[type=checkbox]', bx), btn = $('[data-nx-b-add]', bx);
		function update() {
			var on = boxes.filter(function (c) { return c.checked; }), sub = 0;
			on.forEach(function (c) { sub += parseFloat(c.getAttribute('data-price')) || 0; });
			var n = on.length, disc = n >= N.bundleMin ? sub * N.bundlePct / 100 : 0;
			$('[data-nx-b-count]', bx).textContent = n;
			$('[data-nx-b-sub]', bx).textContent = fmt(sub);
			var dr = $('[data-nx-b-disc-row]', bx);
			if (dr) { dr.hidden = !disc; $('[data-nx-b-disc]', bx).textContent = '−' + fmt(disc); }
			$('[data-nx-b-total]', bx).textContent = fmt(sub - disc);
			btn.disabled = !n;
			btn.textContent = sprintf(T.addN, n);
			var note = $('[data-nx-b-note]', bx);
			if (note && N.bundlePct) { note.textContent = n >= N.bundleMin ? sprintf(T.applied, N.bundlePct) : sprintf(T.chooseMore, N.bundleMin - n, N.bundlePct); }
		}
		boxes.forEach(function (c) { c.addEventListener('change', update); });
		update();
		btn.addEventListener('click', function () {
			var ids = boxes.filter(function (c) { return c.checked; }).map(function (c) { return c.value; });
			if (!ids.length) { return; }
			btn.disabled = true;
			post('nexus_add_bundle', { ids: ids, name: bx.getAttribute('data-name') || '' })
				.then(function (r) {
					btn.disabled = false;
					if (r && r.success === false) { toast((r.data && r.data.message) || T.error); return; }
					afterCartChange(r, T.added);
				})
				.catch(function () { btn.disabled = false; toast(T.error); });
		});
	});

	/* ---------- Wishlist ---------- */
	function wishIds() { return getCookie('nx_wishlist').split(',').filter(Boolean); }
	function paintWish() {
		var ids = wishIds();
		$$('[data-nx-wish]').forEach(function (b) { b.setAttribute('aria-pressed', String(ids.indexOf(b.getAttribute('data-nx-wish')) > -1)); });
		$$('[data-nx-wish-count]').forEach(function (c) { c.textContent = ids.length; c.hidden = !ids.length; });
	}
	function saveWish(ids, replace) {
		setCookie('nx_wishlist', ids.join(','), 365);
		paintWish();
		if (N.loggedIn) {
			post('nexus_wishlist_sync', { ids: ids.join(','), replace: replace ? 1 : 0 }).then(function (r) {
				if (r && r.success && r.data && !replace) { setCookie('nx_wishlist', r.data.ids.join(','), 365); paintWish(); }
			}).catch(function () {});
		}
	}
	document.addEventListener('click', function (e) {
		var b = e.target.closest('[data-nx-wish]');
		if (!b) { return; }
		e.preventDefault();
		var id = b.getAttribute('data-nx-wish'), ids = wishIds(), i = ids.indexOf(id);
		if (i > -1) { ids.splice(i, 1); } else { ids.unshift(id); }
		saveWish(ids.slice(0, 100), true);
		toast(i > -1 ? T.removed : T.saved);
		if (i > -1 && b.closest('[data-nx-wishlist]')) {
			var li = b.closest('li.product');
			if (li) { li.remove(); }
			if (!$$('[data-nx-wishlist] li.product').length) { location.reload(); }
		}
	});
	paintWish();
	try {
		if (N.loggedIn && !sessionStorage.getItem('nx_wish_synced')) {
			sessionStorage.setItem('nx_wish_synced', '1');
			saveWish(wishIds(), false);
		}
	} catch (e) {}

	/* ---------- Recently viewed (stored in this browser) ---------- */
	var pm = body.className.match(/\bnx-product-(\d+)\b/), current = pm ? pm[1] : null;
	var recent = (store('nx_recent') || []).filter(Boolean);
	if (current) {
		recent = [current].concat(recent.filter(function (x) { return x !== current; })).slice(0, 12);
		store('nx_recent', recent);
	}
	$$('[data-nx-recent]').forEach(function (sec) {
		var limit = parseInt(sec.getAttribute('data-limit'), 10) || 8;
		var ids = recent.filter(function (x) { return x !== current; }).slice(0, limit);
		if (!ids.length || !N.storeApi) { return; }
		fetch(N.storeApi + (N.storeApi.indexOf('?') > -1 ? '&' : '?') + 'include=' + ids.join(',') + '&per_page=' + ids.length)
			.then(function (r) { return r.json(); })
			.then(function (items) {
				if (!Array.isArray(items) || !items.length) { return; }
				items.sort(function (a, b) { return ids.indexOf(String(a.id)) - ids.indexOf(String(b.id)); });
				$('[data-nx-recent-list]', sec).innerHTML = items.map(function (p) {
					var a = document.createElement('a');
					a.className = 'nx-mini';
					a.href = p.permalink;
					var pr = p.prices ? Number(p.prices.price) / Math.pow(10, p.prices.currency_minor_unit || 0) : 0;
					a.innerHTML = (p.images && p.images[0] ? '<img src="' + p.images[0].thumbnail + '" alt="" width="56" height="56" loading="lazy">' : '<span class="nx-mini__ph"></span>') + '<span><b></b><span class="nx-mini__price">' + fmt(pr) + '</span></span>';
					a.querySelector('b').textContent = decode(p.name);
					return a.outerHTML;
				}).join('');
				sec.hidden = false;
			})
			.catch(function () {});
	});

	/* ---------- Shop filters ---------- */
	var ff = $('[data-nx-filter-form]');
	if (ff) {
		var submitClean = function () {
			$$('input', ff).forEach(function (i) { if ((i.type === 'number' || i.type === 'hidden') && i.value === '') { i.disabled = true; } });
			ff.submit();
		};
		ff.addEventListener('change', function (e) {
			// Apply instantly on desktop; on phones the "Show products" button applies.
			if (openId !== 'nx-filters' && e.target.type === 'checkbox') { submitClean(); }
		});
		ff.addEventListener('submit', function (e) { e.preventDefault(); submitClean(); });
		$$('[data-nx-price]', ff).forEach(function (b) {
			b.addEventListener('click', function () {
				var v = b.getAttribute('data-nx-price').split(',');
				ff.elements.min_price.value = v[0] === '0' ? '' : v[0];
				ff.elements.max_price.value = v[1] || '';
				if (openId !== 'nx-filters') { submitClean(); }
			});
		});
	}

	/* ---------- Quantity steppers (product and cart pages) ---------- */
	function stepper(q) {
		if (q.querySelector('.nx-step') || !q.querySelector('input.qty') || q.querySelector('input[type=hidden]')) { return; }
		var inp = q.querySelector('input.qty');
		var mk = function (d, label) {
			var b = document.createElement('button');
			b.type = 'button';
			b.className = 'nx-step';
			b.setAttribute('aria-label', label);
			b.textContent = d > 0 ? '+' : '−';
			b.addEventListener('click', function () {
				var step = parseFloat(inp.step) || 1, min = inp.min !== '' ? parseFloat(inp.min) : 0, max = inp.max !== '' ? parseFloat(inp.max) : Infinity;
				var v = Math.min(max, Math.max(min, (parseFloat(inp.value) || 0) + d * step));
				inp.value = v;
				inp.dispatchEvent(new Event('change', { bubbles: true }));
				if (jq) { jq(inp).trigger('change'); }
			});
			return b;
		};
		q.insertBefore(mk(-1, '−1'), inp);
		q.appendChild(mk(1, '+1'));
		q.classList.add('nx-qty');
	}
	$$('.quantity').forEach(stepper);
	if (jq) { jq(document.body).on('updated_cart_totals', function () { $$('.quantity').forEach(stepper); }); }

	/* ---------- Delivery estimate ---------- */
	var DAYS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'], MON = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	function eta(range) {
		var r = String(range || N.defaultDays || '3-5').split('-'), now = new Date(Date.now() + (N.tzOffset || 0) * 36e5);
		var work = function (d) { return !(N.sundayOff && d.getUTCDay() === 0); };
		var add = function (d, n) { var x = new Date(d.getTime()); while (n > 0) { x.setUTCDate(x.getUTCDate() + 1); if (work(x)) { n--; } } return x; };
		var f = function (d) { return DAYS[d.getUTCDay()] + ' ' + d.getUTCDate() + ' ' + MON[d.getUTCMonth()]; };
		var today = work(now) && now.getUTCHours() < N.cutoff;
		var disp = today ? now : add(now, 1);
		var left = (N.cutoff * 60) - (now.getUTCHours() * 60 + now.getUTCMinutes());
		return {
			text: f(add(disp, +r[0])) + ' – ' + f(add(disp, +(r[1] || r[0]))),
			cut: today ? sprintf(T.orderIn, Math.floor(left / 60) + ' h ' + (left % 60) + ' min') : sprintf(T.leavesOn, f(disp))
		};
	}
	var etaBox = $('[data-nx-eta]');
	if (etaBox) {
		var cs = $('[data-nx-city]', etaBox);
		var saved = store('nx_city');
		if (saved !== null && cs.options[saved]) { cs.selectedIndex = saved; }
		var paint = function () {
			var e = eta(cs.value);
			$('[data-nx-eta-text]', etaBox).innerHTML = T.arrives + ' <b>' + e.text + '</b>';
			$('[data-nx-eta-cut]', etaBox).textContent = e.cut;
		};
		cs.addEventListener('change', function () { store('nx_city', cs.selectedIndex); paint(); });
		paint();
	}
	function checkoutEta() {
		var el = $('[data-nx-checkout-eta]');
		if (!el) { return; }
		var ship = $('#ship-to-different-address-checkbox');
		var cityIn = (ship && ship.checked) ? $('#shipping_city') : $('#billing_city');
		var city = cityIn ? cityIn.value.trim().toLowerCase() : '';
		var range = null;
		Object.keys(N.cities || {}).forEach(function (c) { if (c.toLowerCase() === city) { range = N.cities[c]; } });
		el.innerHTML = city ? T.arrives + ' <b>' + eta(range).text + '</b>' : '';
	}
	document.addEventListener('input', function (e) { if (e.target.id === 'billing_city' || e.target.id === 'shipping_city') { checkoutEta(); } });
	if (jq) { jq(document.body).on('updated_checkout', checkoutEta); }

	/* ---------- Variations: unit price and batch stamp ---------- */
	if (jq) {
		jq('form.variations_form').on('found_variation', function (e, v) {
			var u = $('[data-nx-unit]'), bt = $('[data-nx-batch]');
			if (u) { u.textContent = v.nx_unit || ''; u.hidden = !v.nx_unit; }
			if (bt && typeof v.nx_batch === 'string') { bt.innerHTML = v.nx_batch; }
		}).on('reset_data', function () {
			var u = $('[data-nx-unit]');
			if (u) { u.hidden = true; }
		});
	}

	/* ---------- Sticky add to bag ---------- */
	var sticky = $('[data-nx-sticky]'), mainBtn = $('form.cart .single_add_to_cart_button');
	if (sticky && mainBtn && 'IntersectionObserver' in window) {
		var sb = $('[data-nx-sticky-btn]', sticky);
		new IntersectionObserver(function (en) {
			var on = !en[0].isIntersecting && en[0].boundingClientRect.top < 0;
			sticky.classList.toggle('is-on', on);
			sticky.setAttribute('aria-hidden', String(!on));
			sb.tabIndex = on ? 0 : -1;
			body.classList.toggle('nx-sticky-on', on);
		}).observe(mainBtn);
		sb.addEventListener('click', function () {
			var form = mainBtn.closest('form');
			if (form && form.classList.contains('variations_form')) {
				form.scrollIntoView({ behavior: 'smooth', block: 'center' });
			} else {
				mainBtn.click();
			}
		});
	}

	/* ---------- Tabs ---------- */
	$$('[data-nx-tabs]').forEach(function (w) {
		var tabs = $$('[role=tab]', w);
		tabs.forEach(function (t, i) {
			t.addEventListener('click', function () {
				tabs.forEach(function (x) { x.setAttribute('aria-selected', String(x === t)); });
				$$('[role=tabpanel]', w).forEach(function (p) { p.hidden = p.id !== t.getAttribute('aria-controls'); });
			});
			t.addEventListener('keydown', function (e) {
				var d = e.key === 'ArrowRight' ? 1 : e.key === 'ArrowLeft' ? -1 : 0;
				if (d) { var n = tabs[(i + d + tabs.length) % tabs.length]; n.focus(); n.click(); }
			});
		});
	});

	/* ---------- Lightweight YouTube ---------- */
	document.addEventListener('click', function (e) {
		var a = e.target.closest('[data-nx-yt]');
		if (!a) { return; }
		e.preventDefault();
		var f = document.createElement('iframe');
		f.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(a.getAttribute('data-nx-yt')) + '?autoplay=1&rel=0';
		f.title = a.getAttribute('data-title') || 'Video';
		f.allow = 'autoplay; encrypted-media; picture-in-picture';
		f.allowFullscreen = true;
		f.className = 'nx-video nx-video--frame';
		a.replaceWith(f);
	});

	/* ---------- Cookie notice, back to top ---------- */
	var ck = $('[data-nx-cookie]');
	if (ck && !getCookie('nx_cookies')) { ck.hidden = false; }
	$$('[data-nx-cookie-choice]').forEach(function (b) {
		b.addEventListener('click', function () {
			setCookie('nx_cookies', b.getAttribute('data-nx-cookie-choice'), 180);
			ck.hidden = true;
			document.dispatchEvent(new CustomEvent('nexus:cookies', { detail: b.getAttribute('data-nx-cookie-choice') }));
		});
	});
	var top = $('[data-nx-top]');
	if (top) {
		window.addEventListener('scroll', function () { top.hidden = window.scrollY < 900; }, { passive: true });
		top.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
	}
})();
