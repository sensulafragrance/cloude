import re, json, html
SITE='/tmp/claude-0/-home-user-cloude/2682baff-9cb6-53bf-af5e-3bcfa84e0a83/scratchpad/site/'
orig=open(SITE+'../artifact-files/c7210914-4079-45be-85d1-ae145de1c900/product.html').read()
SVGDEFS=orig[orig.index('<svg width="0"'):orig.index('</defs></svg>')+len('</defs></svg>')]
SVGDEFS=SVGDEFS.replace('</defs></svg>',' <symbol id="ic-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></symbol>\n</defs></svg>')

def rs(n): return 'Rs '+f'{n:,}'
e=html.escape

CATS=[('skincare','Skincare','Serums, cleansers, creams'),('sun','Sun care','SPF, sticks, tinted'),('hair','Hair care','Hair fall, masks, oils'),
('body','Body care','Lotions, washes, gels'),('fragrance','Fragrance','Mists, perfume, decants'),('makeup','Makeup','Lips, blush, face'),
('men','Men','Face wash, beard, sprays'),('personal','Personal care','Razors, hygiene')]

NAV=''.join(f'<li><a href="shop.html">{e(n)}</a></li>' for _,n,_ in CATS)+'<li><a href="./#brands">Brands</a></li><li><a href="shop.html" class="is-sale">Offers</a></li>'

POPULAR=['Sunscreen','Hair fall shampoo','Axis-Y','PDRN','Body mist','Rosemary','Minis under Rs 1,000','Niacinamide']
def popular(): return '<div class="popular"><span>Popular:</span>'+''.join(f'<a href="shop.html">{e(p)}</a>' for p in POPULAR)+'</div>'

def finder_form(fid):
    return f'''<form class="finder-form" role="search" data-demo="search" action="shop.html">
      <div class="field"><label class="sr-only" for="{fid}">Search products, brands and concerns</label><svg aria-hidden="true"><use href="#ic-search"/></svg><input id="{fid}" name="q" type="search" placeholder="Search a product, brand or concern, e.g. hair fall" autocomplete="off"></div>
      <button class="btn btn--primary btn--lg" type="submit">Search</button>
    </form>'''

HEADER=f'''<header class="header">
  <div class="wrap header__bar header__bar--slim">
    <div style="display:flex;align-items:center;gap:4px">
      <button class="icon-btn menu-btn" type="button" data-open-menu aria-label="Open menu"><svg aria-hidden="true"><use href="#ic-menu"/></svg></button>
      <a class="logo" href="./" aria-label="Sensula home">Sensula<span>.</span></a>
    </div>
    <div class="actions">
      <button class="icon-btn" type="button" data-open-search aria-label="Search"><svg aria-hidden="true"><use href="#ic-search"/></svg></button>
      <a class="icon-btn hide-sm" href="#account" aria-label="Your account"><svg aria-hidden="true"><use href="#ic-user"/></svg></a>
      <a class="icon-btn hide-sm" href="#wishlist" aria-label="Wishlist"><svg aria-hidden="true"><use href="#ic-heart"/></svg></a>
      <button class="icon-btn" type="button" data-open-cart aria-label="Open bag"><svg aria-hidden="true"><use href="#ic-bag"/></svg><span class="badge-count" data-cart-count hidden>0</span></button>
    </div>
  </div>
  <nav class="nav" aria-label="Main"><div class="wrap"><ul>{NAV}</ul></div></nav>
</header>
<nav class="mnav" aria-label="Mobile" aria-hidden="true">
  <div style="display:flex;justify-content:space-between;align-items:center"><span class="logo">Sensula<span>.</span></span><button class="icon-btn" type="button" data-close aria-label="Close menu"><svg aria-hidden="true"><use href="#ic-close"/></svg></button></div>
  <ul>{NAV}<li><a href="#account">My account</a></li><li><a href="#track">Track my order</a></li></ul>
</nav>
<div class="searchpanel" aria-hidden="true" aria-label="Search">
  <div class="wrap">
    <div style="display:flex;justify-content:space-between;align-items:center"><p class="eyebrow">Search Sensula</p><button class="icon-btn" type="button" data-close aria-label="Close search"><svg aria-hidden="true"><use href="#ic-close"/></svg></button></div>
    {finder_form("q-panel")}
    {popular()}
  </div>
</div>'''

TAIL=f'''<div class="scrim"></div>
<aside class="drawer" aria-label="Shopping bag" aria-hidden="true">
  <div class="drawer__head"><h2>Your bag</h2><button class="icon-btn" type="button" data-close aria-label="Close bag"><svg aria-hidden="true"><use href="#ic-close"/></svg></button></div>
  <div class="ship-bar"><span data-ship-text>Free delivery over Rs 3,500</span><div class="ship-bar__track"><div class="ship-bar__fill"></div></div></div>
  <div class="drawer__items"></div>
  <div class="drawer__foot">
    <div class="drawer__total"><span>Subtotal</span><span data-cart-total>Rs 0</span></div>
    <button class="btn btn--primary btn--lg btn--block" type="button" data-checkout>Checkout securely</button>
    <small>Cash on delivery, cards, JazzCash and Easypaisa accepted.</small>
  </div>
</aside>
<div class="toast" role="status" aria-live="polite"></div>
<footer class="footer">
  <div class="wrap">
    <div class="footer__grid">
      <div class="footer__about"><span class="logo">Sensula<span>.</span></span>
        <p>A Pakistani multi-brand beauty store. Local derm brands, Korean favourites and our own Koh-e-Noor fragrances, with the batch and expiry shown on every order.</p>
        <p>Customer care: <b>+92 21 0000 0000</b><br>WhatsApp advice 10am to 10pm, every day</p>
      </div>
      <div><h3>Shop</h3><ul>{''.join(f'<li><a href="shop.html">{e(n)}</a></li>' for _,n,_ in CATS[:6])}</ul></div>
      <div><h3>Brands</h3><ul><li><a href="shop.html">Pakistani derm brands</a></li><li><a href="shop.html">Korean beauty</a></li><li><a href="shop.html">Koh-e-Noor by Sensula</a></li><li><a href="shop.html">Minis &amp; travel sizes</a></li><li><a href="shop.html">All brands A–Z</a></li></ul></div>
      <div><h3>Help</h3><ul><li><a href="shop.html">Track my order</a></li><li><a href="shop.html">Delivery across Pakistan</a></li><li><a href="shop.html">Returns &amp; exchanges</a></li><li><a href="shop.html">How we check authenticity</a></li><li><a href="shop.html">Contact us</a></li></ul></div>
      <div><h3>Company</h3><ul><li><a href="shop.html">About Sensula</a></li><li><a href="shop.html">Beauty journal</a></li><li><a href="shop.html">Stock your brand with us</a></li><li><a href="shop.html">Wholesale</a></li><li><a href="shop.html">Privacy policy</a></li></ul></div>
    </div>
    <div class="footer__bottom">
      <span>© 2026 Sensula. All rights reserved.</span>
      <div class="pay" aria-label="Payment methods"><span>COD</span><span>VISA</span><span>MASTERCARD</span><span>JAZZCASH</span><span>EASYPAISA</span></div>
    </div>
  </div>
</footer>
<script>window.STORE = {{ currency: "Rs ", freeShip: 3500, decimals: 0 }};</script>
<script src="assets/js/main.js" defer></script>'''

HEADLINKS='''<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,500;0,6..96,600;1,6..96,500&family=Figtree:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap">
<link rel="stylesheet" href="assets/css/styles.css">'''

PILL={'best':('','Bestseller'),'mini':('pill--mini','Mini'),'new':('pill--new','New'),'derm':('pill--derm','Derm brand'),
'own':('pill--own','Our label'),'kr':('pill--intl','Korea'),'local':('pill--local','Pakistani'),'intl':('pill--intl','Imported'),'sale':('pill--sale','')}

def card(p, extra=''):
    pills=''.join(f'<span class="pill {PILL[k][0]}">{e(PILL[k][1])}</span>' for k in p['pills'])
    was=f'<span class="price__was"><span class="sr-only">Was </span>{rs(p["was"])}</span>' if p.get('was') else ''
    top=' data-top' if p.get('top') else ''
    return f'''<article class="card" data-cat="{p['cat']}"{top}{extra}>
  <div class="card__media" style="--tone:{p['tone']};--bg:{p['bg']}">
    <svg aria-hidden="true" viewBox="0 0 100 160"><use href="#i-{p['shape']}"/></svg>
    <div class="card__badges">{pills}</div>
  </div>
  <button class="card__wish" type="button" aria-pressed="false" aria-label="Save {e(p['name'])} to wishlist"><svg aria-hidden="true"><use href="#ic-heart"/></svg></button>
  <div class="card__quick"><button class="btn btn--dark btn--block" type="button" data-add data-id="{p['id']}" data-name="{e(p['brand']+' '+p['name'])}" data-price="{p['price']}" data-size="{e(p['size'])}" data-shape="{p['shape']}" data-tone="{p['tone']}" data-bg="{p['bg']}">Add to bag</button></div>
  <p class="card__brand">{e(p['brand'])}</p>
  <h3 class="card__title"><a href="product.html">{e(p['name'])}</a></h3>
  <p class="card__why">{e(p['why'])}</p>
  <div class="card__meta"><p class="price"><span class="price__now">{rs(p['price'])}</span>{was}</p><span class="card__size">{e(p['size'])}</span></div>
</article>'''
