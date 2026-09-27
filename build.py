#!/usr/bin/env python3
"""Generates the static storefront pages from one product catalog.

Edit PRODUCTS / SITE below, then run:  python3 build.py
Outputs: index.html, shop.html, product.html, sitemap.xml, robots.txt, llms.txt
"""
import json
from html import escape

SITE = {
    "name": "Sensula",
    "url": "https://www.sensula.com",          # replace with your domain
    "currency_symbol": "$",                     # keep in sync with CURRENCY in assets/js/main.js
    "currency_code": "USD",
    "free_ship": 50,
    "phone": "+1 555 010 2030",
    "email": "care@sensula.com",
}
CUR = SITE["currency_symbol"]

# id, name, brand, origin(local|intl), category, concerns, shape, tone, bg, size, price, was, rating, reviews, badge
PRODUCTS = [
    ("hyaluronic-dew-serum", "Hyaluronic Dew Serum", "Hanok Skin", "intl", "skincare", "dryness dullness", "dropper", "#7fa7b8", "#e9f1f4", "30 ml", 24.00, 30.00, 4.8, 312, "Bestseller"),
    ("vitamin-c-glow-serum", "15% Vitamin C Glow Serum", "Lumière Lab", "intl", "skincare", "dullness pigmentation", "dropper", "#e2a33b", "#fbf1df", "30 ml", 28.00, None, 4.7, 198, "New"),
    ("rose-oud-eau-de-parfum", "Rose & Oud Eau de Parfum", "Sensula Atelier", "local", "fragrance", "", "perfume", "#b04a6b", "#f7e5eb", "50 ml", 45.00, 58.00, 4.9, 421, "Bestseller"),
    ("argan-repair-hair-oil", "Argan Repair Hair Oil", "Veda Roots", "local", "haircare", "hairfall frizz", "bottle", "#c68a3a", "#f8eedf", "100 ml", 14.00, None, 4.6, 156, ""),
    ("gentle-foaming-cleanser", "Gentle Foaming Cleanser", "Hanok Skin", "intl", "skincare", "acne sensitive", "pump", "#9bb79e", "#eaf2eb", "150 ml", 16.00, 19.00, 4.7, 264, ""),
    ("matte-velvet-lipstick", "Matte Velvet Lipstick — Berry Nude", "Maison Rouge", "intl", "makeup", "", "lipstick", "#9e2f4a", "#f6e2e7", "3.5 g", 18.00, None, 4.5, 89, "New"),
    ("shea-body-butter", "Whipped Shea Body Butter", "Veda Roots", "local", "bodycare", "dryness", "jar", "#d9b48f", "#f7efe6", "200 g", 12.00, 15.00, 4.8, 230, ""),
    ("spf50-sun-fluid", "Invisible SPF 50 PA++++ Sun Fluid", "Lumière Lab", "intl", "skincare", "pigmentation sensitive", "tube", "#f0c24b", "#fdf5dc", "50 ml", 22.00, None, 4.7, 177, "Bestseller"),
    ("charcoal-beard-wash", "Charcoal Beard & Face Wash", "Northman Co.", "local", "men", "acne", "tube", "#3c3f44", "#e8e9eb", "100 ml", 11.00, None, 4.6, 74, ""),
    ("aloe-hand-wash", "Aloe & Cucumber Hand Wash", "Pure Nest", "local", "hygiene", "sensitive", "pump", "#79b089", "#e6f2e9", "500 ml", 6.00, 7.50, 4.5, 118, ""),
    ("ceramide-night-cream", "Ceramide Barrier Night Cream", "Hanok Skin", "intl", "skincare", "dryness sensitive", "jar", "#b3a0c9", "#f0ebf6", "50 ml", 32.00, None, 4.8, 143, ""),
    ("keratin-smooth-shampoo", "Keratin Smooth Shampoo", "Veda Roots", "local", "haircare", "frizz", "bottle", "#8f6fb0", "#efe8f6", "300 ml", 13.00, 16.00, 4.6, 201, ""),
]
KEYS = ["id", "name", "brand", "origin", "cat", "concern", "shape", "tone", "bg", "size", "price", "was", "rating", "reviews", "badge"]
PRODUCTS = [dict(zip(KEYS, p)) for p in PRODUCTS]

CATS = [
    ("skincare", "Skincare", "Serums, cleansers, SPF", "dropper", "#7fa7b8", "#e9f1f4"),
    ("makeup", "Makeup", "Lips, face, eyes", "lipstick", "#9e2f4a", "#f6e2e7"),
    ("haircare", "Haircare", "Oils, shampoos, masks", "bottle", "#c68a3a", "#f8eedf"),
    ("bodycare", "Bath & Body", "Butters, washes, scrubs", "jar", "#d9b48f", "#f7efe6"),
    ("fragrance", "Fragrance", "Perfume, attar, mists", "perfume", "#b04a6b", "#f7e5eb"),
    ("men", "Men's Grooming", "Beard, shave, face", "tube", "#3c3f44", "#e8e9eb"),
]
CAT_NAME = {c[0]: c[1] for c in CATS}
CAT_NAME["hygiene"] = "Personal Hygiene"

BRANDS = [
    ("Hanok Skin", "intl", "Korea"), ("Lumière Lab", "intl", "France"), ("Maison Rouge", "intl", "Italy"),
    ("Sensula Atelier", "local", "Our own label"), ("Veda Roots", "local", "Local · Ayurvedic"),
    ("Northman Co.", "local", "Local · Men"), ("Pure Nest", "local", "Local · Hygiene"),
    ("Aqua Bloom", "intl", "Japan"), ("Nordic Pure", "intl", "Sweden"), ("Terra Botanica", "intl", "UK"),
    ("Desert Rose", "local", "Local · Fragrance"), ("Glow Theory", "intl", "USA"),
]

CONCERNS = [("acne", "Acne & breakouts"), ("dryness", "Dryness"), ("dullness", "Dullness"),
            ("pigmentation", "Dark spots"), ("sensitive", "Sensitive skin"), ("hairfall", "Hair fall"), ("frizz", "Frizz")]


def m(v):
    return f"{CUR}{v:.2f}"


# ---------------------------------------------------------------- SVG sprite
SPRITE = """<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
<defs>
 <symbol id="i-dropper" viewBox="0 0 100 160"><path d="M42 30V14c0-10 16-10 16 0v16z" fill="#2b1f2c"/><rect x="36" y="30" width="28" height="14" rx="2" fill="#c9a86a"/><rect x="24" y="44" width="52" height="108" rx="12" fill="currentColor"/><rect x="31" y="84" width="38" height="40" rx="3" fill="#fff" opacity=".85"/><rect x="36" y="94" width="28" height="3" fill="#2b1f2c" opacity=".55"/><rect x="40" y="102" width="20" height="2" fill="#2b1f2c" opacity=".35"/><rect x="29" y="52" width="5" height="90" rx="2.5" fill="#fff" opacity=".28"/></symbol>
 <symbol id="i-perfume" viewBox="0 0 100 160"><rect x="37" y="8" width="26" height="28" rx="3" fill="#2b1f2c"/><rect x="43" y="36" width="14" height="10" fill="#c9a86a"/><rect x="14" y="46" width="72" height="104" rx="10" fill="currentColor"/><rect x="22" y="54" width="56" height="88" rx="6" fill="#fff" opacity=".16"/><rect x="30" y="86" width="40" height="26" rx="2" fill="#fff" opacity=".88"/><rect x="36" y="95" width="28" height="3" fill="#2b1f2c" opacity=".55"/><rect x="40" y="102" width="20" height="2" fill="#2b1f2c" opacity=".35"/></symbol>
 <symbol id="i-bottle" viewBox="0 0 100 160"><rect x="40" y="6" width="20" height="22" rx="3" fill="#2b1f2c"/><rect x="43" y="28" width="14" height="6" fill="#2b1f2c" opacity=".8"/><path d="M28 50c0-12 8-16 22-16s22 4 22 16v92c0 6-4 10-10 10H38c-6 0-10-4-10-10z" fill="currentColor"/><rect x="34" y="78" width="32" height="46" rx="3" fill="#fff" opacity=".85"/><rect x="39" y="90" width="22" height="3" fill="#2b1f2c" opacity=".55"/><rect x="42" y="98" width="16" height="2" fill="#2b1f2c" opacity=".35"/><rect x="32" y="52" width="4" height="88" rx="2" fill="#fff" opacity=".25"/></symbol>
 <symbol id="i-pump" viewBox="0 0 100 160"><path d="M46 8h28v8H56v12h-10z" fill="#2b1f2c"/><rect x="42" y="28" width="16" height="16" rx="2" fill="#2b1f2c"/><rect x="22" y="44" width="56" height="108" rx="14" fill="currentColor"/><rect x="30" y="80" width="40" height="44" rx="3" fill="#fff" opacity=".85"/><rect x="36" y="92" width="28" height="3" fill="#2b1f2c" opacity=".55"/><rect x="40" y="100" width="20" height="2" fill="#2b1f2c" opacity=".35"/><rect x="28" y="54" width="4" height="88" rx="2" fill="#fff" opacity=".25"/></symbol>
 <symbol id="i-lipstick" viewBox="0 0 100 160"><path d="M40 60V28l20-14v46z" fill="currentColor"/><rect x="36" y="60" width="28" height="28" rx="2" fill="#c9a86a"/><rect x="31" y="88" width="38" height="64" rx="4" fill="#2b1f2c"/><rect x="36" y="94" width="4" height="52" rx="2" fill="#fff" opacity=".2"/></symbol>
 <symbol id="i-jar" viewBox="0 0 100 160"><rect x="12" y="62" width="76" height="26" rx="6" fill="#2b1f2c"/><rect x="16" y="88" width="68" height="60" rx="12" fill="currentColor"/><rect x="28" y="100" width="44" height="30" rx="3" fill="#fff" opacity=".85"/><rect x="34" y="110" width="32" height="3" fill="#2b1f2c" opacity=".55"/><rect x="38" y="118" width="24" height="2" fill="#2b1f2c" opacity=".35"/></symbol>
 <symbol id="i-tube" viewBox="0 0 100 160"><rect x="24" y="6" width="52" height="8" rx="2" fill="currentColor" opacity=".75"/><path d="M26 14h48l-8 112H34z" fill="currentColor"/><rect x="36" y="46" width="28" height="44" rx="3" fill="#fff" opacity=".85"/><rect x="40" y="58" width="20" height="3" fill="#2b1f2c" opacity=".55"/><rect x="42" y="66" width="16" height="2" fill="#2b1f2c" opacity=".35"/><rect x="36" y="126" width="28" height="26" rx="3" fill="#2b1f2c"/></symbol>
 <symbol id="ic-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></symbol>
 <symbol id="ic-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/></symbol>
 <symbol id="ic-heart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"><path d="M12 20s-7.5-4.6-9-9.3C2 7.4 4.2 4.5 7.4 4.5c2 0 3.4 1.1 4.6 2.6 1.2-1.5 2.6-2.6 4.6-2.6 3.2 0 5.4 2.9 4.4 6.2-1.5 4.7-9 9.3-9 9.3z"/></symbol>
 <symbol id="ic-bag" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"><path d="M5 8h14l-1 12H6z"/><path d="M9 8V6a3 3 0 016 0v2"/></symbol>
 <symbol id="ic-menu" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></symbol>
 <symbol id="ic-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></symbol>
 <symbol id="ic-truck" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M2 6h12v10H2zM14 10h4l3 3v3h-7z"/><circle cx="6" cy="18" r="2" fill="#fff"/><circle cx="17" cy="18" r="2" fill="#fff"/></symbol>
 <symbol id="ic-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="M8.5 12l2.5 2.5 4.5-5" stroke-linecap="round"/></symbol>
 <symbol id="ic-return" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9h11a5 5 0 010 10H9"/><path d="M8 5L4 9l4 4"/></symbol>
 <symbol id="ic-cash" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="2.5" y="6" width="19" height="12" rx="2"/><circle cx="12" cy="12" r="2.6"/></symbol>
 <symbol id="ic-leaf" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M5 19C5 10 10 5 20 4c0 10-5 15-14 15z"/><path d="M5 19l8-8"/></symbol>
 <symbol id="ic-chat" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M4 5h16v11H9l-5 4z"/></symbol>
 <symbol id="ic-clock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
 <symbol id="ic-filter" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"/></symbol>
</defs></svg>"""


def ic(name, cls=""):
    c = f' class="{cls}"' if cls else ""
    return f'<svg{c} aria-hidden="true"><use href="#ic-{name}"/></svg>'


def art(shape):
    return f'<svg aria-hidden="true" viewBox="0 0 100 160"><use href="#i-{shape}"/></svg>'


def stars(r, n, show_n=True):
    pct = r / 5 * 100
    tail = f' <span>{r} ({n})</span>' if show_n else ""
    return f'<span class="rating"><span class="stars" style="--pct:{pct:.0f}%" aria-hidden="true"></span><span class="sr-only">Rated {r} out of 5 from {n} reviews</span>{tail}</span>'


def add_attrs(p, size=None, price=None):
    return (f'data-add data-id="{p["id"]}" data-name="{escape(p["name"])}" data-price="{price or p["price"]}" '
            f'data-size="{size or p["size"]}" data-shape="{p["shape"]}" data-tone="{p["tone"]}" data-bg="{p["bg"]}"')


def card(p, lazy=False):
    badges = []
    if p["was"]:
        badges.append(f'<span class="pill pill--sale">−{round((1 - p["price"] / p["was"]) * 100)}%</span>')
    if p["badge"] == "New":
        badges.append('<span class="pill pill--new">New</span>')
    elif p["badge"]:
        badges.append(f'<span class="pill">{p["badge"]}</span>')
    badges.append('<span class="pill pill--local">Local brand</span>' if p["origin"] == "local" else '<span class="pill pill--intl">Imported</span>')
    was = f'<span class="price__was"><span class="sr-only">Was </span>{m(p["was"])}</span>' if p["was"] else ""
    return f'''<article class="card" data-cat="{p["cat"]}" data-brand="{p["brand"].lower().replace(" ", "-").replace(".", "")}" data-origin="{p["origin"]}" data-concern="{p["concern"]}" data-price="{p["price"]}" data-rating="{p["rating"]}">
  <div class="card__media" style="--tone:{p["tone"]};--bg:{p["bg"]}">
    {art(p["shape"])}
    <div class="card__badges">{"".join(badges)}</div>
  </div>
  <button class="card__wish" type="button" aria-pressed="false" aria-label="Save {escape(p["name"])} to wishlist">{ic("heart")}</button>
  <div class="card__quick"><button class="btn btn--dark btn--block" type="button" {add_attrs(p)}>Add to bag</button></div>
  <p class="card__brand">{escape(p["brand"])}</p>
  <h3 class="card__title"><a href="product.html">{escape(p["name"])}</a></h3>
  <div class="card__meta">{stars(p["rating"], p["reviews"])}<span class="card__size">{p["size"]}</span></div>
  <p class="price"><span class="price__now">{m(p["price"])}</span>{was}</p>
</article>'''


# ---------------------------------------------------------------- shared chrome
NAV = [("shop.html", "Skincare"), ("shop.html", "Makeup"), ("shop.html", "Haircare"), ("shop.html", "Bath & Body"),
       ("shop.html", "Fragrance"), ("shop.html", "Men"), ("shop.html", "Personal Hygiene"), ("index.html#brands", "Brands"),
       ("shop.html", "New In"), ("shop.html", "Offers")]


def head(title, desc, canonical, jsonld, og_type="website"):
    ld = "\n".join(f'<script type="application/ld+json">{json.dumps(j, ensure_ascii=False)}</script>' for j in jsonld)
    return f'''<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>{escape(title)}</title>
<meta name="description" content="{escape(desc)}">
<link rel="canonical" href="{SITE["url"]}/{canonical}">
<meta name="robots" content="index, follow, max-image-preview:large">
<meta name="theme-color" content="#8c2350">
<meta property="og:type" content="{og_type}">
<meta property="og:site_name" content="{SITE["name"]}">
<meta property="og:title" content="{escape(title)}">
<meta property="og:description" content="{escape(desc)}">
<meta property="og:url" content="{SITE["url"]}/{canonical}">
<meta property="og:image" content="{SITE["url"]}/assets/img/og-cover.jpg">
<meta name="twitter:card" content="summary_large_image">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,500;0,6..96,600;1,6..96,500&family=Figtree:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="assets/css/styles.css">
{ld}
</head>
<body>
<a class="skip" href="#main">Skip to content</a>
{SPRITE}'''


def header():
    sale = ' class="is-sale"'
    nav = "".join(f'<li><a href="{h}"{sale if t == "Offers" else ""}>{t}</a></li>' for h, t in NAV)
    return f'''
<div class="announce"><strong>Free delivery over {m(SITE["free_ship"])}</strong> · Cash on delivery available · 100% authentic, sourced from brands &amp; authorised distributors</div>
<header class="header">
  <div class="wrap header__bar">
    <div style="display:flex;align-items:center;gap:4px">
      <button class="icon-btn menu-btn" type="button" data-open-menu aria-label="Open menu">{ic("menu")}</button>
      <a class="logo" href="index.html" aria-label="{SITE["name"]} home">Sensula<span>.</span></a>
    </div>
    <form class="search" role="search" data-demo="search" action="shop.html">
      <label class="sr-only" for="q">Search products, brands and concerns</label>
      {ic("search")}
      <input id="q" name="q" type="search" placeholder="Search serums, SPF, perfume, brands…" autocomplete="off">
    </form>
    <div class="actions">
      <a class="icon-btn hide-sm" href="#account" aria-label="Your account">{ic("user")}</a>
      <a class="icon-btn hide-sm" href="#wishlist" aria-label="Wishlist">{ic("heart")}</a>
      <button class="icon-btn" type="button" data-open-cart aria-label="Open bag">{ic("bag")}<span class="badge-count" data-cart-count hidden>0</span></button>
    </div>
  </div>
  <nav class="nav" aria-label="Main"><div class="wrap"><ul>{nav}</ul></div></nav>
</header>
<nav class="mnav" aria-label="Mobile" aria-hidden="true">
  <div style="display:flex;justify-content:space-between;align-items:center"><span class="logo">Sensula<span>.</span></span><button class="icon-btn" type="button" data-close aria-label="Close menu">{ic("close")}</button></div>
  <ul>{nav}<li><a href="#account">My account</a></li><li><a href="#track">Track my order</a></li></ul>
</nav>'''


def trust():
    items = [("truck", "Free delivery", f"On orders over {m(SITE['free_ship'])}"), ("shield", "100% authentic", "Direct from brands"),
             ("cash", "Cash on delivery", "Pay when it arrives"), ("return", "Easy 14-day returns", "Unopened items")]
    li = "".join(f"<li>{ic(i)}<div><strong>{a}</strong><span>{b}</span></div></li>" for i, a, b in items)
    return f'<section class="trust" aria-label="Why shop with us"><ul class="wrap">{li}</ul></section>'


def cart_drawer():
    return f'''
<div class="scrim"></div>
<aside class="drawer" aria-label="Shopping bag" aria-hidden="true">
  <div class="drawer__head"><h2>Your bag</h2><button class="icon-btn" type="button" data-close aria-label="Close bag">{ic("close")}</button></div>
  <div class="ship-bar"><span data-ship-text>Free delivery over {m(SITE["free_ship"])}</span><div class="ship-bar__track"><div class="ship-bar__fill"></div></div></div>
  <div class="drawer__items"></div>
  <div class="drawer__foot">
    <div class="drawer__total"><span>Subtotal</span><span data-cart-total>{CUR}0.00</span></div>
    <button class="btn btn--primary btn--lg btn--block" type="button" data-checkout>Checkout securely</button>
    <small>Taxes included. Cash on delivery, cards and wallets accepted.</small>
  </div>
</aside>
<div class="toast" role="status" aria-live="polite"></div>'''


def footer():
    cols = {
        "Shop": ["Skincare", "Makeup", "Haircare", "Bath & Body", "Fragrance", "Men's Grooming"],
        "Brands": ["International brands", "Local brands", "Korean beauty", "Sensula Atelier", "All brands A–Z"],
        "Help": ["Track my order", "Delivery information", "Returns & exchanges", "Authenticity promise", "Contact us"],
        "Company": ["About Sensula", "Beauty journal", "Become a brand partner", "Wholesale", "Privacy policy"],
    }
    def links(v):
        return "".join('<li><a href="shop.html">' + escape(x) + '</a></li>' for x in v)
    colhtml = "".join(f'<div><h3>{k}</h3><ul>{links(v)}</ul></div>' for k, v in cols.items())
    return f'''
<footer class="footer">
  <div class="wrap">
    <div class="footer__grid">
      <div class="footer__about"><span class="logo">Sensula<span>.</span></span>
        <p>A multi-brand beauty and personal care store. Local favourites and international labels, checked for authenticity and shipped fast.</p>
        <p>Customer care: <b>{SITE["phone"]}</b><br>{SITE["email"]} · 9am–9pm daily</p>
      </div>
      {colhtml}
    </div>
    <div class="footer__bottom">
      <span>© 2026 {SITE["name"]}. All rights reserved.</span>
      <div class="pay" aria-label="Payment methods"><span>VISA</span><span>MASTERCARD</span><span>APPLE PAY</span><span>GOOGLE PAY</span><span>COD</span></div>
    </div>
  </div>
</footer>
<script src="assets/js/main.js" defer></script>
</body>
</html>'''


ORG_LD = {
    "@context": "https://schema.org", "@type": "Organization", "name": SITE["name"], "url": SITE["url"],
    "logo": f'{SITE["url"]}/assets/img/logo.png',
    "contactPoint": {"@type": "ContactPoint", "telephone": SITE["phone"], "email": SITE["email"], "contactType": "customer service"},
    "sameAs": ["https://www.instagram.com/sensula", "https://www.facebook.com/sensula", "https://www.tiktok.com/@sensula"],
}


def faq_ld(faqs):
    return {"@context": "https://schema.org", "@type": "FAQPage",
            "mainEntity": [{"@type": "Question", "name": q, "acceptedAnswer": {"@type": "Answer", "text": a}} for q, a in faqs]}


def faq_html(faqs, open_first=True):
    return "".join(f'<details{" open" if (i == 0 and open_first) else ""}><summary>{escape(q)}</summary><div><p>{escape(a)}</p></div></details>' for i, (q, a) in enumerate(faqs))


def crumbs_ld(items):
    return {"@context": "https://schema.org", "@type": "BreadcrumbList",
            "itemListElement": [{"@type": "ListItem", "position": i + 1, "name": n, "item": f'{SITE["url"]}/{u}'} for i, (n, u) in enumerate(items)]}


def crumbs_html(items):
    li = "".join(f'<li><a href="{u}">{escape(n)}</a></li>' if i < len(items) - 1 else f'<li aria-current="page">{escape(n)}</li>' for i, (n, u) in enumerate(items))
    return f'<nav class="crumbs wrap" aria-label="Breadcrumb"><ol>{li}</ol></nav>'


# ================================================================= HOME
HOME_FAQ = [
    ("Are the products on Sensula original?", "Yes. Every product is bought directly from the brand or its authorised distributor, and each order ships with the batch number and expiry date printed on the pack. If anything looks wrong, we refund you in full."),
    ("Do you sell both local and international brands?", "Yes. Sensula stocks over 120 brands: international labels from Korea, France, Japan, the UK and the USA, alongside trusted local brands and our own Sensula Atelier fragrances."),
    ("How long does delivery take?", f"Orders placed before 3pm ship the same day. Major cities receive them in 1–2 working days and other areas in 3–5 working days. Delivery is free on orders over {m(SITE['free_ship'])}."),
    ("Can I pay cash on delivery?", "Yes. Cash on delivery is available on all orders, along with debit and credit cards, Apple Pay and Google Pay."),
    ("What is your return policy?", "Unopened products can be returned within 14 days of delivery for a refund or exchange. Damaged or wrong items are replaced free of charge."),
]


def build_home():
    best = [p for p in PRODUCTS if p["id"] in ("hyaluronic-dew-serum", "rose-oud-eau-de-parfum", "spf50-sun-fluid", "shea-body-butter", "gentle-foaming-cleanser", "argan-repair-hair-oil", "vitamin-c-glow-serum", "ceramide-night-cream")]
    new = [p for p in PRODUCTS if p["id"] in ("matte-velvet-lipstick", "charcoal-beard-wash", "keratin-smooth-shampoo", "aloe-hand-wash")]
    ld = [ORG_LD,
          {"@context": "https://schema.org", "@type": "WebSite", "name": SITE["name"], "url": SITE["url"],
           "potentialAction": {"@type": "SearchAction", "target": f'{SITE["url"]}/shop.html?q={{search_term_string}}', "query-input": "required name=search_term_string"}},
          faq_ld(HOME_FAQ)]
    cats = "".join(f'<a class="cat" href="shop.html"><span class="cat__img" style="--tone:{t};--tile:{b}">{art(s)}</span><span>{n}<small>{d}</small></span></a>' for _, n, d, s, t, b in CATS)
    brands = "".join(f'<a class="brand" href="shop.html" data-origin="{o}"><b>{escape(n)}</b><span>{escape(c)}</span></a>' for n, o, c in BRANDS)
    concerns = "".join(f'<a href="shop.html">{c}</a>' for _, c in CONCERNS)
    reviews = [
        ("The Hyaluronic Dew Serum sorted out my dry patches in a week. Delivery took one day and the box had the batch code and expiry printed on it.", "Ayesha K.", "Hyaluronic Dew Serum"),
        ("Hard to find genuine Korean skincare locally. Sensula has the brands I use, the prices are fair, and I paid cash on delivery with no fuss.", "Daniel R.", "Gentle Foaming Cleanser"),
        ("Rose & Oud lasts all day on me. I've had three compliments at work already. I've ordered the 100 ml for my sister.", "Mariam S.", "Rose & Oud Eau de Parfum"),
    ]
    rv = "".join(f'<figure class="review">{stars(5, 0, False)}<blockquote>“{escape(t)}”</blockquote><footer><span><b>{w}</b> · <span class="verified">Verified buyer</span></span><span>{p}</span></footer></figure>' for t, w, p in reviews)
    usp = [("shield", "Authenticity guaranteed", "Sourced from brands and authorised distributors only. Batch and expiry on every order."),
           ("leaf", "Curated by experts", "Our team tests and reviews every product before it goes on the shelf."),
           ("truck", "Same-day dispatch", "Order by 3pm and your parcel leaves our warehouse today."),
           ("chat", "Free skin consultation", "Chat with a beauty advisor on WhatsApp for a routine that fits your skin.")]
    usph = "".join(f"<div>{ic(i)}<h3>{a}</h3><p>{b}</p></div>" for i, a, b in usp)

    html = head("Sensula | Buy Original Skincare, Makeup, Haircare & Fragrance Online",
                "Shop 100% original beauty and personal care from 120+ local and international brands. Skincare, makeup, haircare, fragrance and men's grooming with fast delivery and cash on delivery.",
                "", ld)
    html += header() + trust()
    html += f'''
<main id="main">
<section class="hero">
  <div class="wrap hero__grid">
    <div>
      <p class="eyebrow">120+ brands · Local &amp; international</p>
      <h1>Beauty you can <em>trust</em>, delivered tomorrow.</h1>
      <p class="lede">Original skincare, makeup, haircare and fragrance from the brands you love, checked for authenticity and shipped the same day.</p>
      <div class="hero__ctas">
        <a class="btn btn--primary btn--lg" href="shop.html">Shop bestsellers</a>
        <a class="btn btn--ghost btn--lg" href="#concerns">Find my routine</a>
      </div>
      <div class="hero__proof">{stars(4.8, "18,400")} <span>from 18,400+ verified buyers</span></div>
    </div>
    <div class="hero__art" aria-hidden="true">
      <div class="disc"></div>
      <div class="p p1">{art("jar")}</div>
      <div class="p p3">{art("pump")}</div>
      <div class="p p2">{art("dropper")}</div>
      <div class="hero__tag"><b>Up to 30% off</b>K-beauty week</div>
    </div>
  </div>
</section>

<section class="section" aria-labelledby="cat-h">
  <div class="wrap">
    <div class="sec-head"><div><p class="eyebrow">Shop by category</p><h2 class="h2" id="cat-h">Everything for your routine</h2></div><a class="link-arrow" href="shop.html">View all categories</a></div>
    <div class="cats">{cats}</div>
  </div>
</section>

<section class="section section--tint" aria-labelledby="best-h">
  <div class="wrap">
    <div class="sec-head"><div><p class="eyebrow">Most loved this month</p><h2 class="h2" id="best-h">Bestsellers</h2></div><a class="link-arrow" href="shop.html">Shop all bestsellers</a></div>
    <div class="grid">{"".join(card(p) for p in best)}</div>
  </div>
</section>

<section class="section" aria-label="Offers">
  <div class="wrap promo">
    <div class="promo__card promo__card--a">
      <p class="eyebrow" style="color:#e9b8cb">First order</p>
      <h3>Take 10% off your first order</h3>
      <p>Use code <span class="promo__code">HELLO10</span> at checkout. Works on every brand, including sale items.</p>
      <a class="btn" href="shop.html">Start shopping</a>
    </div>
    <div class="promo__card promo__card--b">
      <p class="eyebrow" style="color:var(--sage)">Bundle &amp; save</p>
      <h3>Build a 3-step routine, save 15%</h3>
      <p>Pick any cleanser, serum and SPF. The discount applies automatically in your bag.</p>
      <a class="btn btn--dark" href="shop.html">Build my routine</a>
    </div>
  </div>
</section>

<section class="section section--tint" id="concerns" aria-labelledby="con-h">
  <div class="wrap">
    <div class="sec-head"><div><p class="eyebrow">Shop by concern</p><h2 class="h2" id="con-h">What would you like to fix?</h2><p class="lede">Pick a concern and we'll show products that target it, with the key ingredient on every card.</p></div></div>
    <div class="concerns">{concerns}</div>
  </div>
</section>

<section class="section" id="brands" aria-labelledby="brand-h">
  <div class="wrap">
    <div class="sec-head"><div><p class="eyebrow">Our brands</p><h2 class="h2" id="brand-h">Local favourites and global icons</h2></div><a class="link-arrow" href="shop.html">All brands A–Z</a></div>
    <div class="brand-tabs" role="group" aria-label="Filter brands">
      <button class="chip" type="button" data-brand-tab="all" aria-pressed="true">All brands</button>
      <button class="chip" type="button" data-brand-tab="intl" aria-pressed="false">International</button>
      <button class="chip" type="button" data-brand-tab="local" aria-pressed="false">Local</button>
    </div>
    <div class="brands">{brands}</div>
  </div>
</section>

<section class="section section--tint" aria-labelledby="new-h">
  <div class="wrap">
    <div class="sec-head"><div><p class="eyebrow">Just landed</p><h2 class="h2" id="new-h">New in</h2></div><a class="link-arrow" href="shop.html">See what's new</a></div>
    <div class="grid">{"".join(card(p) for p in new)}</div>
  </div>
</section>

<section class="section" aria-labelledby="rv-h">
  <div class="wrap">
    <div class="sec-head"><div><p class="eyebrow">Reviews</p><h2 class="h2" id="rv-h">18,400+ happy customers</h2></div>
      <div class="score"><span class="score__big">4.8</span><div>{stars(4.8, "18,412", False)}<p style="font-size:13px;color:var(--ink-3)">Average from 18,412 verified reviews</p></div></div></div>
    <div class="reviews" style="background:var(--blush);padding:20px;border-radius:var(--r-md)">{rv}</div>
  </div>
</section>

<section class="section section--tint" aria-labelledby="why-h">
  <div class="wrap">
    <div class="sec-head"><div><p class="eyebrow">Why Sensula</p><h2 class="h2" id="why-h">Shop with confidence</h2></div></div>
    <div class="usp">{usph}</div>
  </div>
</section>

<section class="section" aria-labelledby="faq-h">
  <div class="wrap" style="display:grid;gap:24px">
    <div><p class="eyebrow">Questions</p><h2 class="h2" id="faq-h">Frequently asked questions</h2></div>
    <div class="faq">{faq_html(HOME_FAQ)}</div>
  </div>
</section>

<section class="section" style="padding-top:0" aria-label="Newsletter">
  <div class="wrap">
    <div class="news">
      <div><h2>Get 10% off your first order</h2><p>Join 60,000 subscribers for new launches, restocks and members-only offers. One or two emails a week.</p></div>
      <form data-demo="news">
        <label class="sr-only" for="news-email">Email address</label>
        <input id="news-email" type="email" required placeholder="Your email address" autocomplete="email">
        <button class="btn" type="submit">Get my code</button>
        <small>Unsubscribe anytime. We never share your email.</small>
      </form>
    </div>
  </div>
</section>

<section class="section section--tint" aria-labelledby="seo-h">
  <div class="wrap seo-copy">
    <h2 class="h2" id="seo-h">Your online beauty and personal care store</h2>
    <h3>Original beauty products from local and international brands</h3>
    <p>Sensula is a multi-brand online store for skincare, makeup, haircare, bath and body, fragrance, men's grooming and everyday personal hygiene. We stock more than 120 brands, from Korean and French skincare labels to trusted local names and our own Sensula Atelier perfumes.</p>
    <h3>How we guarantee authenticity</h3>
    <p>We buy every product directly from the brand or its authorised distributor. Each parcel shows the batch number and expiry date, and our customer care team can confirm the source of any product before you buy.</p>
    <h3>Skincare for every skin type</h3>
    <p>Shop serums with hyaluronic acid, vitamin C and niacinamide, gentle cleansers, ceramide moisturisers and daily sunscreen. Filter by concern (acne, dryness, dark spots or sensitivity) to find products that suit your skin.</p>
    <h3>Fast delivery and cash on delivery</h3>
    <p>Orders placed before 3pm ship the same day. Delivery is free over {m(SITE["free_ship"])}, and you can pay by card, digital wallet or cash on delivery.</p>
  </div>
</section>
</main>'''
    html += cart_drawer() + footer()
    return html


# ================================================================= SHOP
SHOP_FAQ = [
    ("Which skincare products should I start with?", "A simple routine has three steps: a gentle cleanser, a serum for your main concern, and a broad-spectrum SPF 30 or higher in the morning. Add a moisturiser at night if your skin feels tight."),
    ("Is Korean skincare suitable for oily or acne-prone skin?", "Yes. Many Korean products are lightweight and water-based. Look for non-comedogenic formulas with ingredients such as centella, niacinamide or salicylic acid."),
    ("How do I know which products are local and which are imported?", "Every product card shows a 'Local brand' or 'Imported' label, and you can filter by brand origin in the sidebar."),
]


def build_shop():
    items = PRODUCTS
    crumbs = [("Home", "index.html"), ("Shop", "shop.html"), ("All beauty & personal care", "shop.html")]
    ld = [ORG_LD, crumbs_ld(crumbs),
          {"@context": "https://schema.org", "@type": "CollectionPage", "name": "Shop all beauty & personal care",
           "url": f'{SITE["url"]}/shop.html',
           "mainEntity": {"@type": "ItemList", "numberOfItems": len(items),
                          "itemListElement": [{"@type": "ListItem", "position": i + 1, "url": f'{SITE["url"]}/product.html#{p["id"]}', "name": p["name"]} for i, p in enumerate(items)]}},
          faq_ld(SHOP_FAQ)]

    def group(title, name, opts):
        rows = "".join(f'<label class="check"><input type="checkbox" id="f-{name}-{v}" name="{name}" value="{v}" data-label="{escape(l)}"> {escape(l)}<span>{c}</span></label>' for v, l, c in opts)
        return f'<fieldset class="fgroup"><legend>{title}</legend>{rows}</fieldset>'

    from collections import Counter
    cc = Counter(p["cat"] for p in items)
    bc = Counter(p["brand"] for p in items)
    oc = Counter(p["origin"] for p in items)
    conc = Counter(c for p in items for c in p["concern"].split())
    filters = (
        group("Category", "cat", [(k, CAT_NAME[k], v) for k, v in cc.items()]) +
        group("Brand origin", "origin", [("local", "Local brands", oc["local"]), ("intl", "International brands", oc["intl"])]) +
        group("Brand", "brand", [(b.lower().replace(" ", "-").replace(".", ""), b, n) for b, n in sorted(bc.items())]) +
        group("Concern", "concern", [(k, l, conc[k]) for k, l in CONCERNS if conc[k]]) +
        f'''<div class="fgroup range"><p>Price</p><label for="price-max">Up to <b id="price-out">{m(50)}</b></label>
        <input id="price-max" type="range" min="5" max="50" step="1" value="50"></div>'''
    )
    grid_items = [card(p) for p in items]
    grid_items.insert(6, f'<aside class="inline-banner"><div><b>Build a routine, save 15%</b><p style="font-size:14px;color:var(--ink-2)">Any cleanser + serum + SPF. Discount applies in your bag.</p></div><a class="btn btn--dark" href="#">Build my routine</a></aside>')
    html = head("Shop Beauty & Personal Care Online | Skincare, Makeup, Haircare | Sensula",
                "Browse 100% original skincare, makeup, haircare, fragrance and personal care from local and international brands. Filter by brand, concern and price. Free delivery over " + m(SITE["free_ship"]) + ".",
                "shop.html", ld)
    html += header()
    html += f'''
<main id="main">
<div class="shop-head">
  {crumbs_html(crumbs)}
  <div class="wrap">
    <h1>Beauty &amp; personal care</h1>
    <p class="lede">Shop {len(items) * 40}+ original products from 120+ local and international brands. Every item is sourced from the brand or its authorised distributor.</p>
    <div class="concerns" aria-label="Popular searches">{"".join(f'<a href="shop.html">{c}</a>' for _, c in CONCERNS[:5])}</div>
  </div>
</div>
<div class="wrap shop">
  <aside class="filters" aria-label="Filters">
    <div class="filters__head"><h2>Filter</h2><div style="display:flex;gap:12px;align-items:center"><button class="linkish" type="button" data-clear-filters>Clear all</button><button class="icon-btn filter-toggle" type="button" data-close aria-label="Close filters">{ic("close")}</button></div></div>
    {filters}
  </aside>
  <section aria-label="Products">
    <div class="toolbar">
      <div class="toolbar__left">
        <button class="btn btn--ghost filter-toggle" type="button" data-open-filters style="padding:10px 16px">{ic("filter")} Filter</button>
        <p><b data-result-count>{len(items)}</b> products</p>
      </div>
      <label>
        <span class="sr-only">Sort by</span>
        <select id="sort"><option value="featured">Sort: Featured</option><option value="rating">Top rated</option><option value="price-asc">Price: low to high</option><option value="price-desc">Price: high to low</option></select>
      </label>
    </div>
    <div class="active-filters" aria-live="polite"></div>
    <div class="grid grid--3" data-product-grid>{"".join(grid_items)}</div>
    <p class="empty" hidden>No products match these filters. <button class="linkish" type="button" data-clear-filters>Clear filters</button></p>
    <nav class="pager" aria-label="Pagination"><span aria-current="page">1</span><a href="shop.html?page=2">2</a><a href="shop.html?page=3">3</a><a href="shop.html?page=2" aria-label="Next page">›</a></nav>
  </section>
</div>

<section class="section section--tint" aria-labelledby="guide-h">
  <div class="wrap seo-copy">
    <h2 class="h2" id="guide-h">A quick guide to buying beauty online</h2>
    <h3>Choose by concern, not by hype</h3>
    <p>Start with the one thing you want to change: breakouts, dryness, dark spots or dullness. Use the Concern filter to narrow the list, then compare the key ingredient shown on each product page.</p>
    <h3>Local or international?</h3>
    <p>Local brands are usually priced lower and formulated for the local climate. International brands give you access to proven formulas from Korea, France and Japan. Both are sourced directly and carry the same authenticity guarantee.</p>
    <h3>Check size and price per ml</h3>
    <p>Larger sizes often cost less per ml. Each product page shows all available sizes and how much you save on the larger one.</p>
  </div>
</section>
<section class="section" aria-labelledby="sfaq-h">
  <div class="wrap" style="display:grid;gap:24px">
    <h2 class="h2" id="sfaq-h">Shopping questions</h2>
    <div class="faq">{faq_html(SHOP_FAQ)}</div>
  </div>
</section>
</main>'''
    html += cart_drawer() + footer()
    return html


# ================================================================= PRODUCT
def tagspan(t):
    return f'<span class="tag">{t or "&nbsp;"}</span>'


def build_product():
    p = PRODUCTS[0]
    related = [q for q in PRODUCTS if q["id"] in ("gentle-foaming-cleanser", "spf50-sun-fluid", "ceramide-night-cream", "vitamin-c-glow-serum")]
    cleanser = next(q for q in PRODUCTS if q["id"] == "gentle-foaming-cleanser")
    spf = next(q for q in PRODUCTS if q["id"] == "spf50-sun-fluid")
    sizes = [("15 ml", 14.00, 16.00, ""), ("30 ml", 24.00, 30.00, "Most popular"), ("50 ml", 36.00, 48.00, "Best value")]
    crumbs = [("Home", "index.html"), ("Skincare", "shop.html"), ("Serums", "shop.html"), (p["name"], "product.html")]
    pfaq = [
        ("Can I use Hyaluronic Dew Serum with vitamin C or retinol?", "Yes. Hyaluronic acid works well with other actives. Apply vitamin C first in the morning and let it absorb, then this serum. At night, apply it before or after retinol to reduce dryness."),
        ("Is it suitable for oily and acne-prone skin?", "Yes. The formula is oil-free, fragrance-free and non-comedogenic, so it hydrates without clogging pores."),
        ("How long does one 30 ml bottle last?", "Used twice a day with 2–3 drops, a 30 ml bottle lasts about 8–10 weeks."),
        ("Is this the original Hanok Skin product?", "Yes. We buy Hanok Skin directly from the brand's authorised distributor. The batch number and expiry date are printed on every box."),
    ]
    reviews_list = [
        ("Ayesha K.", "Dry, sensitive skin", "2 weeks", 5, "My skin finally feels plump", "I have dry, flaky patches around my nose. After two weeks of using this morning and night they're gone and my makeup sits better. No stinging at all.", ["Hydrating", "No fragrance"]),
        ("Hira M.", "Combination skin", "1 month", 5, "Light and absorbs in seconds", "It isn't sticky like other hyaluronic serums I've tried. I layer it under SPF every morning with no pilling.", ["Lightweight", "Layers well"]),
        ("Omar F.", "Oily skin", "3 weeks", 4, "Good, but buy the 50 ml", "Works well and didn't break me out. Only four stars because the 30 ml went fast. The 50 ml is better value.", ["No breakouts"]),
    ]
    bars = [(5, 82), (4, 12), (3, 4), (2, 1), (1, 1)]
    ld = [ORG_LD, crumbs_ld(crumbs), faq_ld(pfaq), {
        "@context": "https://schema.org", "@type": "Product", "name": p["name"], "sku": "HNK-HDS-30", "gtin13": "8801234567890",
        "image": [f'{SITE["url"]}/assets/img/{p["id"]}-1.jpg', f'{SITE["url"]}/assets/img/{p["id"]}-2.jpg'],
        "description": "A lightweight serum with 5 types of hyaluronic acid and 2% panthenol that hydrates dry, dull skin for up to 72 hours. Fragrance-free, suitable for all skin types.",
        "brand": {"@type": "Brand", "name": p["brand"]}, "category": "Skincare > Serums",
        "countryOfOrigin": "KR",
        "aggregateRating": {"@type": "AggregateRating", "ratingValue": p["rating"], "reviewCount": p["reviews"]},
        "review": [{"@type": "Review", "author": {"@type": "Person", "name": r[0]}, "name": r[4], "reviewBody": r[5],
                    "reviewRating": {"@type": "Rating", "ratingValue": r[3], "bestRating": 5}} for r in reviews_list],
        "offers": [{"@type": "Offer", "sku": f"HNK-HDS-{s.split()[0]}", "name": s, "price": f"{pr:.2f}", "priceCurrency": SITE["currency_code"],
                    "availability": "https://schema.org/InStock", "itemCondition": "https://schema.org/NewCondition",
                    "url": f'{SITE["url"]}/product.html', "priceValidUntil": "2026-12-31",
                    "shippingDetails": {"@type": "OfferShippingDetails", "shippingRate": {"@type": "MonetaryAmount", "value": "0", "currency": SITE["currency_code"]},
                                        "deliveryTime": {"@type": "ShippingDeliveryTime", "handlingTime": {"@type": "QuantitativeValue", "minValue": 0, "maxValue": 1, "unitCode": "DAY"},
                                                         "transitTime": {"@type": "QuantitativeValue", "minValue": 1, "maxValue": 5, "unitCode": "DAY"}}},
                    "hasMerchantReturnPolicy": {"@type": "MerchantReturnPolicy", "returnPolicyCategory": "https://schema.org/MerchantReturnFiniteReturnWindow", "merchantReturnDays": 14}}
                   for s, pr, _, _ in sizes],
    }]
    size_btns = "".join(
        f'<button type="button" aria-pressed="{"true" if s == "30 ml" else "false"}" data-size="{s}" data-price="{pr}" data-was="{w}">'
        f'{tagspan(t)}<b>{s}</b><small>{m(pr)} · {m(pr / float(s.split()[0]))}/ml</small></button>'
        for s, pr, w, t in sizes)
    thumbs = "".join(f'<button type="button" data-view="{i}" aria-current="{"true" if i == 0 else "false"}" aria-label="{lab}">{art(p["shape"])}</button>'
                     for i, lab in enumerate(["Front of bottle", "Angled view", "Dropper close-up", "Texture"]))
    rvh = "".join(f'''<article class="rv"><div class="rv__who"><b>{n}</b><span class="verified">Verified buyer</span><span>{skin}</span><span>Used for {used}</span></div>
      <div>{stars(r, 0, False)}<h3>{escape(t)}</h3><p>{escape(b)}</p><div class="rv__tags">{"".join(f"<span>{x}</span>" for x in tags)}</div></div></article>''' for n, skin, used, r, t, b, tags in reviews_list)
    barh = "".join(f'<div><span>{s} star</span><i style="--w:{w}%"></i><span>{w}%</span></div>' for s, w in bars)
    bundle_price = p["price"] + cleanser["price"] + spf["price"]
    bundle_items = "".join(
        f'<a class="bundle__item" href="product.html" data-bundle-item {add_attrs(q)[9:]}><div class="card__media" style="--tone:{q["tone"]};--bg:{q["bg"]}">{art(q["shape"])}</div><span>{escape(q["name"])}</span><b>{m(q["price"])}</b></a>'
        for q in (p, cleanser, spf))
    bundle_items = bundle_items.replace('</a><a class="bundle__item"', '</a><span class="bundle__plus" aria-hidden="true">+</span><a class="bundle__item"')

    html = head(f"{p['name']} {p['size']} by {p['brand']} | Buy Original Online | Sensula",
                f"Buy {p['brand']} {p['name']} ({p['size']}) online at {m(p['price'])}. 5 types of hyaluronic acid for 72-hour hydration. 100% original, rated {p['rating']}/5 by {p['reviews']} buyers. Cash on delivery, free delivery over {m(SITE['free_ship'])}.",
                "product.html", ld, og_type="product")
    html += header()
    html += f'''
<main id="main">
{crumbs_html(crumbs)}
<div class="wrap pdp">
  <div class="gallery" style="--tone:{p["tone"]};--bg:{p["bg"]}">
    <div class="thumbs" aria-label="Product images">{thumbs}</div>
    <div class="stage" data-view="0">
      {art(p["shape"])}
      <div class="card__badges"><span class="pill pill--sale">−20%</span><span class="pill">Bestseller</span><span class="pill pill--intl">Imported · Korea</span></div>
      <span class="stage__note">Front of bottle</span>
    </div>
  </div>

  <div class="buybox">
    <div style="display:grid;gap:8px">
      <a class="buybox__brand" href="shop.html">{p["brand"]}</a>
      <h1>{p["name"]}</h1>
      <p class="buybox__sub">5 types of hyaluronic acid + 2% panthenol · For dry, dull and dehydrated skin</p>
      <div class="rating">{stars(p["rating"], p["reviews"], False)} <b>{p["rating"]}</b> <a href="#reviews">{p["reviews"]} reviews</a> · <a href="#qa">4 answered questions</a></div>
    </div>
    <div>
      <p class="price"><span class="price__now" data-pdp-price>{m(p["price"])}</span><span class="price__was" data-pdp-was>{m(p["was"])}</span><span class="price__save" data-pdp-save>Save 20%</span></p>
      <p class="tax-note">Tax included. Or 3 interest-free payments of {m(p["price"] / 3)}.</p>
    </div>
    <ul class="benefits">
      <li>Hydrates for up to 72 hours (clinically tested on 32 people)</li>
      <li>Fragrance-free, alcohol-free, non-comedogenic</li>
      <li>Suitable for all skin types, including sensitive</li>
    </ul>
    <div>
      <p class="opt-label">Size: <span data-size-label>30 ml</span></p>
      <div class="sizes">{size_btns}</div>
    </div>
    <p class="stock">In stock, ready to ship <em>· Only 9 left in 30 ml</em></p>
    <div class="buy-row">
      <div class="qty"><button type="button" data-step="-1" aria-label="Decrease quantity">−</button><label class="sr-only" for="qty">Quantity</label><input id="qty" type="number" min="1" max="10" value="1" inputmode="numeric"><button type="button" data-step="1" aria-label="Increase quantity">+</button></div>
      <button id="add-main" class="btn btn--primary btn--lg" type="button" {add_attrs(p)} data-use-qty>Add to bag · <span data-pdp-price>{m(p["price"])}</span></button>
    </div>
    <button class="btn btn--dark btn--lg btn--block" type="button" {add_attrs(p)} data-use-qty data-buy-now>Buy now, pay on delivery</button>
    <div class="ship-bar"><span data-ship-text>Free delivery over {m(SITE["free_ship"])}</span><div class="ship-bar__track"><div class="ship-bar__fill"></div></div></div>
    <ul class="deliver">
      <li>{ic("clock")}<span>Order within <b>3 hrs 20 min</b> for <b>same-day dispatch</b></span></li>
      <li>{ic("truck")}<span>Delivery in 1–2 days to major cities, 3–5 days elsewhere</span></li>
      <li>{ic("shield")}<span><b>100% original.</b> Sourced from the Hanok Skin authorised distributor</span></li>
      <li>{ic("return")}<span>14-day returns on unopened products</span></li>
    </ul>
    <div class="pay-row">Pay with <span>COD</span><span>VISA</span><span>MC</span><span>APPLE PAY</span><span>GPAY</span></div>

    <section class="facts" aria-labelledby="facts-h">
      <h2 id="facts-h">Quick facts</h2>
      <dl>
        <dt>What it is</dt><dd>A water-light hydrating serum</dd>
        <dt>Best for</dt><dd>Dry, dehydrated or dull skin; all skin types</dd>
        <dt>Key ingredients</dt><dd>5 hyaluronic acids (1.5%), panthenol 2%, centella asiatica</dd>
        <dt>Texture</dt><dd>Clear gel-serum, absorbs in about 30 seconds</dd>
        <dt>Use</dt><dd>AM and PM after cleansing, before moisturiser</dd>
        <dt>Free from</dt><dd>Fragrance, alcohol, parabens, mineral oil</dd>
        <dt>Origin</dt><dd>Made in South Korea · Cruelty-free</dd>
        <dt>Shelf life</dt><dd>12 months after opening; expiry on box</dd>
      </dl>
    </section>

    <div class="acc">
      <details open><summary>Description</summary><div>
        <p>Hyaluronic Dew Serum pulls moisture into every layer of the skin. Five hyaluronic acids of different molecular sizes hydrate the surface and deeper layers, while panthenol calms redness and strengthens the skin barrier.</p>
        <p>The result is skin that feels plump and smooth and looks dewy, without a sticky finish. It layers well under moisturiser, sunscreen and makeup.</p>
      </div></details>
      <details><summary>How to use</summary><div>
        <p>1. Cleanse and pat skin dry, leaving it slightly damp.<br>2. Apply 2–3 drops to face and neck and press in gently.<br>3. Follow with moisturiser. In the morning, finish with SPF 30 or higher.</p>
      </div></details>
      <details><summary>Full ingredients</summary><div>
        <p>Water, Butylene Glycol, Glycerin, Panthenol, Sodium Hyaluronate, Hydrolyzed Hyaluronic Acid, Sodium Acetylated Hyaluronate, Hyaluronic Acid, Sodium Hyaluronate Crosspolymer, Centella Asiatica Extract, Allantoin, Betaine, 1,2-Hexanediol, Carbomer, Arginine, Disodium EDTA.</p>
      </div></details>
      <details><summary>Delivery &amp; returns</summary><div>
        <p>Free delivery over {m(SITE["free_ship"])}; otherwise a flat {m(3.99)}. Unopened items can be returned within 14 days. Damaged or incorrect items are replaced free of charge.</p>
      </div></details>
    </div>
  </div>
</div>

<section class="section section--tint" aria-labelledby="fbt-h">
  <div class="wrap">
    <div class="sec-head"><div><p class="eyebrow">Complete the routine</p><h2 class="h2" id="fbt-h">Frequently bought together</h2></div></div>
    <div class="bundle" style="background:#fff">
      <div class="bundle__items">{bundle_items}</div>
      <div class="bundle__sum">
        <p>Total for 3 items</p>
        <p class="price" style="justify-content:inherit"><span class="price__now" style="font-size:24px">{m(bundle_price * .85)}</span><span class="price__was">{m(bundle_price)}</span></p>
        <p class="price__save">You save {m(bundle_price * .15)} (15%)</p>
        <button class="btn btn--primary btn--lg" type="button" data-add data-bundle="1">Add all 3 to bag</button>
      </div>
    </div>
  </div>
</section>

<section class="section" id="reviews" aria-labelledby="rvs-h">
  <div class="wrap">
    <div class="sec-head"><div><p class="eyebrow">Reviews</p><h2 class="h2" id="rvs-h">What customers say</h2></div><button class="btn btn--ghost" type="button">Write a review</button></div>
    <div class="rv-summary">
      <div class="score" style="align-items:flex-start;flex-direction:column;gap:6px"><span class="score__big">{p["rating"]}</span>{stars(p["rating"], p["reviews"], False)}<p style="font-size:14px;color:var(--ink-2)">Based on {p["reviews"]} verified reviews<br><b style="color:var(--sage)">96%</b> would recommend</p></div>
      <div class="bars" aria-label="Rating breakdown">{barh}</div>
    </div>
    <div class="rv-list">{rvh}</div>
    <p style="margin-top:20px"><a class="link-arrow" href="#reviews">Read all {p["reviews"]} reviews</a></p>
  </div>
</section>

<section class="section section--tint" id="qa" aria-labelledby="qa-h">
  <div class="wrap" style="display:grid;gap:24px">
    <div><p class="eyebrow">Questions &amp; answers</p><h2 class="h2" id="qa-h">Questions about this serum</h2></div>
    <div class="faq">{faq_html(pfaq)}</div>
  </div>
</section>

<section class="section" aria-labelledby="rel-h">
  <div class="wrap">
    <div class="sec-head"><div><p class="eyebrow">You may also like</p><h2 class="h2" id="rel-h">Pairs well with</h2></div><a class="link-arrow" href="shop.html">Shop all skincare</a></div>
    <div class="grid">{"".join(card(q) for q in related)}</div>
  </div>
</section>
</main>

<div class="sticky-atc" aria-label="Quick add to bag">
  <div class="sticky-atc__info"><b>{p["name"]} · <span data-size-label-sticky>{p["size"]}</span></b><span data-pdp-price>{m(p["price"])}</span></div>
  <button class="btn btn--primary" type="button" {add_attrs(p)} data-use-qty>Add to bag</button>
</div>'''
    html += cart_drawer() + footer()
    return html


def build_extras():
    base = SITE["url"]
    sitemap = '<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n' + "".join(
        f"  <url><loc>{base}/{u}</loc><changefreq>{c}</changefreq><priority>{pr}</priority></url>\n"
        for u, c, pr in [("", "daily", "1.0"), ("shop.html", "daily", "0.9"), ("product.html", "weekly", "0.8")]) + "</urlset>\n"
    robots = f"User-agent: *\nAllow: /\nDisallow: /cart\nDisallow: /checkout\nDisallow: /account\n\nSitemap: {base}/sitemap.xml\n"
    llms = f"""# {SITE["name"]}

> {SITE["name"]} is a multi-brand online store for original beauty and personal care products: skincare, makeup, haircare, bath & body, fragrance, men's grooming and personal hygiene. It stocks 120+ local and international brands, sourced directly from brands or authorised distributors.

Key facts:
- Delivery: same-day dispatch before 3pm; 1–2 days to major cities, 3–5 days elsewhere; free over {m(SITE["free_ship"])}.
- Payment: cash on delivery, cards, Apple Pay, Google Pay.
- Returns: 14 days on unopened items.
- Customer care: {SITE["phone"]}, {SITE["email"]}, 9am–9pm daily.

## Main pages
- [Home]({base}/): categories, bestsellers, brands, offers, FAQ
- [Shop all]({base}/shop.html): full catalogue with filters by category, brand, origin, concern and price

## Bestselling products
""" + "".join(f"- [{p['name']}]({base}/product.html): {p['brand']}, {p['size']}, {m(p['price'])}, rated {p['rating']}/5 ({p['reviews']} reviews)\n" for p in PRODUCTS[:8])
    return sitemap, robots, llms


if __name__ == "__main__":
    out = {"index.html": build_home(), "shop.html": build_shop(), "product.html": build_product()}
    out["sitemap.xml"], out["robots.txt"], out["llms.txt"] = build_extras()
    for name, content in out.items():
        with open(name, "w", encoding="utf-8") as f:
            f.write(content)
        print("wrote", name, len(content), "bytes")
