/* Sensula storefront — progressive enhancement only.
   All content is in the HTML for SEO; this script adds cart, filters and gallery. */
(function () {
  "use strict";

  /* Pages can override these with window.STORE = { currency, freeShip, decimals } */
  var CFG = window.STORE || {};
  var CURRENCY = CFG.currency || "$";          // e.g. "Rs ", "AED ", "£"
  var FREE_SHIP = CFG.freeShip || 50;          // free-shipping threshold, same currency
  var DECIMALS = CFG.decimals == null ? 2 : CFG.decimals;
  var STORE_KEY = "sensula-cart" + (CURRENCY === "$" ? "" : "-" + CURRENCY.trim().toLowerCase());

  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var money = function (n) {
    return CURRENCY + n.toLocaleString("en-US", { minimumFractionDigits: DECIMALS, maximumFractionDigits: DECIMALS });
  };

  /* ---------- storage (guarded) ---------- */
  function load() {
    try { return JSON.parse(localStorage.getItem(STORE_KEY)) || []; } catch (e) { return []; }
  }
  function save(c) {
    try { localStorage.setItem(STORE_KEY, JSON.stringify(c)); } catch (e) { /* storage blocked */ }
  }
  var cart = load();

  /* ---------- toast ---------- */
  var toastEl = $(".toast"), toastT;
  function toast(msg) {
    if (!toastEl) return;
    toastEl.textContent = msg;
    toastEl.classList.add("is-on");
    clearTimeout(toastT);
    toastT = setTimeout(function () { toastEl.classList.remove("is-on"); }, 2200);
  }

  /* ---------- overlays ---------- */
  var scrim = $(".scrim");
  function openPanel(el) { el.classList.add("is-open"); scrim.classList.add("is-open"); el.setAttribute("aria-hidden", "false"); }
  function closeAll() {
    $$(".drawer, .mnav, .filters, .searchpanel").forEach(function (el) { el.classList.remove("is-open"); });
    $$(".drawer, .mnav, .searchpanel").forEach(function (el) { el.setAttribute("aria-hidden", "true"); });
    scrim.classList.remove("is-open");
  }
  scrim && scrim.addEventListener("click", closeAll);
  document.addEventListener("keydown", function (e) { if (e.key === "Escape") closeAll(); });
  $$("[data-close]").forEach(function (b) { b.addEventListener("click", closeAll); });
  $$("[data-open-cart]").forEach(function (b) { b.addEventListener("click", function () { openPanel($(".drawer")); }); });
  $$("[data-open-menu]").forEach(function (b) { b.addEventListener("click", function () { openPanel($(".mnav")); }); });
  $$("[data-open-search]").forEach(function (b) {
    b.addEventListener("click", function () {
      var sp = $(".searchpanel"); if (!sp) return;
      openPanel(sp);
      var i = $("input", sp); if (i) setTimeout(function () { i.focus(); }, 60);
    });
  });
  $$("[data-open-filters]").forEach(function (b) { b.addEventListener("click", function () { openPanel($(".filters")); }); });

  /* ---------- cart ---------- */
  function subtotal() { return cart.reduce(function (s, l) { return s + l.price * l.qty; }, 0); }
  function count() { return cart.reduce(function (s, l) { return s + l.qty; }, 0); }

  function shipMsg(el) {
    var st = subtotal(), left = Math.max(0, FREE_SHIP - st);
    var txt = el.querySelector("[data-ship-text]"), fill = el.querySelector(".ship-bar__fill");
    if (txt) txt.innerHTML = left > 0
      ? "You're <b>" + money(left) + "</b> away from <b>free delivery</b>"
      : "<b>You've unlocked free delivery.</b>";
    if (fill) fill.style.width = Math.min(100, (st / FREE_SHIP) * 100) + "%";
  }

  function render() {
    $$("[data-cart-count]").forEach(function (b) { b.textContent = count(); b.hidden = count() === 0; });
    $$(".ship-bar").forEach(shipMsg);
    var list = $(".drawer__items"); if (!list) return;
    if (!cart.length) {
      list.innerHTML = '<div class="drawer__empty"><p>Your bag is empty.</p><a class="btn btn--dark" href="shop.html">Shop bestsellers</a></div>';
    } else {
      list.innerHTML = cart.map(function (l, i) {
        return '<div class="line"><div class="line__img" style="--tone:' + l.tone + ';--bg:' + l.bg + '"><svg aria-hidden="true"><use href="#i-' + l.shape + '"/></svg></div>' +
          '<div><b>' + l.name + '</b><small>' + l.size + '</small><div class="line__qty">' +
          '<button type="button" data-q="' + i + '" data-d="-1" aria-label="Decrease quantity">−</button><span>' + l.qty + '</span>' +
          '<button type="button" data-q="' + i + '" data-d="1" aria-label="Increase quantity">+</button></div></div>' +
          '<span class="line__price">' + money(l.price * l.qty) + '</span></div>';
      }).join("");
    }
    var tot = $("[data-cart-total]"); if (tot) tot.textContent = money(subtotal());
  }

  function add(d, qty) {
    var key = d.id + "|" + d.size;
    var found = cart.filter(function (l) { return l.key === key; })[0];
    if (found) found.qty += qty;
    else cart.push({ key: key, id: d.id, name: d.name, size: d.size, price: parseFloat(d.price), shape: d.shape, tone: d.tone, bg: d.bg, qty: qty });
    save(cart); render();
  }

  document.addEventListener("click", function (e) {
    var q = e.target.closest("[data-q]");
    if (q) {
      var l = cart[+q.dataset.q]; l.qty += +q.dataset.d;
      if (l.qty <= 0) cart.splice(+q.dataset.q, 1);
      save(cart); render(); return;
    }
    var a = e.target.closest("[data-add]");
    if (a) {
      e.preventDefault();
      var qtyIn = $("#qty");
      var n = a.hasAttribute("data-use-qty") && qtyIn ? Math.max(1, parseInt(qtyIn.value, 10) || 1) : 1;
      if (a.dataset.bundle) {
        $$("[data-bundle-item]").forEach(function (b) { add(b.dataset, 1); });
        toast("Bundle added to your bag");
      } else {
        add(a.dataset, n);
        toast(a.dataset.name + " added to your bag");
      }
      if (a.hasAttribute("data-buy-now")) location.href = "#checkout";
      openPanel($(".drawer"));
      return;
    }
    var w = e.target.closest(".card__wish");
    if (w) {
      var on = w.getAttribute("aria-pressed") !== "true";
      w.setAttribute("aria-pressed", on);
      toast(on ? "Saved to your wishlist" : "Removed from your wishlist");
    }
  });

  var checkout = $("[data-checkout]");
  checkout && checkout.addEventListener("click", function () { toast("Connect your checkout (Shopify, WooCommerce, Stripe) here"); });

  /* ---------- forms (no real submission in this template) ---------- */
  $$("form[data-demo]").forEach(function (f) {
    f.addEventListener("submit", function (e) {
      e.preventDefault();
      if (f.dataset.demo === "search") { location.href = "shop.html"; return; }
      toast(f.dataset.demo === "news" ? "You're in. Check your inbox for 10% off." : "Thanks!");
      f.reset();
    });
  });

  /* ---------- brand tabs (home) ---------- */
  $$("[data-brand-tab]").forEach(function (t) {
    t.addEventListener("click", function () {
      $$("[data-brand-tab]").forEach(function (x) { x.setAttribute("aria-pressed", x === t); });
      var v = t.dataset.brandTab;
      $$(".brand").forEach(function (b) { b.hidden = v !== "all" && b.dataset.origin !== v; });
    });
  });

  /* ---------- category tabs (home) ---------- */
  var tabGrid = $("[data-tab-grid]");
  if (tabGrid) {
    var tabCards = $$(".card", tabGrid), tabNote = $("[data-tab-note]");
    function showTab(v) {
      $$("[data-cat-tab]").forEach(function (x) { x.setAttribute("aria-pressed", x.dataset.catTab === v); });
      var n = 0;
      tabCards.forEach(function (c) {
        var ok = v === "all" ? c.hasAttribute("data-top") : c.dataset.cat === v;
        c.hidden = !ok; if (ok) n++;
      });
      if (tabNote) {
        var t = $('[data-cat-tab="' + v + '"]');
        tabNote.textContent = v === "all" ? "The eight products Pakistan buys most, one or two from each category." : (t ? t.dataset.note || "" : "");
      }
    }
    $$("[data-cat-tab]").forEach(function (t) { t.addEventListener("click", function () { showTab(t.dataset.catTab); }); });
    showTab("all");
  }

  /* ---------- shop filters ---------- */
  var grid = $("[data-product-grid]");
  if (grid) {
    var cards = $$(".card", grid);
    var sortSel = $("#sort"), priceIn = $("#price-max"), priceOut = $("#price-out");
    var countEl = $("[data-result-count]"), chips = $(".active-filters"), empty = $(".empty");
    cards.forEach(function (c, i) { c.dataset.i = i; });

    function apply() {
      var sel = {};
      $$(".filters input[type=checkbox]:checked").forEach(function (i) { (sel[i.name] = sel[i.name] || []).push(i.value); });
      var max = priceIn ? +priceIn.value : Infinity;
      if (priceOut) priceOut.textContent = money(max);
      var shown = 0;
      cards.forEach(function (c) {
        var ok = +c.dataset.price <= max;
        Object.keys(sel).forEach(function (k) {
          var vals = (c.dataset[k] || "").split(" ");
          if (!sel[k].some(function (v) { return vals.indexOf(v) > -1; })) ok = false;
        });
        c.hidden = !ok; if (ok) shown++;
      });
      if (countEl) countEl.textContent = shown;
      if (empty) empty.hidden = shown > 0;
      if (chips) chips.innerHTML = $$(".filters input[type=checkbox]:checked").map(function (i) {
        return '<button type="button" data-unfilter="' + i.id + '">' + i.dataset.label + ' ×</button>';
      }).join("");
      var s = sortSel ? sortSel.value : "featured";
      cards.slice().sort(function (a, b) {
        if (s === "price-asc") return a.dataset.price - b.dataset.price;
        if (s === "price-desc") return b.dataset.price - a.dataset.price;
        if (s === "rating") return b.dataset.rating - a.dataset.rating;
        return a.dataset.i - b.dataset.i;
      }).forEach(function (c) { grid.appendChild(c); });
      var banner = $(".inline-banner", grid);
      if (banner) { var visible = cards.filter(function (c) { return !c.hidden; }); var ref = visible[6]; if (ref && s === "featured") grid.insertBefore(banner, ref); else grid.appendChild(banner); }
    }
    $$(".filters input").forEach(function (i) { i.addEventListener("input", apply); });
    sortSel && sortSel.addEventListener("change", apply);
    chips && chips.addEventListener("click", function (e) {
      var b = e.target.closest("[data-unfilter]"); if (!b) return;
      $("#" + b.dataset.unfilter).checked = false; apply();
    });
    $$("[data-clear-filters]").forEach(function (b) {
      b.addEventListener("click", function () {
        $$(".filters input[type=checkbox]").forEach(function (i) { i.checked = false; });
        if (priceIn) priceIn.value = priceIn.max; apply();
      });
    });
    apply();
  }

  /* ---------- product page ---------- */
  var stage = $(".stage");
  $$(".thumbs button").forEach(function (b) {
    b.addEventListener("click", function () {
      $$(".thumbs button").forEach(function (x) { x.setAttribute("aria-current", x === b); });
      stage.dataset.view = b.dataset.view;
      var note = $(".stage__note"); if (note) note.textContent = b.getAttribute("aria-label");
    });
  });

  var mainAdd = $("#add-main");
  $$(".sizes button").forEach(function (b) {
    b.addEventListener("click", function () {
      $$(".sizes button").forEach(function (x) { x.setAttribute("aria-pressed", x === b); });
      var p = +b.dataset.price, was = +b.dataset.was || p;
      $$("[data-pdp-price]").forEach(function (el) { el.textContent = money(p); });
      $$("[data-pdp-was]").forEach(function (el) { el.textContent = money(was); el.hidden = was <= p; });
      $$("[data-pdp-save]").forEach(function (el) { el.textContent = "Save " + Math.round((1 - p / was) * 100) + "%"; el.hidden = was <= p; });
      $$("[data-size-label], [data-size-label-sticky]").forEach(function (el) { el.textContent = b.dataset.size; });
      $$("[data-add][data-use-qty]").forEach(function (a) { a.dataset.price = p; a.dataset.size = b.dataset.size; });
    });
  });
  $$("[data-step]").forEach(function (b) {
    b.addEventListener("click", function () {
      var i = $("#qty"); i.value = Math.min(10, Math.max(1, (+i.value || 1) + +b.dataset.step));
    });
  });

  /* ---------- scent picker (product page) ---------- */
  $$("[data-scent]").forEach(function (b) {
    b.addEventListener("click", function () {
      $$("[data-scent]").forEach(function (x) { x.setAttribute("aria-pressed", x === b); });
      var g = $(".gallery");
      if (g) { g.style.setProperty("--tone", b.dataset.tone); g.style.setProperty("--bg", b.dataset.bg); }
      $$("[data-scent-label]").forEach(function (el) { el.textContent = b.dataset.scent; });
      $$("[data-scent-notes]").forEach(function (el) { el.hidden = el.dataset.scentNotes !== b.dataset.scent; });
      $$("[data-add][data-use-qty]").forEach(function (a) {
        a.dataset.name = "Koh-e-Noor " + b.dataset.scent + " Hair & Body Mist";
        a.dataset.id = b.dataset.id; a.dataset.tone = b.dataset.tone; a.dataset.bg = b.dataset.bg;
      });
    });
  });

  var sticky = $(".sticky-atc");
  if (sticky && mainAdd && "IntersectionObserver" in window) {
    new IntersectionObserver(function (en) {
      sticky.classList.toggle("is-visible", !en[0].isIntersecting && en[0].boundingClientRect.top < 0);
    }).observe(mainAdd);
  }

  render();
})();
