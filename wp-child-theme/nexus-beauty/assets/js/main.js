/**
 * Nexus Beauty (Sensula design) front-end. Plain JavaScript; jQuery is only used to talk to WooCommerce.
 * Everything is progressive: without JavaScript, links and forms still work.
 */
(function () {
	'use strict';

	var N = window.NEXUS || {};
	var T = N.i18n || {};
	var $ = function (s, r) { return (r || document).querySelector(s); };
	var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
	var jq = window.jQuery;
	var body = document.body;

	function money(n) {
		var d = N.decimals || 0;
		return (N.currency || 'Rs') + ' ' + Number(n).toLocaleString('en-US', { minimumFractionDigits: d, maximumFractionDigits: d });
	}
	function sprintf(s) {
		var a = Array.prototype.slice.call(arguments, 1), i = 0;
		return String(s).replace(/%(\d+\$)?[sd]/g, function () { return a[i++]; }).replace('%%', '%');
	}
	function post(action, data) {
		var fd = data instanceof FormData ? data : new FormData();
		fd.append('action', action);
		fd.append('nonce', N.nonce);
		if (!(data instanceof FormData)) {
			Object.keys(data || {}).forEach(function (k) { fd.append(k, data[k]); });
		}
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
	function esc(s) {
		var d = document.createElement('div');
		d.textContent = s == null ? '' : String(s);
		return d.innerHTML;
	}

	/* ---------- Toast ---------- */
	var toastEl = $('.toast'), toastT;
	function toast(msg) {
		if (!toastEl || !msg) { return; }
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

	/* ---------- Overlays: bag, menu, search, filters ---------- */
	var scrim = $('.scrim'), lastFocus = null, openEl = null;
	function openPanel(el) {
		if (!el) { return false; }
		closeAll(true);
		lastFocus = document.activeElement;
		openEl = el;
		el.classList.add('is-open');
		el.setAttribute('aria-hidden', 'false');
		if (scrim) { scrim.classList.add('is-open'); }
		body.classList.add('nx-lock');
		var f = $('input[type=search], button, a[href], input, select', el);
		if (f) { setTimeout(function () { f.focus({ preventScroll: true }); }, 60); }
		return true;
	}
	function closeAll(silent) {
		$$('.drawer, .mnav, .filters, .searchpanel').forEach(function (el) {
			el.classList.remove('is-open');
			if (!el.classList.contains('filters')) { el.setAttribute('aria-hidden', 'true'); }
		});
		if (scrim) { scrim.classList.remove('is-open'); }
		body.classList.remove('nx-lock');
		if (openEl && !silent && lastFocus && lastFocus.focus) { lastFocus.focus({ preventScroll: true }); }
		openEl = null;
	}
	var onCartPage = body.classList.contains('woocommerce-cart') || body.classList.contains('woocommerce-checkout');
	function openBag() { if (!onCartPage) { openPanel($('.drawer')); } }
	document.addEventListener('click', function (e) {
		var t = e.target;
		if (t.closest('[data-open-cart]')) { if (!onCartPage && $('.drawer')) { e.preventDefault(); openBag(); } return; }
		if (t.closest('[data-open-menu]')) { openPanel($('.mnav')); return; }
		if (t.closest('[data-open-search]')) { openPanel($('.searchpanel')); return; }
		if (t.closest('[data-open-filters]')) { openPanel($('.filters')); return; }
		if (t.closest('[data-close]')) { closeAll(); }
	});
	document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && openEl) { closeAll(); } });

	/* ---------- Live search ---------- */
	var sIn = $('[data-nx-live-search]'), sRes = $('[data-nx-results]'), sT, sCtl;
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
						if (!Array.isArray(items) || !items.length) { sRes.innerHTML = '<p>' + esc(T.noResults) + '</p>'; return; }
						sRes.innerHTML = items.map(function (p) {
							var img = p.images && p.images[0] ? '<img src="' + esc(p.images[0].thumbnail) + '" alt="" width="48" height="48" loading="lazy">' : '<span class="ph"></span>';
							var pr = p.prices ? Number(p.prices.price) / Math.pow(10, p.prices.currency_minor_unit || 0) : 0;
							return '<a href="' + esc(p.permalink) + '">' + img + '<b>' + esc(decode(p.name)) + '</b><span class="price__now">' + esc(money(pr)) + '</span></a>';
						}).join('') + '<a class="all" href="' + esc((N.home || '/') + '?post_type=product&s=' + encodeURIComponent(q)) + '">' + esc(T.viewAll) + ' →</a>';
					})
					.catch(function () {});
			}, 250);
		});
	}

	/* ---------- Bag: fragments, quantities, add to bag ---------- */
	var quiet = false;
	function applyFragments(fr) {
		if (!fr) { return; }
		Object.keys(fr).forEach(function (sel) {
			$$(sel).forEach(function (el) { el.outerHTML = fr[sel]; });
		});
	}
	function afterCartChange(res, message) {
		if (!res || !res.fragments) { toast((res && res.data && res.data.message) || T.error); return false; }
		applyFragments(res.fragments);
		quiet = true;
		if (jq) {
			jq(document.body).trigger('wc_fragment_refresh_done');
			jq(document.body).trigger('added_to_cart', [res.fragments, res.cart_hash]);
		}
		quiet = false;
		toast(message);
		openBag();
		return true;
	}
	if (jq) {
		// WooCommerce's own AJAX add to bag (product cards).
		jq(document.body).on('added_to_cart', function () {
			if (quiet) { return; }
			toast(T.added);
			openBag();
		});
	}
	function setQty(line, qty) {
		var box = $('.line__qty', line);
		if (box) { box.classList.add('is-busy'); }
		post('nexus_update_qty', { key: line.getAttribute('data-key'), qty: Math.max(0, qty) })
			.then(function (r) {
				if (r && r.fragments) { applyFragments(r.fragments); if (jq) { jq(document.body).trigger('wc_fragments_refreshed'); } }
				else { toast(T.error); if (box) { box.classList.remove('is-busy'); } }
			})
			.catch(function () { toast(T.error); if (box) { box.classList.remove('is-busy'); } });
	}
	document.addEventListener('click', function (e) {
		var b = e.target.closest('[data-nx-qty]'), rm = e.target.closest('[data-nx-remove]');
		var line = (b || rm) ? (b || rm).closest('.line') : null;
		if (!line) { return; }
		e.preventDefault();
		if (rm) { setQty(line, 0); return; }
		var n = parseInt($('.line__qty span', line).textContent, 10) || 0;
		setQty(line, n + parseInt(b.getAttribute('data-nx-qty'), 10));
	});

	// Product page form: add to bag without reloading (simple and variable products).
	var buyNowClicked = false;
	document.addEventListener('click', function (e) { buyNowClicked = !!e.target.closest('[data-nx-buy-now]'); }, true);
	document.addEventListener('submit', function (e) {
		var form = e.target;
		if (!form.matches || !form.matches('.buybox form.cart') || form.classList.contains('grouped_form')) { return; }
		var sub = e.submitter;
		if ((sub && sub.hasAttribute('data-nx-buy-now')) || (!sub && buyNowClicked)) { return; }
		var idField = $('[name="add-to-cart"]', form);
		if (!idField || !window.FormData) { return; }
		e.preventDefault();
		var btn = $('.single_add_to_cart_button', form);
		var fd = new FormData(form);
		fd.delete('add-to-cart');
		fd.set('product_id', ($('[name="product_id"]', form) || idField).value);
		if (btn) { btn.classList.add('loading'); btn.disabled = true; }
		post('nexus_add_to_cart', fd)
			.then(function (r) {
				if (btn) { btn.classList.remove('loading'); btn.disabled = false; }
				if (r && r.success === false) { toast((r.data && r.data.message) || T.error); return; }
				afterCartChange(r, T.added);
			})
			.catch(function () {
				if (btn) { btn.classList.remove('loading'); btn.disabled = false; }
				form.submit();
			});
	});

	/* ---------- Bundles: frequently bought together and kits ---------- */
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('[data-nx-bundle-add]');
		if (!btn) { return; }
		var bx = btn.closest('[data-nx-bundle]');
		btn.disabled = true;
		btn.classList.add('loading');
		post('nexus_add_bundle', { ids: bx.getAttribute('data-ids'), name: bx.getAttribute('data-name') || '' })
			.then(function (r) {
				btn.disabled = false;
				btn.classList.remove('loading');
				if (r && r.success === false) { toast((r.data && r.data.message) || T.error); return; }
				afterCartChange(r, T.bundle);
			})
			.catch(function () { btn.disabled = false; btn.classList.remove('loading'); toast(T.error); });
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
			var card = b.closest('.card');
			if (card) { card.remove(); }
			if (!$$('[data-nx-wishlist] .card').length) { location.reload(); }
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
		var limit = parseInt(sec.getAttribute('data-limit'), 10) || 4;
		var ids = recent.filter(function (x) { return x !== current; }).slice(0, limit);
		if (!ids.length || !N.storeApi) { return; }
		fetch(N.storeApi + (N.storeApi.indexOf('?') > -1 ? '&' : '?') + 'include=' + ids.join(',') + '&per_page=' + ids.length)
			.then(function (r) { return r.json(); })
			.then(function (items) {
				if (!Array.isArray(items) || !items.length) { return; }
				items.sort(function (a, b) { return ids.indexOf(String(a.id)) - ids.indexOf(String(b.id)); });
				$('[data-nx-recent-list]', sec).innerHTML = items.map(function (p) {
					var pr = p.prices ? Number(p.prices.price) / Math.pow(10, p.prices.currency_minor_unit || 0) : 0;
					var img = p.images && p.images[0] ? '<img src="' + esc(p.images[0].src) + '" alt="" loading="lazy">' : '<svg aria-hidden="true" viewBox="0 0 100 160"><use href="#i-bottle"/></svg>';
					var brand = p.brands && p.brands[0] ? '<p class="card__brand">' + esc(decode(p.brands[0].name)) + '</p>' : '';
					return '<article class="card"><div class="card__media" style="--tone:#b04a6b;--bg:#f7e5eb">' + img + '</div>' + brand +
						'<h3 class="card__title"><a href="' + esc(p.permalink) + '">' + esc(decode(p.name)) + '</a></h3>' +
						'<div class="card__meta"><p class="price"><span class="price__now">' + esc(money(pr)) + '</span></p></div></article>';
				}).join('');
				sec.hidden = false;
			})
			.catch(function () {});
	});

	/* ---------- Shop filters ---------- */
	var ff = $('[data-nx-filter-form]');
	if (ff) {
		var range = $('#price-max', ff), out = $('#price-out', ff);
		var submitClean = function () {
			if (range && Number(range.value) >= Number(range.getAttribute('data-max'))) { range.disabled = true; }
			$$('input[type=hidden]', ff).forEach(function (i) { if (i.value === '') { i.disabled = true; } });
			ff.submit();
		};
		var instant = function () { return !ff.closest('.filters').classList.contains('is-open'); };
		ff.addEventListener('change', function (e) {
			// Apply instantly on desktop; on phones the "Show products" button applies.
			if (instant() && (e.target.type === 'checkbox' || e.target === range)) { submitClean(); }
		});
		ff.addEventListener('submit', function (e) { e.preventDefault(); submitClean(); });
		if (range && out) {
			range.addEventListener('input', function () { out.textContent = money(range.value); });
		}
	}

	/* ---------- Quantity steppers (product and cart pages) ---------- */
	function stepper(q) {
		var inp = q.querySelector('input.qty');
		if (!inp || q.querySelector('[data-step]') || inp.type === 'hidden') { return; }
		var mk = function (d, label) {
			var b = document.createElement('button');
			b.type = 'button';
			b.setAttribute('data-step', d);
			b.setAttribute('aria-label', label);
			b.textContent = d > 0 ? '+' : '−';
			b.addEventListener('click', function () {
				var step = parseFloat(inp.step) || 1, min = inp.min !== '' ? parseFloat(inp.min) : 0, max = inp.max !== '' ? parseFloat(inp.max) : Infinity;
				inp.value = Math.min(max, Math.max(min, (parseFloat(inp.value) || 0) + d * step));
				inp.dispatchEvent(new Event('change', { bubbles: true }));
				if (jq) { jq(inp).trigger('change'); }
			});
			return b;
		};
		q.insertBefore(mk(-1, 'Decrease quantity'), inp);
		q.appendChild(mk(1, 'Increase quantity'));
	}
	$$('.quantity').forEach(stepper);
	if (jq) { jq(document.body).on('updated_cart_totals updated_wc_div', function () { $$('.quantity').forEach(stepper); }); }

	/* ---------- Delivery estimate ---------- */
	var DAYS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'], MON = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	function eta(range) {
		var r = String(range || N.defaultDays || '3-5').split('-'), now = new Date(Date.now() + (N.tzOffset || 0) * 36e5);
		var work = function (d) { return !(N.sundayOff && d.getUTCDay() === 0); };
		var add = function (d, n) { var x = new Date(d.getTime()); while (n > 0) { x.setUTCDate(x.getUTCDate() + 1); if (work(x)) { n--; } } return x; };
		var f = function (d) { return DAYS[d.getUTCDay()] + ' ' + d.getUTCDate() + ' ' + MON[d.getUTCMonth()]; };
		var disp = (work(now) && now.getUTCHours() < N.cutoff) ? now : add(now, 1);
		return f(add(disp, +r[0])) + ' – ' + f(add(disp, +(r[1] || r[0])));
	}
	var etaBox = $('[data-nx-eta]');
	if (etaBox) {
		var cs = $('[data-nx-city]', etaBox), saved = store('nx_city');
		if (saved !== null && cs.options[saved]) { cs.selectedIndex = saved; }
		var paintEta = function () { $('[data-nx-eta-text]', etaBox).textContent = eta(cs.value); };
		cs.addEventListener('change', function () { store('nx_city', cs.selectedIndex); paintEta(); });
		paintEta();
	}
	function checkoutEta() {
		var el = $('[data-nx-checkout-eta]');
		if (!el) { return; }
		var ship = $('#ship-to-different-address-checkbox');
		var cityIn = (ship && ship.checked) ? $('#shipping_city') : $('#billing_city');
		var city = cityIn ? cityIn.value.trim().toLowerCase() : '';
		var range = null;
		Object.keys(N.cities || {}).forEach(function (c) { if (c.toLowerCase() === city) { range = N.cities[c]; } });
		el.innerHTML = city ? esc(T.arrives) + ' <b>' + esc(eta(range)) + '</b>' : '';
	}
	document.addEventListener('input', function (e) { if (e.target.id === 'billing_city' || e.target.id === 'shipping_city') { checkoutEta(); } });
	if (jq) { jq(document.body).on('updated_checkout', checkoutEta); }

	/* ---------- Product page: price, variations as size buttons, gallery ---------- */
	var priceNow = $$('[data-pdp-price]'), priceWas = $('.buybox [data-pdp-was]'), priceSave = $('.buybox [data-pdp-save]');
	var origNow = priceNow.map(function (el) { return el.textContent; });
	var origWasHTML = priceWas ? priceWas.outerHTML : '';
	function setPrice(now, was) {
		priceNow.forEach(function (el) { el.textContent = money(now); });
		if (priceWas) { priceWas.hidden = !(was > now); if (was > now) { priceWas.textContent = money(was); } }
		if (priceSave) { priceSave.hidden = !(was > now); if (was > now) { priceSave.textContent = sprintf(T.save, Math.round((1 - now / was) * 100)); } }
	}
	function addPriceToButton() {
		var btn = $('.buybox .single_add_to_cart_button');
		var now = $('.buybox .price__now');
		if (!btn || !now || btn.closest('.variations_form')) { return; }
		btn.innerHTML = esc(btn.textContent.trim()) + ' · <span data-pdp-price>' + esc(now.textContent.replace(/^\D*From\s*/, '')) + '</span>';
	}
	addPriceToButton();

	var stage = $('.stage'), stageImg = stage ? $('.stage__img', stage) : null, note = $('[data-stage-note]');
	var stageOrig = stageImg ? { src: stageImg.getAttribute('src'), srcset: stageImg.getAttribute('srcset') || '' } : null;
	function showStage(src, srcset, text) {
		if (stageImg && src) { stageImg.src = src; if (srcset) { stageImg.srcset = srcset; } else { stageImg.removeAttribute('srcset'); } }
		if (note) { note.textContent = text || ''; note.hidden = !text; }
	}
	$$('.thumbs button').forEach(function (b) {
		b.addEventListener('click', function () {
			$$('.thumbs button').forEach(function (x) { x.setAttribute('aria-current', String(x === b)); });
			if (stage) { stage.setAttribute('data-view', b.getAttribute('data-view')); }
			showStage(b.getAttribute('data-src'), b.getAttribute('data-srcset'), b.getAttribute('data-note'));
		});
	});

	function enhanceVariations(form) {
		$$('table.variations select', form).forEach(function (sel) {
			sel.classList.add('nx-enhanced');
			var box = sel.parentNode.querySelector('.sizes');
			if (!box) {
				box = document.createElement('div');
				box.className = 'sizes';
				box.setAttribute('role', 'group');
				sel.parentNode.insertBefore(box, sel);
			}
			box.innerHTML = '';
			Array.prototype.forEach.call(sel.options, function (o) {
				if (!o.value) { return; }
				var b = document.createElement('button');
				b.type = 'button';
				b.setAttribute('aria-pressed', String(sel.value === o.value));
				b.disabled = o.disabled;
				b.innerHTML = '<b>' + esc(o.textContent) + '</b>';
				b.addEventListener('click', function () {
					sel.value = sel.value === o.value ? '' : o.value;
					if (jq) { jq(sel).trigger('change'); } else { sel.dispatchEvent(new Event('change', { bubbles: true })); }
					$$('button', box).forEach(function (x) { x.setAttribute('aria-pressed', String(x === b && sel.value === o.value)); });
				});
				box.appendChild(b);
			});
		});
	}
	if (jq) {
		jq('form.variations_form').each(function () {
			var form = this, $f = jq(form);
			enhanceVariations(form);
			var buyNow = $('[data-nx-buy-now]', form);
			if (buyNow) { buyNow.disabled = true; }
			$f.on('woocommerce_update_variation_values', function () { enhanceVariations(form); });
			$f.on('show_variation', function (e, v, purchasable) { if (buyNow) { buyNow.disabled = !purchasable; } });
			$f.on('hide_variation', function () { if (buyNow) { buyNow.disabled = true; } });
			$f.on('found_variation', function (e, v) {
				setPrice(v.display_price, v.display_regular_price);
				var u = $('[data-nx-unit]'), bt = $('[data-nx-batch]');
				if (u) { u.textContent = v.nx_unit ? v.nx_unit + ' · ' : ''; }
				if (bt && typeof v.nx_batch === 'string') { bt.innerHTML = v.nx_batch; }
				$$('[data-size-label]').forEach(function (el) { if (v.nx_size) { el.textContent = v.nx_size; } });
				if (v.image && v.image.src && stageImg) { showStage(v.image.src, v.image.srcset, ''); }
			});
			$f.on('reset_data', function () {
				enhanceVariations(form);
				priceNow.forEach(function (el, i) { el.textContent = origNow[i]; });
				if (priceWas) { priceWas.outerHTML = origWasHTML; priceWas = $('.buybox [data-pdp-was]'); }
				if (priceSave) { priceSave.hidden = true; }
				if (stageOrig && stageImg) { showStage(stageOrig.src, stageOrig.srcset, ''); }
			});
		});
	}

	/* ---------- Sticky add to bag ---------- */
	var sticky = $('.sticky-atc'), mainBtn = $('.buybox form.cart .single_add_to_cart_button');
	if (sticky && mainBtn && 'IntersectionObserver' in window) {
		var sb = $('[data-nx-sticky-btn]', sticky);
		new IntersectionObserver(function (en) {
			var on = !en[0].isIntersecting && en[0].boundingClientRect.top < 0;
			sticky.classList.toggle('is-visible', on);
			sticky.setAttribute('aria-hidden', String(!on));
			sb.tabIndex = on ? 0 : -1;
			body.classList.toggle('nx-sticky-on', on);
		}).observe(mainBtn);
		sb.addEventListener('click', function () {
			var form = mainBtn.closest('form');
			if (form && (form.classList.contains('variations_form') && mainBtn.classList.contains('disabled'))) {
				form.scrollIntoView({ behavior: 'smooth', block: 'center' });
			} else {
				mainBtn.click();
			}
		});
	}

	/* ---------- Reviews: show the form ---------- */
	document.addEventListener('click', function (e) {
		var b = e.target.closest('[data-nx-review-toggle]');
		var w = $('#review_form_wrapper');
		if (!b || !w) { return; }
		w.hidden = false;
		w.scrollIntoView({ behavior: 'smooth', block: 'start' });
		var f = $('select, textarea, input', w);
		if (f) { setTimeout(function () { f.focus({ preventScroll: true }); }, 400); }
	});
	if (location.hash.indexOf('#comment') === 0 || location.hash === '#review_form') {
		var rw = $('#review_form_wrapper');
		if (rw) { rw.hidden = false; }
	}

	/* ---------- Home: category tabs over the product grid ---------- */
	var tabGrid = $('[data-tab-grid]');
	if (tabGrid) {
		var tabCards = $$('.card', tabGrid), tabNote = $('[data-tab-note]');
		var showTab = function (v) {
			$$('[data-cat-tab]').forEach(function (x) { x.setAttribute('aria-pressed', String(x.getAttribute('data-cat-tab') === v)); });
			tabCards.forEach(function (c) {
				c.hidden = !(v === 'all' ? c.hasAttribute('data-top') : c.getAttribute('data-cat') === v);
			});
			var t = $('[data-cat-tab="' + v + '"]');
			if (tabNote && t) { tabNote.textContent = t.getAttribute('data-note') || ''; }
		};
		$$('[data-cat-tab]').forEach(function (t) { t.addEventListener('click', function () { showTab(t.getAttribute('data-cat-tab')); }); });
		if (tabCards.some(function (c) { return c.hasAttribute('data-top'); })) { showTab('all'); }
	}

	/* ---------- Brand tabs ---------- */
	$$('[data-brand-tab]').forEach(function (t) {
		t.addEventListener('click', function () {
			var wrap = t.closest('.brand-tabs').parentNode;
			$$('[data-brand-tab]', wrap).forEach(function (x) { x.setAttribute('aria-pressed', String(x === t)); });
			var v = t.getAttribute('data-brand-tab');
			$$('.brand', wrap).forEach(function (b) { b.hidden = v !== 'all' && b.getAttribute('data-origin') !== v; });
		});
	});

	/* ---------- Tabs from the [nexus_product_tabs] shortcode ---------- */
	$$('[data-nx-tabs]').forEach(function (w) {
		var tabs = $$('[role=tab]', w);
		tabs.forEach(function (t, i) {
			t.addEventListener('click', function () {
				tabs.forEach(function (x) { x.setAttribute('aria-selected', String(x === t)); x.setAttribute('aria-pressed', String(x === t)); });
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
		f.className = 'video';
		a.replaceWith(f);
	});

	/* ---------- Cookie notice ---------- */
	var ck = $('[data-nx-cookie]');
	if (ck && !getCookie('nx_cookies')) { ck.hidden = false; }
	$$('[data-nx-cookie-choice]').forEach(function (b) {
		b.addEventListener('click', function () {
			setCookie('nx_cookies', b.getAttribute('data-nx-cookie-choice'), 180);
			ck.hidden = true;
			document.dispatchEvent(new CustomEvent('nexus:cookies', { detail: b.getAttribute('data-nx-cookie-choice') }));
		});
	});
})();
