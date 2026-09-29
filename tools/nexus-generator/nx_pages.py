import json, html, io, contextlib
with contextlib.redirect_stdout(io.StringIO()):
    import nexus as N
from nexus_css import CSS
e=html.escape; rs=N.rs; ic=N.ic; shape=N.shape; P=N.P; BY=N.BYID
BASE='https://nexusbeauty.pk/'
HEADER=N.BODY[N.BODY.index('<a class="skip"'):N.BODY.index('<main id="main">')]
FOOTER=N.BODY[N.BODY.index('<footer class="footer">'):]
JSCORE=N.JS[:N.JS.index('/* Product tabs */')]
JSEND='\nrender();\n})();\n'
FONTS=N.FONTS
def doc(title,desc,canon,ld,body,pagejs,og_type='website'):
    lds=''.join(f'<script type="application/ld+json">{json.dumps(x,ensure_ascii=False)}</script>\n' for x in ld)
    return f'''<!doctype html>
<html lang="en-PK">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>{title}</title>
<meta name="description" content="{desc}">
<link rel="canonical" href="{canon}">
<meta name="robots" content="index, follow, max-image-preview:large">
<meta name="theme-color" content="#8c2350">
<meta property="og:type" content="{og_type}"><meta property="og:site_name" content="Nexus Beauty"><meta property="og:title" content="{title}"><meta property="og:description" content="{desc}"><meta property="og:url" content="{canon}"><meta property="og:locale" content="en_PK">
<meta name="twitter:card" content="summary_large_image">
{FONTS}
<style>{CSS}</style>
{lds}</head>
<body>
{HEADER}<main id="main">
{body}
</main>
{FOOTER}<script>{JSCORE}{pagejs}{JSEND}</script>
</body>
</html>
'''
def origin(p):
    if 'own' in p['pills']: return 'own'
    if 'pk' in p['pills'] or 'derm' in p['pills']: return 'pk'
    if 'kr' in p['pills']: return 'kr'
    return 'intl'
def scard(p,i):
    c=N.card(p)
    attrs=f' data-cat="{p["cat"]}" data-brand="{e(p["brand"])}" data-origin="{origin(p)}" data-concern="{p["concerns"]}" data-price="{p["price"]}" data-mini="{1 if ("mini" in p["pills"] or p["price"]<1000) else 0}" data-new="{1 if "new" in p["pills"] else 0}" data-derm="{1 if "derm" in p["pills"] else 0}" data-i="{i}"'
    return c.replace('<article class="card" ','<article class="card"'+attrs+' ',1)

# ================= SHOP =================
CATS=[('skincare','Skincare'),('sun','Sun care'),('hair','Hair care'),('body','Body care'),('fragrance','Fragrance'),('makeup','Makeup'),('men',"Men's grooming"),('personal','Personal care')]
CONC=[('spots','Dark spots'),('dull','Dullness'),('acne','Acne'),('dry','Dryness'),('sensitive','Sensitive skin'),('sun','Sun protection'),('hairfall','Hair fall'),('dandruff','Dandruff'),('frizz','Frizz'),('body','Body care'),('scent','Long-lasting scent'),('lips','Lips')]
ORIG=[('pk','Pakistani brands'),('kr','Korean beauty'),('intl','International'),('own','Our label (Koh-e-Noor)')]
PRICES=[('any','Any price'),('0-999','Under Rs 1,000'),('1000-2999','Rs 1,000 – 2,999'),('3000-4999','Rs 3,000 – 4,999'),('5000-999999','Rs 5,000 and above')]
def count(f): return sum(1 for p in P if f(p))
def checks(name,items,fn):
    return ''.join(f'<label class="check"><input type="checkbox" name="{name}" value="{k}" data-label="{e(l)}"><span>{e(l)}</span><span class="n">{fn(k)}</span></label>' for k,l in items)
brands=sorted({p['brand'] for p in P})
filters=f'''
<div class="filters__head"><h2 id="filters-h">Filter</h2><button class="linkish" type="button" data-clear>Clear all</button></div>
<div class="filters__body">
<fieldset class="fgroup"><legend>Category</legend><div class="fopts">{checks('cat',CATS,lambda k:count(lambda p:p['cat']==k))}</div></fieldset>
<fieldset class="fgroup"><legend>Concern</legend><div class="fopts">{checks('concern',CONC,lambda k:count(lambda p:k in p['concerns'].split()))}</div></fieldset>
<fieldset class="fgroup"><legend>Price</legend><div class="fopts">{''.join(f'<label class="check"><input type="radio" name="price" value="{k}" data-label="{e(l)}"{" checked" if k=="any" else ""}><span>{e(l)}</span></label>' for k,l in PRICES)}</div></fieldset>
<fieldset class="fgroup"><legend>Made by</legend><div class="fopts">{checks('origin',ORIG,lambda k:count(lambda p:origin(p)==k))}</div></fieldset>
<fieldset class="fgroup"><legend>Show only</legend><div class="fopts">
 <label class="check"><input type="checkbox" name="flag" value="derm" data-label="Derm brands"><span>Pakistani derm brands</span><span class="n">{count(lambda p:'derm' in p['pills'])}</span></label>
 <label class="check"><input type="checkbox" name="flag" value="mini" data-label="Minis & under Rs 1,000"><span>Minis &amp; under Rs 1,000</span><span class="n">{count(lambda p:'mini' in p['pills'] or p['price']<1000)}</span></label>
 <label class="check"><input type="checkbox" name="flag" value="new" data-label="New arrivals"><span>New arrivals</span><span class="n">{count(lambda p:'new' in p['pills'])}</span></label>
</div></fieldset>
<fieldset class="fgroup fgroup--scroll"><legend>Brand</legend><div class="fopts">{checks('brand',[(b,b) for b in brands],lambda k:count(lambda p:p['brand']==k))}</div></fieldset>
</div>
<div class="filters__foot"><button class="btn btn--g" type="button" data-clear>Clear</button><button class="btn btn--p" type="button" data-close-filters>Show <span data-count>{len(P)}</span> products</button></div>'''
cards=''.join(scard(p,i) for i,p in enumerate(P))
promo='<aside class="promo-tile" aria-label="Routine finder"><p class="eyebrow" style="color:var(--pink)">Not sure what to choose?</p><h3>Get a routine in 3 taps</h3><p>Tell us your concern, skin type and budget. We\'ll pick products that fit.</p><a class="btn btn--w" href="index.html#finder">Open the routine finder</a></aside>'
catbar='<button class="chip" type="button" data-quick="all" aria-pressed="true">All</button>'+''.join(f'<button class="chip" type="button" data-quick="{k}" aria-pressed="false">{e(l)}</button>' for k,l in CATS)+'<button class="chip" type="button" data-quick="derm" aria-pressed="false">Pakistani derm</button><button class="chip" type="button" data-quick="minis" aria-pressed="false">Minis</button><button class="chip" type="button" data-quick="new" aria-pressed="false">New in</button><button class="chip" type="button" data-quick="own" aria-pressed="false">Koh-e-Noor</button>'
SHOP_FAQ=[('Are the products on Nexus Beauty original?','Yes. We buy only from brands and their authorised distributors. Every order lists the batch number and expiry date of each product on the parcel sticker.'),
('Which sunscreen is best for oily skin in Pakistan?','Light, gel-like formulas work best in heat and humidity. Beauty of Joseon Relief Sun and Estelin Tinted SPF 70 are our best sellers; Estelin also hides redness without a white cast.'),
('What is the best shampoo for hair fall?','Rederm Tressfix and Jenpharm Anagrow Biotin are Pakistan\'s most-bought hair fall shampoos. Use them 3 times a week for at least 6–8 weeks. For heavy hair fall, see a dermatologist.'),
('Do you offer cash on delivery?','Yes, everywhere in Pakistan. Delivery is free over Rs 5,000.')]
shop_body=f'''
<div class="shop-head">
 <div class="wrap">
  <nav class="crumbs" aria-label="Breadcrumb"><ol><li><a href="index.html">Home</a></li><li aria-current="page" id="crumbCur">Shop all</li></ol></nav>
  <h1 id="shopTitle">Shop original beauty products</h1>
  <p class="lede" id="shopIntro">Skincare, sunscreen, hair care, fragrance and makeup from Pakistani derm brands and global favourites. Every order shows the batch and expiry date. Cash on delivery anywhere in Pakistan.</p>
  <div class="catbar" role="group" aria-label="Quick categories">{catbar}</div>
 </div>
</div>
<div class="wrap shop">
 <aside class="filters" id="filters" aria-labelledby="filters-h">{filters}</aside>
 <section aria-label="Products">
  <div class="toolbar">
   <p><b data-count>{len(P)}</b> products</p>
   <div class="toolbar__r">
    <button class="btn btn--g filter-toggle" type="button" id="openFilters" style="padding:9px 16px;font-size:14px">Filter</button>
    <label class="sr-only" for="sort">Sort by</label>
    <select class="select" id="sort"><option value="featured">Sort: Best sellers</option><option value="price-asc">Price: low to high</option><option value="price-desc">Price: high to low</option><option value="new">Newest first</option><option value="az">Name: A to Z</option></select>
   </div>
  </div>
  <div class="active" id="active" aria-live="polite"></div>
  <div class="grid" id="shopGrid">{cards}{promo}</div>
  <div class="empty-state" id="empty" hidden><b>No products match these filters.</b><p>Try removing a filter, or message us on WhatsApp and we'll find it for you.</p><button class="btn btn--d" type="button" data-clear>Clear all filters</button></div>
 </section>
</div>
<section class="section section--tint" aria-labelledby="probs-h">
 <div class="wrap">
  <div class="sec-head"><div><p class="eyebrow">Shop by problem</p><h2 class="h2" id="probs-h">Complete kits with a step-by-step guide</h2><p class="lede">Everything you need for one problem, in the right order, with tips to get results. 15% off when you choose 3 or more.</p></div><a class="link" href="solutions.html">See all routines</a></div>
  <div class="probs">__PROBS__</div>
 </div>
</section>
<section class="section" aria-labelledby="sfaq-h">
 <div class="wrap" style="display:grid;gap:24px">
  <div><p class="eyebrow">Buying guide</p><h2 class="h2" id="sfaq-h">Questions before you buy</h2></div>
  <div class="faq">{''.join(f'<details><summary>{e(q)}</summary><p>{e(a)}</p></details>' for q,a in SHOP_FAQ)}</div>
 </div>
</section>
<section class="section" aria-labelledby="seo-h">
 <div class="wrap seo-text">
  <h2 class="h2" id="seo-h">Buy original skincare and beauty products online in Pakistan</h2>
  <h3>Pakistani derm brands in one place</h3>
  <p>Nexus Beauty stocks dermatologist brands such as Rederm, Estelin, Neophar and Jenpharm, next to Korean skincare from AXIS-Y, Beauty of Joseon, Dr. Althea and Medicube, and everyday favourites from L'Oréal Paris, Garnier, Pond's and Vaseline.</p>
  <h3>Sunscreen for Pakistan's climate</h3>
  <p>Choose from SPF 50 to SPF 70, including tinted, stick and serum formats that stay light in heat and humidity. Filter by "Sun protection" to see them all.</p>
  <h3>Hair fall and dandruff care</h3>
  <p>Shop hair fall shampoos with biotin, anti-dandruff formulas and rosemary hair masks. Use the Concern filter to find products for your hair.</p>
  <h3>Original products, checked before they ship</h3>
  <p>We buy only from brands and authorised distributors. Every parcel lists the batch number and expiry date, and you can pay cash on delivery anywhere in Pakistan.</p>
 </div>
</section>'''
shop_ld=[
 {"@context":"https://schema.org","@type":"CollectionPage","name":"Shop original beauty products","url":BASE+"shop","description":"Original skincare, sunscreen, hair care and fragrance delivered across Pakistan.","isPartOf":{"@type":"WebSite","name":"Nexus Beauty","url":BASE},
  "mainEntity":{"@type":"ItemList","numberOfItems":len(P),"itemListElement":[{"@type":"ListItem","position":i+1,"name":p['brand']+' '+p['name'],"url":BASE+"products/"+p['id']} for i,p in enumerate(P)]}},
 {"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":BASE},{"@type":"ListItem","position":2,"name":"Shop all","item":BASE+"shop"}]},
 {"@context":"https://schema.org","@type":"FAQPage","mainEntity":[{"@type":"Question","name":q,"acceptedAnswer":{"@type":"Answer","text":a}} for q,a in SHOP_FAQ]}]
CATNAMES=dict(CATS); CATNAMES.update({'derm':'Pakistani derm brands','minis':'Minis & under Rs 1,000','new':'New arrivals','own':'Koh-e-Noor by Nexus Beauty','all':'Shop all'})
SHOP_JS=r'''
/* ===== Shop page ===== */
var sgrid=$("#shopGrid"),scards=$$(".card",sgrid),promoT=$(".promo-tile",sgrid),fl=$("#filters"),sortSel=$("#sort"),activeEl=$("#active"),emptyEl=$("#empty");
var NAMES=__NAMES__;
function inputs(){return $$("#filters input");}
function sel(name){return $$('#filters input[name="'+name+'"]:checked').map(function(i){return i.value;});}
function applyShop(){
  var cat=sel("cat"),con=sel("concern"),org=sel("origin"),br=sel("brand"),fg=sel("flag"),pr=(sel("price")[0]||"any");
  var lo=0,hi=1e9;if(pr!=="any"){var a=pr.split("-");lo=+a[0];hi=+a[1];}
  var shown=[];
  scards.forEach(function(c){var d=c.dataset,ok=true;
    if(cat.length&&cat.indexOf(d.cat)<0)ok=false;
    if(ok&&con.length&&!con.some(function(k){return (" "+d.concern+" ").indexOf(" "+k+" ")>-1;}))ok=false;
    if(ok&&org.length&&org.indexOf(d.origin)<0)ok=false;
    if(ok&&br.length&&br.indexOf(d.brand)<0)ok=false;
    if(ok&&(+d.price<lo||+d.price>hi))ok=false;
    if(ok&&fg.indexOf("derm")>-1&&d.derm!=="1")ok=false;
    if(ok&&fg.indexOf("mini")>-1&&d.mini!=="1")ok=false;
    if(ok&&fg.indexOf("new")>-1&&d.new!=="1")ok=false;
    c.hidden=!ok;if(ok)shown.push(c);});
  var s=sortSel.value;
  shown.sort(function(a,b){var A=a.dataset,B=b.dataset;
    if(s==="price-asc")return A.price-B.price; if(s==="price-desc")return B.price-A.price;
    if(s==="new")return (B.new-A.new)||(A.i-B.i);
    if(s==="az")return a.querySelector(".card__title").textContent.localeCompare(b.querySelector(".card__title").textContent);
    return A.i-B.i;});
  var hidden=scards.filter(function(c){return c.hidden;});
  shown.concat(hidden).forEach(function(c){sgrid.appendChild(c);});
  if(shown.length>=9){sgrid.insertBefore(promoT,shown[8]);promoT.hidden=false;}else{promoT.hidden=true;}
  $$("[data-count]").forEach(function(el){el.textContent=shown.length;});
  emptyEl.hidden=shown.length>0;
  var chips=$$("#filters input:checked").filter(function(i){return i.value!=="any";});
  activeEl.innerHTML=chips.map(function(i){return '<button type="button" data-un="'+esc(i.name)+'|'+esc(i.value)+'">'+esc(i.dataset.label)+' ×</button>';}).join("")+(chips.length>1?'<button type="button" data-clear style="background:none;text-decoration:underline;color:var(--ink-2)">Clear all</button>':'');
  var q="all";if(chips.length===1){var c0=chips[0];q=c0.name==="cat"?c0.value:c0.name==="flag"?(c0.value==="mini"?"minis":c0.value):c0.name==="origin"&&c0.value==="own"?"own":"";}
  $$("[data-quick]").forEach(function(b){b.setAttribute("aria-pressed",b.dataset.quick===q);});
  var title=NAMES[q];$("#shopTitle").textContent=title&&q!=="all"?title:"Shop original beauty products";$("#crumbCur").textContent=title&&q!=="all"?title:"Shop all";
}
function setOnly(tok){
  inputs().forEach(function(i){i.checked=i.type==="radio"?i.value==="any":false;});
  var map={minis:["flag","mini"],derm:["flag","derm"],"new":["flag","new"],own:["origin","own"]};
  if(map[tok]){var m=$('#filters input[name="'+map[tok][0]+'"][value="'+map[tok][1]+'"]');if(m)m.checked=true;}
  else if(tok&&tok!=="all"){var c=$('#filters input[name="cat"][value="'+tok+'"]');if(c)c.checked=true;}
  applyShop();
}
$$("[data-quick]").forEach(function(b){b.addEventListener("click",function(){setOnly(b.dataset.quick);try{history.replaceState(null,"","#"+b.dataset.quick);}catch(err){}});});
inputs().forEach(function(i){i.addEventListener("change",applyShop);});
sortSel.addEventListener("change",applyShop);
document.addEventListener("click",function(ev){
  var u=ev.target.closest("[data-un]");if(u){var p=u.dataset.un.split("|");var inp=$('#filters input[name="'+p[0]+'"][value="'+p[1].replace(/"/g,'\\"')+'"]');if(inp){inp.checked=false;if(inp.type==="radio")$('#filters input[value="any"]').checked=true;}applyShop();return;}
  if(ev.target.closest("[data-clear]")){setOnly("all");return;}
});
function closeFilters(){fl.classList.remove("is-open");scrim.classList.remove("is-open");}
$("#openFilters").addEventListener("click",function(){fl.classList.add("is-open");scrim.classList.add("is-open");});
$$("[data-close-filters]").forEach(function(b){b.addEventListener("click",closeFilters);});
scrim.addEventListener("click",closeFilters);
document.addEventListener("keydown",function(ev){if(ev.key==="Escape")closeFilters();});
var tok=(location.hash||"").replace("#","");setOnly(NAMES[tok]?tok:"all");
window.addEventListener("hashchange",function(){var t=location.hash.replace("#","");if(NAMES[t])setOnly(t);});
'''.replace('__NAMES__',json.dumps(CATNAMES))
probs=''.join(f'<a class="prob" href="solutions.html#rg-{r["key"]}"><span class="prob__art" style="--bg:{N.T[r["tone"]][1]}">'+''.join(f'<svg viewBox="0 0 100 160" aria-hidden="true" style="color:{BY[x[2]]["tone"]}"><use href="#i-{BY[x[2]]["shape"]}"/></svg>' for x in r['steps'][:4])+f'</span><span class="prob__body"><h3>{e(r["name"])}</h3><p>{len(r["steps"])} products · from {rs(int(round(sum(BY[x[2]]["price"] for x in r["steps"])*.85)))} as a full kit</p><span class="link">View kit and guide</span></span></a>' for r in N.RG)
shop_body=shop_body.replace('__PROBS__',probs)
shop=doc('Buy Original Skincare, Sunscreen &amp; Hair Care Online in Pakistan | Nexus Beauty',
 'Shop '+str(len(P))+' original beauty products: sunscreen, serums, hair fall shampoos, mists and makeup. Pakistani derm brands and Korean favourites. Batch and expiry on every order. Cash on delivery.',
 BASE+'shop',shop_ld,shop_body,SHOP_JS)
open('/home/user/cloude/nexus-beauty/shop.html','w').write(shop)

# ================= PRODUCT =================
A5,A50=BY['axis-y-5'],BY['axis-y-50']
PRODUCT_FAQ=[('How soon will I see results?','Most people notice a smoother, brighter look in 1–2 weeks. Dark spots usually look lighter after 4–8 weeks of daily use with sunscreen every morning. Results vary from person to person.'),
('Can I use it with vitamin C or retinol?','Yes. Niacinamide works well with most actives. Use vitamin C in the morning and this serum after it, or use this serum in the morning and retinol at night.'),
('Is it suitable for sensitive or acne-prone skin?','The formula is light and non-greasy, and many people with oily and acne-prone skin use it. If your skin is sensitive, patch test on your jaw for 2 days first.'),
('Which size should I buy?','Buy the 5 ml mini (about 2 weeks of use) to test it on your skin. If you like it, the 50 ml costs about half as much per ml and lasts around 3 months.'),
('Is this the original AXIS-Y serum?','Yes. We buy AXIS-Y from its authorised distributor. The batch number and expiry date are printed on the box and listed on your parcel sticker, so you can check them before you pay.'),
('Can I use it during pregnancy?','Please ask your doctor before starting any new skincare product during pregnancy or breastfeeding.')]
REL=[BY[i] for i in ['garnier-vitc','boj-relief-sun','estelin-70','dr-althea-345']]
FBT=[('axis-y-5','This item','Dark spot serum'),('boj-relief-sun','Protect','Sunscreen SPF 50+'),('loreal-glycolic','Cleanse','Glycolic face wash'),('ponds-gel','Moisturise','Light gel moisturiser')]
fbt_rows=''
for i,(pid,role,_) in enumerate(FBT):
    p=BY[pid]
    lock=' disabled data-this' if i==0 else ''
    nm=f'<b>{e(p["brand"])} {e(p["name"])} · <span id="fbtThisSize">5 ml mini</span></b>' if i==0 else f'<b>{e(p["brand"])} {e(p["name"])}</b><a href="product.html">View product</a>'
    fbt_rows+=f'<label class="fbt__item"><input type="checkbox" checked data-fbt="{pid}" data-price="{p["price"]}"{lock} aria-label="{"This item (always included)" if i==0 else "Include "+e(p["name"])}"><span class="thumb" style="--tone:{p["tone"]};--bg:{p["bg"]}">{shape(p["shape"])}</span><span><small>{e(role)}</small>{nm}</span><span class="p" {"id=fbtThisPrice" if i==0 else ""}>{rs(p["price"])}</span></label>'
PVIDEOS=[('Review','AXIS-Y Dark Spot Correcting Glow Serum reviews','axis-y dark spot correcting glow serum review','yel','dropper',''),
 ('How to','How to layer niacinamide in your routine','how to use niacinamide serum routine order','yel','dropper',''),
 ('How to','Fading acne marks: what actually works','how to fade acne marks dermatologist','pink','tube',''),
 ('How to','How much sunscreen to use on your face','how much sunscreen face two finger rule','sun','tube','')]
bund_ids=['loreal-glycolic','axis-y-50','boj-relief-sun']; btot=sum(BY[i]['price'] for i in bund_ids); bpr=int(round(btot*.85))
bundle_items='<span class="bundle__plus" aria-hidden="true">+</span>'.join(f'<a class="bundle__item" href="product.html"><span class="card__media" style="--tone:{BY[i]["tone"]};--bg:{BY[i]["bg"]}">{shape(BY[i]["shape"])}</span><span>{e(BY[i]["brand"])} {e(BY[i]["name"])}</span><b>{rs(BY[i]["price"])}</b></a>' for i in bund_ids)
TONE=A5['tone'];BG=A5['bg']
views=[('Front of bottle','dropper'),('Side view','dropper'),('Dropper close-up','dropper'),('Batch & expiry','batch')]
thumbs=''.join(f'<button type="button" data-view="{i}" aria-pressed="{str(i==0).lower()}" aria-label="{e(n)}">'+(shape(s) if s!='batch' else '<span class="tb">BATCH<br>EXP</span>')+'</button>' for i,(n,s) in enumerate(views))
prod_body=f'''
<div class="wrap">
 <nav class="crumbs" aria-label="Breadcrumb"><ol><li><a href="index.html">Home</a></li><li><a href="shop.html#skincare">Skincare</a></li><li><a href="shop.html#skincare">Serums</a></li><li aria-current="page">AXIS-Y Dark Spot Correcting Glow Serum</li></ol></nav>
</div>
<div class="wrap pdp">
 <div class="gallery" style="--tone:{TONE};--bg:{BG}">
  <div class="thumbs" role="group" aria-label="Product views">{thumbs}</div>
  <div class="stage" id="stage" data-view="0">
   {shape('dropper')}
   <div class="badges"><span class="pill">Best seller</span><span class="pill">Korea</span></div>
   <div class="batch"><b>✓ On your box</b><span>BATCH&nbsp;&nbsp;AX2609K</span><span>MFG&nbsp;&nbsp;&nbsp;&nbsp;03/2026</span><span>EXP&nbsp;&nbsp;&nbsp;&nbsp;02/2029</span></div>
   <span class="stage__note" id="stageNote">Front of bottle</span>
  </div>
 </div>

 <div class="buy">
  <div style="display:grid;gap:8px">
   <a class="buy__brand" href="shop.html#skincare">AXIS-Y · Korea</a>
   <h1>Dark Spot Correcting Glow Serum</h1>
   <p class="buy__sub">A light serum with 5% niacinamide that fades dark spots and acne marks, and evens out dull skin.</p>
   <div class="buy__meta"><span>For dark spots, acne marks, dullness</span><a href="#faq">6 questions answered</a><a href="#reviews">Be the first to review</a></div>
  </div>
  <div class="pricebox"><span class="now" id="price">{rs(A5['price'])}</span><small id="perml">Rs 159.8 per ml · Tax included</small></div>
  <div>
   <p class="opt-label">Size: <span id="sizeLabel">5 ml mini</span></p>
   <div class="sizes" role="group" aria-label="Choose a size">
    <button type="button" aria-pressed="true" data-size="5 ml mini" data-id="axis-y-5" data-price="799" data-ml="5"><span class="tag">Try it first</span><b>5 ml mini</b><small>Rs 799 · about 2 weeks</small></button>
    <button type="button" aria-pressed="false" data-size="50 ml" data-id="axis-y-50" data-price="4049" data-ml="50"><span class="tag">Best value · half price per ml</span><b>50 ml</b><small>Rs 4,049 · about 3 months</small></button>
   </div>
  </div>
  <p class="stock" id="stock">In stock · ready to ship today</p>
  <div class="buyrow">
   <div class="stepper"><button type="button" data-step="-1" aria-label="One less">−</button><label class="sr-only" for="qty">Quantity</label><input id="qty" type="number" inputmode="numeric" min="1" max="10" value="1"><button type="button" data-step="1" aria-label="One more">+</button></div>
   <button class="btn btn--p btn--lg" type="button" id="addMain" data-add-main>Add to bag · <span data-price-btn>{rs(A5['price'])}</span></button>
  </div>
  <button class="btn btn--d btn--lg btn--block" type="button" data-add-main data-buy-now>Buy now, pay cash on delivery</button>
  <div class="eta">
   <div class="eta__row">{ic('truck')}<label for="city">Delivery to</label><select id="city">
    <option value="1-2" selected>Karachi</option><option value="1-2">Lahore</option><option value="2-3">Islamabad</option><option value="2-3">Rawalpindi</option><option value="3-5">Faisalabad</option><option value="3-5">Multan</option><option value="3-5">Peshawar</option><option value="3-5">Hyderabad</option><option value="3-5">Quetta</option><option value="3-5">Other city</option></select></div>
   <p id="etaText">Arrives in 1–2 working days.</p>
   <p id="cutoff" style="color:var(--ink-2)"></p>
  </div>
  <ul class="assure">
   <li>{ic('shield')}<span><b>100% original</b>From AXIS-Y's authorised distributor</span></li>
   <li>{ic('cash')}<span><b>Cash on delivery</b>Pay when it arrives</span></li>
   <li>{ic('truck')}<span><b>Free delivery</b>On orders over Rs 5,000</span></li>
   <li>{ic('return')}<span><b>7-day returns</b>On unopened products</span></li>
  </ul>
  <div class="help"><span>Not sure it suits your skin? WhatsApp us: <b id="wa">+92 300 000 0000</b></span><button class="copy" type="button" id="copyWa">Copy number</button></div>
  <div class="acc">
   <details open><summary>Description</summary><div><p>AXIS-Y Dark Spot Correcting Glow Serum is a lightweight, fast-absorbing serum made for uneven tone, dark spots and marks left by acne. Its main active is 5% niacinamide, supported by plant extracts and light hydration. It sits well under sunscreen and makeup, which makes it easy to use in Pakistan's heat.</p></div></details>
   <details><summary>Full ingredients</summary><div><p>Please check the ingredient list printed on the box before use. The key ingredients are listed below.</p></div></details>
   <details><summary>Delivery &amp; returns</summary><div><p>Orders before 3pm leave the same day. Free delivery over Rs 5,000; otherwise Rs 250. Unopened products can be returned within 7 days.</p></div></details>
  </div>
 </div>
</div>

<section class="section section--tint" aria-labelledby="fit-h">
 <div class="wrap" style="display:grid;gap:28px">
  <div class="sec-head" style="margin:0"><div><p class="eyebrow">Is it right for me?</p><h2 class="h2" id="fit-h">Who this serum is for</h2></div></div>
  <div class="fit">
   <div class="yes"><h3>Good for you if you have</h3><ul><li>Dark spots or sun spots</li><li>Marks left behind by acne</li><li>Dull, uneven skin tone</li><li>Oily, combination or normal skin</li></ul></div>
   <div class="no"><h3>Choose something else if</h3><ul><li>You have active, painful breakouts (try <a href="product.html" style="text-decoration:underline">Anua Azelaic Acid</a>)</li><li>Your skin is very dry and tight (add Dr. Althea 345 Relief Cream)</li><li>You won't wear sunscreen daily. Spots come back without it.</li></ul></div>
  </div>
  <div><h3 style="font-size:20px;margin-bottom:14px">Key ingredients</h3>
   <div class="ingr">
    <div><span>Main active</span><b>Niacinamide 5%</b><p>Vitamin B3. Helps fade dark spots, evens tone and controls oil.</p></div>
    <div><span>Antioxidant</span><b>Sea buckthorn</b><p>Rich in vitamins that help skin look brighter.</p></div>
    <div><span>Tone support</span><b>Mulberry &amp; licorice</b><p>Plant extracts used to help even out skin tone.</p></div>
    <div><span>Hydration</span><b>Squalane</b><p>Light moisture without a greasy finish.</p></div>
   </div>
  </div>
 </div>
</section>

<section class="section" aria-labelledby="use-h">
 <div class="wrap" style="display:grid;gap:28px">
  <div class="sec-head" style="margin:0"><div><p class="eyebrow">How to use</p><h2 class="h2" id="use-h">Your routine with this serum</h2></div></div>
  <div class="use">
   <div><h3>Morning</h3><ol><li>Wash with a gentle face wash.</li><li>Press 2–3 drops into face and neck.</li><li>Moisturise if your skin feels dry.</li><li>Finish with SPF 50. This step matters most.</li></ol></div>
   <div><h3>Night</h3><ol><li>Remove makeup and sunscreen, then wash.</li><li>Apply 2–3 drops.</li><li>Follow with moisturiser.</li><li>Using retinol? Apply it after the serum, 2–3 nights a week.</li></ol></div>
  </div>
  <div><h3 style="font-size:20px;margin-bottom:18px">What to expect</h3>
   <div class="timeline"><div><b>WEEK 1–2</b><h3>Smoother, brighter</h3><p>Skin looks more even and feels softer.</p></div><div><b>WEEK 4–6</b><h3>Marks look lighter</h3><p>Acne marks and small spots begin to fade.</p></div><div><b>WEEK 8+</b><h3>More even tone</h3><p>Keep using it with daily sunscreen to hold the results.</p></div></div>
   <p style="font-size:13px;color:var(--ink-3);margin-top:12px">Results vary. Deep or long-standing pigmentation may need a dermatologist.</p>
  </div>
 </div>
</section>

<section class="section section--tint" aria-labelledby="fbt-h">
 <div class="wrap">
  <div class="sec-head"><div><p class="eyebrow">Frequently bought together</p><h2 class="h2" id="fbt-h">Complete the routine and save 15%</h2><p class="lede">Sunscreen stops new spots while the serum fades old ones. Untick anything you already have.</p></div></div>
  <div class="fbt" id="fbt">
   <div class="fbt__list">{fbt_rows}</div>
   <div class="rg__buy"><div class="rg__line"><span>Selected: <b id="fbtCount">4</b> products</span><span id="fbtSub"></span></div><div class="rg__line rg__disc" id="fbtDiscRow"><span>Bundle discount (15%)</span><span id="fbtDisc"></span></div><div class="rg__line rg__tot"><span>Total</span><b id="fbtTotal"></b></div><button class="btn btn--p btn--lg btn--block" type="button" id="fbtAdd">Add 4 to bag</button><small id="fbtNote">15% off when you choose 3 or more.</small></div>
  </div>
 </div>
</section>

<section class="section" aria-labelledby="pv-h">
 <div class="wrap">
  <div class="sec-head"><div><p class="eyebrow">Watch before you buy</p><h2 class="h2" id="pv-h">Reviews and how to use it</h2><p class="lede">Independent reviews and guides on YouTube.</p></div></div>
  <div class="videos">{N.video_cards(PVIDEOS)}</div>
 </div>
</section>

<section class="section" id="reviews" aria-labelledby="rv-h">
 <div class="wrap" style="display:grid;gap:24px">
  <div class="sec-head" style="margin:0"><div><p class="eyebrow">Reviews</p><h2 class="h2" id="rv-h">Customer reviews</h2></div></div>
  <div class="rv">
   <div class="rv__empty"><h3>No reviews on Nexus Beauty yet</h3><p>We only publish reviews from verified orders, and we never edit or hide them. Bought this serum from us? Tell others how it worked for your skin.</p><p style="font-size:14px">Every published review gets <b>Rs 200 off</b> your next order.</p></div>
   <form class="rvform" id="rvForm" novalidate>
    <fieldset class="stars-in"><legend>Your rating</legend>
     {''.join(f'<input type="radio" id="st{n}" name="stars" value="{n}"><label for="st{n}" title="{n} star{"s" if n>1 else ""}">★</label>' for n in range(5,0,-1))}
    </fieldset>
    <label for="rvName">Your name<input type="text" id="rvName" autocomplete="name" placeholder="e.g. Ayesha K."></label>
    <label for="rvText">Your review<textarea id="rvText" rows="4" placeholder="What changed for your skin, and after how long?"></textarea></label>
    <label for="rvOrder">Order number<input type="text" id="rvOrder" placeholder="NX-104829"></label>
    <button class="btn btn--d" type="submit">Submit review</button>
    <p class="formmsg" id="rvMsg" role="status"></p>
   </form>
  </div>
 </div>
</section>

<section class="section section--tint" id="faq" aria-labelledby="pq-h">
 <div class="wrap" style="display:grid;gap:24px">
  <div><p class="eyebrow">Questions &amp; answers</p><h2 class="h2" id="pq-h">Questions about this serum</h2></div>
  <div class="faq">{''.join(f'<details><summary>{e(q)}</summary><p>{e(a)}</p></details>' for q,a in PRODUCT_FAQ)}</div>
 </div>
</section>

<section class="section" aria-labelledby="rel-h">
 <div class="wrap">
  <div class="sec-head"><div><p class="eyebrow">You may also like</p><h2 class="h2" id="rel-h">Pairs well with this serum</h2></div><a class="link" href="shop.html#skincare">Shop all skincare</a></div>
  <div class="grid">{''.join(N.card(p) for p in REL)}</div>
 </div>
</section>

<div class="sticky-atc" id="stickyAtc" aria-hidden="true">
 <div class="sticky-atc__info"><b>AXIS-Y Glow Serum · <span data-size-sticky>5 ml mini</span></b><span data-price-btn>{rs(A5['price'])}</span></div>
 <button class="btn btn--p" type="button" data-add-main tabindex="-1">Add to bag</button>
</div>'''
offers=[{"@type":"Offer","sku":p['id'].upper(),"name":p['size'],"price":str(p['price']),"priceCurrency":"PKR","availability":"https://schema.org/InStock","itemCondition":"https://schema.org/NewCondition","url":BASE+"products/axis-y-dark-spot-correcting-glow-serum","seller":{"@type":"Organization","name":"Nexus Beauty"},
 "shippingDetails":{"@type":"OfferShippingDetails","shippingDestination":{"@type":"DefinedRegion","addressCountry":"PK"},"shippingRate":{"@type":"MonetaryAmount","value":"250","currency":"PKR"},"deliveryTime":{"@type":"ShippingDeliveryTime","handlingTime":{"@type":"QuantitativeValue","minValue":0,"maxValue":1,"unitCode":"DAY"},"transitTime":{"@type":"QuantitativeValue","minValue":1,"maxValue":5,"unitCode":"DAY"}}},
 "hasMerchantReturnPolicy":{"@type":"MerchantReturnPolicy","applicableCountry":"PK","returnPolicyCategory":"https://schema.org/MerchantReturnFiniteReturnWindow","merchantReturnDays":7}} for p in (A5,A50)]
prod_ld=[
 {"@context":"https://schema.org","@type":"Product","name":"AXIS-Y Dark Spot Correcting Glow Serum","brand":{"@type":"Brand","name":"AXIS-Y"},"category":"Skincare > Serums","countryOfOrigin":"KR",
  "description":"A lightweight serum with 5% niacinamide that fades dark spots and acne marks and evens out dull skin. Available as a 5 ml mini and 50 ml.","image":[BASE+"images/axis-y-dark-spot-correcting-glow-serum.jpg"],"offers":offers},
 {"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":BASE},{"@type":"ListItem","position":2,"name":"Skincare","item":BASE+"shop#skincare"},{"@type":"ListItem","position":3,"name":"Serums","item":BASE+"shop#skincare"},{"@type":"ListItem","position":4,"name":"AXIS-Y Dark Spot Correcting Glow Serum","item":BASE+"products/axis-y-dark-spot-correcting-glow-serum"}]},
 {"@context":"https://schema.org","@type":"FAQPage","mainEntity":[{"@type":"Question","name":q,"acceptedAnswer":{"@type":"Answer","text":a}} for q,a in PRODUCT_FAQ]}]
PROD_JS=r'''
/* ===== Product page ===== */
var stage=$("#stage"),note=$("#stageNote"),curId="axis-y-5",curPrice=799,curSize="5 ml mini";
$$(".thumbs button").forEach(function(b){b.addEventListener("click",function(){$$(".thumbs button").forEach(function(x){x.setAttribute("aria-pressed",x===b);});stage.dataset.view=b.dataset.view;note.textContent=b.getAttribute("aria-label");});});
$$(".sizes button").forEach(function(b){b.addEventListener("click",function(){
  $$(".sizes button").forEach(function(x){x.setAttribute("aria-pressed",x===b);});
  curId=b.dataset.id;curPrice=+b.dataset.price;curSize=b.dataset.size;
  $("#price").textContent=rs(curPrice);$$("[data-price-btn]").forEach(function(el){el.textContent=rs(curPrice);});
  $("#perml").textContent="Rs "+(curPrice/+b.dataset.ml).toFixed(1)+" per ml · Tax included";
  $("#sizeLabel").textContent=curSize;var t=$("[data-this]");t.dataset.fbt=curId;t.dataset.price=curPrice;$("#fbtThisSize").textContent=curSize;$("#fbtThisPrice").textContent=rs(curPrice);if(typeof fbtUpdate==="function")fbtUpdate();$$("[data-size-sticky]").forEach(function(el){el.textContent=curSize;});
});});
var qty=$("#qty");
$$("[data-step]").forEach(function(b){b.addEventListener("click",function(){qty.value=Math.min(10,Math.max(1,(parseInt(qty.value,10)||1)+(+b.dataset.step)));});});
qty.addEventListener("change",function(){qty.value=Math.min(10,Math.max(1,parseInt(qty.value,10)||1));});
$$("[data-add-main]").forEach(function(b){b.addEventListener("click",function(){
  var n=Math.min(10,Math.max(1,parseInt(qty.value,10)||1));
  for(var i=0;i<n;i++)addItem(curId,true);
  toast((n>1?n+" × ":"")+BY[curId].n+" ("+curSize+") added to your bag");open("drawer");
});});
/* Delivery estimate (Pakistan time, Sunday off, 3pm cut-off) */
var DAYS=["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],MON=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
function pk(){return new Date(Date.now()+5*36e5);}
function nextWork(d,n){var x=new Date(d.getTime());while(n>0){x.setUTCDate(x.getUTCDate()+1);if(x.getUTCDay()!==0)n--;}return x;}
function fmt(d){return DAYS[d.getUTCDay()]+" "+d.getUTCDate()+" "+MON[d.getUTCMonth()];}
function eta(){
  var r=$("#city").value.split("-"),now=pk(),h=now.getUTCHours(),m=now.getUTCMinutes();
  var today=now.getUTCDay()!==0&&h<15,disp=today?now:nextWork(now,1);
  var a=nextWork(disp,+r[0]),b=nextWork(disp,+r[1]);
  $("#etaText").innerHTML="Arrives <b>"+fmt(a)+" – "+fmt(b)+"</b> to "+esc($("#city").selectedOptions[0].textContent)+".";
  if(today){var left=(15*60)-(h*60+m);$("#cutoff").textContent="Order in the next "+Math.floor(left/60)+" h "+(left%60)+" min for same-day dispatch.";}
  else{$("#cutoff").textContent="Orders placed now leave our warehouse on "+fmt(disp)+".";}
}
/* Bought together */
function fbtUpdate(){var on=$$("[data-fbt]").filter(function(b){return b.checked;}),sub=0;on.forEach(function(b){sub+=+b.dataset.price;});var n=on.length,disc=n>=3?Math.round(sub*.15):0;
  $("#fbtCount").textContent=n;$("#fbtSub").textContent=rs(sub);$("#fbtDisc").textContent="−"+rs(disc);$("#fbtDiscRow").hidden=!disc;$("#fbtTotal").textContent=rs(sub-disc);
  $("#fbtAdd").textContent=n>1?"Add "+n+" to bag":"Add this serum only";$("#fbtNote").textContent=n>=3?"15% off applied.":"Choose "+(3-n)+" more to get 15% off.";}
$$("[data-fbt]").forEach(function(b){b.addEventListener("change",fbtUpdate);});
$("#fbtAdd").addEventListener("click",function(){var ids=$$("[data-fbt]").filter(function(b){return b.checked;}).map(function(b){return b.dataset.fbt;});
  if(ids.length>=3){var sub=ids.reduce(function(t,id){return t+BY[id].p;},0);addKit(ids,"Dark Spot Routine",Math.round(sub*.85));}else{ids.forEach(function(id){addItem(id,true);});toast(ids.length>1?ids.length+" products added to your bag":BY[ids[0]].n+" added to your bag");open("drawer");}});
fbtUpdate();
$("#city").addEventListener("change",eta);eta();setInterval(eta,60000);
/* Copy WhatsApp number */
$("#copyWa").addEventListener("click",function(){var t=$("#wa").textContent,btn=this;function ok(){btn.textContent="Copied";setTimeout(function(){btn.textContent="Copy number";},1800);}
  function fallback(){var r=document.createRange();r.selectNodeContents($("#wa"));var s=getSelection();s.removeAllRanges();s.addRange(r);btn.textContent="Selected, press copy";}
  try{navigator.clipboard.writeText(t).then(ok,fallback);}catch(err){fallback();}});
/* Sticky add-to-bag */
var sticky=$("#stickyAtc"),mainBtn=$("#addMain");
if("IntersectionObserver" in window){new IntersectionObserver(function(en){var on=!en[0].isIntersecting&&en[0].boundingClientRect.top<0;sticky.classList.toggle("is-on",on);sticky.setAttribute("aria-hidden",!on);$("button",sticky).tabIndex=on?0:-1;}).observe(mainBtn);}
/* Review form */
$("#rvForm").addEventListener("submit",function(ev){ev.preventDefault();var msg=$("#rvMsg"),st=$('input[name="stars"]:checked'),name=$("#rvName").value.trim(),txt=$("#rvText").value.trim(),ord=$("#rvOrder").value.trim();
  msg.className="formmsg err";
  if(!st){msg.textContent="Choose a star rating from 1 to 5.";return;}
  if(txt.length<20){msg.textContent="Write at least 20 characters so others can learn from your review.";$("#rvText").focus();return;}
  if(!/^NX-\d{4,}$/i.test(ord)){msg.textContent="Enter your order number, like NX-104829. It's on your parcel sticker.";$("#rvOrder").focus();return;}
  msg.className="formmsg ok";msg.textContent="Thank you"+(name?", "+name:"")+". We'll check your order and publish your review within 2 days.";this.reset();});
'''
prod=doc('AXIS-Y Dark Spot Correcting Glow Serum Price in Pakistan | Nexus Beauty',
 'Buy original AXIS-Y Dark Spot Correcting Glow Serum in Pakistan: 5 ml mini Rs 799, 50 ml Rs 4,049. 5% niacinamide for dark spots and acne marks. Batch and expiry on every order. Cash on delivery.',
 BASE+'products/axis-y-dark-spot-correcting-glow-serum',prod_ld,prod_body,PROD_JS,'product')
open('/home/user/cloude/nexus-beauty/product.html','w').write(prod)
print(len(shop),len(prod))

# ================= SOLUTIONS (all problem routines) =================
SOL_FAQ=[('How long before I see results?','Brightening and acne routines usually show changes in 4–8 weeks. Hair fall routines take 8–12 weeks because hair grows slowly. Use the routine every day and wear sunscreen every morning.'),
('Can I use two kits together?','Yes, but start one at a time. Add the second after 2 weeks so you know how your skin reacts. Use only one exfoliating product and one strong serum per night.'),
('Do I have to buy the whole kit?','No. Untick anything you already own. You get 15% off whenever you choose 3 or more products from a kit.'),
('Are these routines medical advice?','No. They are general routines built from products that sell well in Pakistan. For severe acne, sudden hair loss or melasma, please see a dermatologist.')]
jump=''.join(f'<a href="#rg-{r["key"]}">{e(r["name"])}</a>' for r in N.RG)
sol_body=f'''
<div class="shop-head">
 <div class="wrap">
  <nav class="crumbs" aria-label="Breadcrumb"><ol><li><a href="index.html">Home</a></li><li aria-current="page">Routines</li></ol></nav>
  <h1>Skincare and hair care routines that work in Pakistan</h1>
  <p class="lede">Pick your problem. Each kit lists every product you need in the order you use them, how to apply each one, and what to avoid. Choose 3 or more products and get 15% off.</p>
  <div class="jump" style="margin-top:20px">{jump}</div>
 </div>
</div>
<div class="wrap" style="display:grid;gap:28px;padding-block:32px 64px">{''.join(N.rg_block(r,heading='h2') for r in N.RG)}</div>
<section class="section section--tint" aria-labelledby="sq-h">
 <div class="wrap" style="display:grid;gap:24px">
  <div><p class="eyebrow">Before you start</p><h2 class="h2" id="sq-h">Routine questions</h2></div>
  <div class="faq">{''.join(f'<details><summary>{e(q)}</summary><p>{e(a)}</p></details>' for q,a in SOL_FAQ)}</div>
 </div>
</section>
<section class="section" aria-labelledby="sv-h">
 <div class="wrap">
  <div class="sec-head"><div><p class="eyebrow">Watch and learn</p><h2 class="h2" id="sv-h">How-to videos</h2></div></div>
  <div class="videos">{N.video_cards(N.VIDEOS)}</div>
 </div>
</section>'''
sol_ld=[{"@context":"https://schema.org","@type":"CollectionPage","name":"Skincare and hair care routines","url":BASE+"routines","isPartOf":{"@type":"WebSite","name":"Nexus Beauty","url":BASE}},
 {"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":BASE},{"@type":"ListItem","position":2,"name":"Routines","item":BASE+"routines"}]},
 {"@context":"https://schema.org","@type":"FAQPage","mainEntity":[{"@type":"Question","name":q,"acceptedAnswer":{"@type":"Answer","text":a}} for q,a in SOL_FAQ]}]+[
 {"@context":"https://schema.org","@type":"HowTo","name":r['name'],"description":r['who'],"step":[{"@type":"HowToStep","position":i+1,"name":f"{st[1]} ({st[0]})","text":f"{BY[st[2]]['brand']} {BY[st[2]]['name']}: {st[3]}"} for i,st in enumerate(r['steps'])]} for r in N.RG]
sol=doc('Brightening, Acne &amp; Hair Fall Routines for Pakistan | Nexus Beauty',
 'Complete skincare and hair care kits for brightening, acne, hair fall and dry skin, with step-by-step guides. Original products, 15% off 3 or more, cash on delivery across Pakistan.',
 BASE+'routines',sol_ld,sol_body,'')
open('/home/user/cloude/nexus-beauty/solutions.html','w').write(sol)

# ================= BRAND page template (Rederm) =================
bprods=[p for p in P if p['brand']=='Rederm']
others=[('Estelin','Sun care and vitamin C'),('Neophar','Everyday sunscreen'),('Jenpharm','Hair growth care'),('B&B Dermaceuticals','Gel SPF and body wash')]
B_FAQ=[('Is Rederm a Pakistani brand?','Yes. Rederm is a Pakistani dermatology brand known for hair fall, dandruff and acne care. Its products are widely recommended by dermatologists and sold in pharmacies.'),
('Is Rederm on Nexus Beauty original?','Yes. We buy Rederm from its authorised distributor. Your parcel sticker shows the batch number and expiry date of every item.'),
('Which Rederm product should I start with?','For hair fall with dandruff, start with Tressfix shampoo. For oily, acne-prone skin, start with Clariderm Face Wash.')]
brand_body=f'''
<div class="bhero">
 <div class="wrap">
  <nav class="crumbs" aria-label="Breadcrumb"><ol><li><a href="index.html">Home</a></li><li><a href="index.html#brands">Brands</a></li><li aria-current="page">Rederm</li></ol></nav>
  <div class="bhero__grid">
   <span class="bhero__mark" aria-hidden="true">R</span>
   <div style="display:grid;gap:8px">
    <p class="eyebrow">Pakistani derm brand</p>
    <h1>Rederm</h1>
    <p class="lede">Dermatologist-developed hair and skin care made in Pakistan. Best known for Tressfix, Pakistan's most-reviewed hair fall shampoo online.</p>
    <div class="bfacts"><span>Made in Pakistan</span><span>Derm brand</span><span>From the authorised distributor</span><span>{len(bprods)} products</span></div>
   </div>
  </div>
 </div>
</div>
<section class="section" aria-labelledby="bp-h">
 <div class="wrap">
  <div class="sec-head"><div><p class="eyebrow">Shop Rederm</p><h2 class="h2" id="bp-h">All Rederm products</h2></div><a class="link" href="shop.html#derm">All derm brands</a></div>
  <div class="grid">{''.join(N.card(p) for p in bprods)}</div>
 </div>
</section>
<section class="section section--tint" aria-labelledby="bk-h">
 <div class="wrap">
  <div class="sec-head"><div><p class="eyebrow">Complete the routine</p><h2 class="h2" id="bk-h">Rederm in a full routine</h2></div><a class="link" href="solutions.html">All routines</a></div>
  {N.rg_block(N.RGBY['hairfall'])}
 </div>
</section>
<section class="section" aria-labelledby="bf-h">
 <div class="wrap" style="display:grid;gap:24px">
  <div><p class="eyebrow">About the brand</p><h2 class="h2" id="bf-h">Rederm questions</h2></div>
  <div class="faq">{''.join(f'<details><summary>{e(q)}</summary><p>{e(a)}</p></details>' for q,a in B_FAQ)}</div>
 </div>
</section>
<section class="section section--tint" aria-labelledby="bo-h">
 <div class="wrap">
  <div class="sec-head"><div><p class="eyebrow">You may also like</p><h2 class="h2" id="bo-h">More Pakistani derm brands</h2></div></div>
  <div class="brand-grid">{''.join(f'<a class="brand" href="brand.html"><span class="brand__mark" aria-hidden="true">{e(N.initials(n))}</span><b>{e(n)}</b><span>{e(d)}</span></a>' for n,d in others)}</div>
 </div>
</section>'''
brand_ld=[{"@context":"https://schema.org","@type":"CollectionPage","name":"Rederm products","url":BASE+"brands/rederm","about":{"@type":"Brand","name":"Rederm"},
  "mainEntity":{"@type":"ItemList","itemListElement":[{"@type":"ListItem","position":i+1,"name":p['brand']+' '+p['name'],"url":BASE+"products/"+p['id']} for i,p in enumerate(bprods)]}},
 {"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":BASE},{"@type":"ListItem","position":2,"name":"Brands","item":BASE+"brands"},{"@type":"ListItem","position":3,"name":"Rederm","item":BASE+"brands/rederm"}]},
 {"@context":"https://schema.org","@type":"FAQPage","mainEntity":[{"@type":"Question","name":q,"acceptedAnswer":{"@type":"Answer","text":a}} for q,a in B_FAQ]}]
brand=doc('Rederm Products Price in Pakistan: Tressfix Shampoo &amp; More | Nexus Beauty',
 'Buy original Rederm products in Pakistan: Tressfix Hairfall &amp; Anti-Dandruff Shampoo and Clariderm Face Wash. From the authorised distributor, batch and expiry on every order. Cash on delivery.',
 BASE+'brands/rederm',brand_ld,brand_body,'')
open('/home/user/cloude/nexus-beauty/brand.html','w').write(brand)
print('solutions',len(sol),'brand',len(brand))
