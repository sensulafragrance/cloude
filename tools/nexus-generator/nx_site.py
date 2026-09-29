import json, html, io, os, contextlib, datetime
with contextlib.redirect_stdout(io.StringIO()):
    import nexus as N
    import nx_pages as NP
from nexus_css import CSS
from site_extra import EXTRA_CSS
from site_js import SITE_JS
e=html.escape; rs=N.rs; ic=N.ic; shape=N.shape; P=N.P; BY=N.BYID; T=N.T
OUT='/home/user/cloude/nexus-beauty/'
BASE='https://nexusbeauty.pk/'
os.makedirs(OUT+'assets/css',exist_ok=True); os.makedirs(OUT+'assets/js',exist_ok=True)
HEADER=N.BODY[N.BODY.index('<a class="skip"'):N.BODY.index('<main id="main">')]
FOOTER=N.BODY[N.BODY.index('<footer class="footer">'):]
PAGES=[]   # (file, title, section) for sitemap

# ---------- shared JS ----------
JSEND='\nrender();\n})();\n'
core=N.JS[:-len(JSEND)] if N.JS.endswith(JSEND) else None
assert core
shop_js=NP.SHOP_JS
for a,b in [('var tok=(location.hash||"").replace("#","");setOnly(NAMES[tok]?tok:"all");','var PRESET=sgrid.dataset.preset||"";var tok=PRESET||(location.hash||"").replace("#","");setOnly(NAMES[tok]?tok:"all");'),
            ('var title=NAMES[q];','if(!sgrid.dataset.preset){var title=NAMES[q];'),
            ('$("#crumbCur").textContent=title&&q!=="all"?title:"Shop all";','$("#crumbCur").textContent=title&&q!=="all"?title:"Shop all";}'),
            ('if(ev.target.closest("[data-clear]")){setOnly("all");return;}','if(ev.target.closest("[data-clear]")){setOnly(sgrid.dataset.preset||"all");return;}')]:
    assert a in shop_js,a; shop_js=shop_js.replace(a,b)
prod_js=NP.PROD_JS.replace('var stage=$("#stage")','pushRecent("axis-y-5");\nvar stage=$("#stage")',1)
JS=core+'\nif($("#shopGrid")){'+shop_js+'\n}\nif($("#stage")){'+prod_js+'\n}\n'+SITE_JS+JSEND
open(OUT+'assets/js/nexus.js','w').write(JS)
open(OUT+'assets/css/nexus.css','w').write(CSS+EXTRA_CSS)

def doc(file,title,desc,body,ld=(),og='website',robots='index, follow, max-image-preview:large',section='Shop',canon=None):
    canon=canon or BASE+('' if file=='index.html' else file.replace('.html',''))
    lds=''.join(f'<script type="application/ld+json">{json.dumps(x,ensure_ascii=False)}</script>\n' for x in ld)
    page=f'''<!doctype html>
<html lang="en-PK">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>{title}</title>
<meta name="description" content="{desc}">
<link rel="canonical" href="{canon}">
<meta name="robots" content="{robots}">
<meta name="theme-color" content="#8c2350">
<meta property="og:type" content="{og}"><meta property="og:site_name" content="Nexus Beauty"><meta property="og:title" content="{title}"><meta property="og:description" content="{desc}"><meta property="og:url" content="{canon}"><meta property="og:locale" content="en_PK"><meta property="og:image" content="{BASE}images/og-nexus-beauty.jpg">
<meta name="twitter:card" content="summary_large_image">
{N.FONTS}
<link rel="stylesheet" href="assets/css/nexus.css">
{lds}</head>
<body>
{HEADER}<main id="main">
{body}
</main>
{FOOTER}<script src="assets/js/nexus.js"></script>
</body>
</html>
'''
    open(OUT+file,'w').write(page)
    if 'noindex' not in robots: PAGES.append((file,title.split(' | ')[0].replace('&amp;','&'),section))
def crumbs(items):
    li=''.join(f'<li><a href="{h}">{e(n)}</a></li>' if h else f'<li aria-current="page">{e(n)}</li>' for n,h in items)
    return f'<nav class="crumbs" aria-label="Breadcrumb"><ol>{li}</ol></nav>'
def bc_ld(items):
    return {"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":i+1,"name":n,"item":BASE+(h or '').replace('.html','').replace('index','')} for i,(n,h) in enumerate(items)]}
def phead(items,h1,lede,extra=''):
    return f'<div class="phead"><div class="wrap">{crumbs(items)}<h1>{h1}</h1><p class="lede">{lede}</p>{extra}</div></div>'
def faq_block(qs,title='Questions',eyebrow='FAQ',tint=True,hid='faqb'):
    return f'''<section class="section{' section--tint' if tint else ''}" aria-labelledby="{hid}"><div class="wrap" style="display:grid;gap:24px"><div><p class="eyebrow">{e(eyebrow)}</p><h2 class="h2" id="{hid}">{e(title)}</h2></div><div class="faq">{''.join(f'<details><summary>{e(q)}</summary><p>{e(a)}</p></details>' for q,a in qs)}</div></div></section>'''
def faq_ld(qs): return {"@context":"https://schema.org","@type":"FAQPage","mainEntity":[{"@type":"Question","name":q,"acceptedAnswer":{"@type":"Answer","text":a}} for q,a in qs]}
def pcall(pid,note):
    p=BY[pid]
    return f'<div class="pcall"><span class="thumb" style="--tone:{p["tone"]};--bg:{p["bg"]}">{shape(p["shape"])}</span><span><b>{e(p["brand"])} {e(p["name"])}</b><small>{e(note)} · {rs(p["price"])}</small></span><button class="btn btn--d" type="button" data-add="{pid}">Add to bag</button></div>'
RECENT='<section class="section" data-recent-wrap aria-labelledby="rec-h"><div class="wrap"><div class="sec-head"><div><p class="eyebrow">Pick up where you left off</p><h2 class="h2" id="rec-h">Recently viewed</h2></div></div><div class="minis" data-recent></div></div></section>'
WEBSITE_LD={"@context":"https://schema.org","@type":"WebSite","name":"Nexus Beauty","url":BASE,"potentialAction":{"@type":"SearchAction","target":BASE+"search?q={search_term_string}","query-input":"required name=search_term_string"}}
ORG_LD={"@context":"https://schema.org","@type":"OnlineStore","name":"Nexus Beauty","url":BASE,"logo":BASE+"images/logo.png","slogan":"Beauty, checked.","areaServed":"PK","currenciesAccepted":"PKR","paymentAccepted":"Cash on delivery, JazzCash, Easypaisa, Card","contactPoint":{"@type":"ContactPoint","telephone":"+92-300-0000000","contactType":"customer service","areaServed":"PK","availableLanguage":["en","ur"]}}

# ================= HOME =================
home=N.BODY[N.BODY.index('<main id="main">')+len('<main id="main">'):N.BODY.index('</main>')]
home=home.replace(' · Coming soon</p></article>',' · <a href="blog-post.html" style="text-decoration:underline">Read the guide</a></p></article>')
home=home.replace('<div class="sec-head"><div><p class="eyebrow">The Nexus journal</p><h2 class="h2" id="jr-h">Guides for better routines</h2></div></div>','<div class="sec-head"><div><p class="eyebrow">The Nexus journal</p><h2 class="h2" id="jr-h">Guides for better routines</h2></div><a class="link" href="blog.html">All articles</a></div>')
home=home.replace('<section class="section" aria-labelledby="faq-h">',RECENT+'\n<section class="section" aria-labelledby="faq-h">',1)
doc('index.html','Nexus Beauty | Original Skincare, Hair Care &amp; Fragrance in Pakistan',
 'Original skincare, sunscreen, hair care, fragrance and makeup delivered across Pakistan. Batch and expiry on every order. Cash on delivery.',home,[ORG_LD,WEBSITE_LD]+N.ld[1:],section='Main')

# ================= SHOP + COLLECTIONS =================
doc('shop.html','Buy Original Skincare, Sunscreen &amp; Hair Care Online in Pakistan | Nexus Beauty',
 f'Shop {len(P)} original beauty products: sunscreen, serums, hair fall shampoos, mists and makeup. Pakistani derm brands and Korean favourites. Batch and expiry on every order. Cash on delivery.',NP.shop_body+RECENT,NP.shop_ld,section='Shop')
COLL=[
 ('skincare','collection-skincare.html','Skincare','Original skincare in Pakistan','Serums, moisturisers, cleansers and scrubs for dark spots, acne, dullness and dry skin, from Korean favourites and Pakistani derm brands.',
  [('Which serum is best for dark spots?','Start with the AXIS-Y Dark Spot Correcting Glow Serum 5 ml mini. It has 5% niacinamide and is Pakistan\'s most-reviewed serum online. Always pair it with sunscreen.'),('What moisturiser suits oily skin in summer?','A light gel like Pond\'s Super Light Gel. Dr. Althea 345 Relief Cream suits dry or sensitive skin better.')]),
 ('sun','collection-sun-care.html','Sun care','Sunscreen in Pakistan: SPF 50 to SPF 70','Creams, tinted formulas, sticks and sun serums that stay light in Pakistan\'s heat and humidity.',
  [('Which SPF should I use in Pakistan?','Use SPF 50 or higher with broad UVA protection (PA+++ or PA++++) every day, even indoors near windows.'),('How often should I reapply?','Every 2–3 hours when you\'re outdoors, and after sweating. A sun stick makes reapplying over makeup easy.')]),
 ('hair','collection-hair-care.html','Hair care','Hair fall shampoos, masks and treatments','Derm-brand shampoos for hair fall and dandruff, plus rosemary and repair masks.',
  [('What is the best shampoo for hair fall?','Rederm Tressfix for hair fall with dandruff, or Jenpharm Anagrow Biotin for hair fall without dandruff. Give either 6–8 weeks.'),('How often should I use a hair mask?','Once a week on the lengths and ends, for 10–15 minutes.')]),
 ('body','collection-body-care.html','Body care','Body lotions, gel oils and body polish','Body care with actives for glowing, smooth skin from shoulders to feet.',
  [('How do I get smoother skin on elbows and knees?','Use a body polish twice a week in the shower, then a lotion like Vaseline Gluta-Hya on damp skin every day.')]),
 ('fragrance','collection-fragrance.html','Fragrance','Hair &amp; body mists and body sprays','Our own Koh-e-Noor mists, sweet body sprays and scents that last in the heat.',
  [('What is the difference between a body spray and a mist?','A body mist has more fragrance oil, so it smells richer and lasts longer. Koh-e-Noor mists last 3–4 hours on skin and longer on hair and clothes.')]),
 ('makeup','collection-makeup.html','Makeup','Lipsticks and everyday makeup','Pakistan\'s best-selling matte lipsticks and everyday colour.',
  [('Which lipstick sells most in Pakistan?','Medora Matte Lipstick is the most-reviewed lipstick online in Pakistan, at around Rs 300.')]),
 ('men','collection-mens-grooming.html',"Men's grooming","Men's face wash and beard care",'Simple, proven basics for oily skin and beards.',
  [('What should a basic men\'s routine include?','A face wash for your skin type, a light moisturiser and sunscreen in the morning. Add beard oil if you have a beard.')]),
 ('personal','collection-personal-care.html','Personal care','Personal care essentials','Razors and everyday hygiene essentials that sell every month.',
  [('Do you deliver personal care items discreetly?','Yes. Every parcel is plain and sealed.')]),
 ('derm','collection-pakistani-derm.html','Pakistani derm brands','Pakistani dermatologist brands','Rederm, Estelin, Neophar and Jenpharm: the brands Pakistani dermatologists recommend, in one place.',
  [('Are derm brands better than regular brands?','They are formulated for specific skin and hair problems and are often recommended by dermatologists. Choose by your concern, not just the label.')]),
 ('minis','collection-minis.html','Minis &amp; under Rs 1,000','Minis and products under Rs 1,000','Try before you commit. Minis and everyday favourites for under Rs 1,000.',
  [('Why buy a mini?','A mini lets you test a product on your skin for about 2 weeks before paying for the full size.')]),
 ('new','collection-new-arrivals.html','New arrivals','New arrivals: PDRN, sun sticks and more','Trending in the US and Korea, and only just arriving in Pakistan.',
  [('What is PDRN?','A skincare ingredient made from salmon DNA, popular in Korea for hydration and skin repair. It\'s new to Pakistan, so start with one product and patch test.')]),
 ('own','collection-koh-e-noor.html','Koh-e-Noor by Nexus Beauty','Koh-e-Noor hair &amp; body mists','Our own label: 100 ml of long-lasting scent for hair, skin and clothes, made in Pakistan.',
  [('How long do Koh-e-Noor mists last?','About 3–4 hours on skin and 6–8 hours on hair and clothes.')]),
]
FILEOF={k:f for k,f,*_ in COLL}
catlinks='<a class="chip" href="shop.html">All</a>'+''.join(f'<a class="chip" href="{f}">{n}</a>' for k,f,n,*_ in COLL)
import re
for k,f,name,h1,intro,qs in COLL:
    b=NP.shop_body
    cur=catlinks.replace('"'+f+'">','"'+f+'" aria-current="page" style="background:var(--ink);color:#fff;border-color:var(--ink)">')
    b=re.sub(r'<div class="catbar" role="group" aria-label="Quick categories">.*?</div>\n',lambda m:'<div class="catbar" aria-label="Collections">'+cur+'</div>\n',b,count=1,flags=re.S)
    b=b.replace('<li aria-current="page" id="crumbCur">Shop all</li>',f'<li><a href="collections.html">Collections</a></li><li aria-current="page" id="crumbCur">{name}</li>',1)
    b=b.replace('<h1 id="shopTitle">Shop original beauty products</h1>',f'<h1 id="shopTitle">{h1}</h1>',1)
    _intro='<p class="lede" id="shopIntro">'+intro+' Batch and expiry on every order, cash on delivery across Pakistan.</p>'
    b=re.sub(r'<p class="lede" id="shopIntro">.*?</p>',lambda m:_intro,b,count=1,flags=re.S)
    b=b.replace('<div class="grid" id="shopGrid">',f'<div class="grid" id="shopGrid" data-preset="{k}">',1)
    _fq=faq_block(qs,f'{name.replace("&amp;","&")}: questions','Buying guide',False,'sfaq-h')
    b=re.sub(r'<section class="section" aria-labelledby="sfaq-h">.*?</section>',lambda m:_fq,b,count=1,flags=re.S)
    b=re.sub(r'<section class="section" aria-labelledby="seo-h">.*?</section>','',b,count=1,flags=re.S)
    items=[('Home','index.html'),('Collections','collections.html'),(name.replace('&amp;','&'),None)]
    doc(f,f'{h1} | Nexus Beauty',f'{intro.replace("&amp;","&")} Original products, batch and expiry on every order, cash on delivery across Pakistan.'.replace('"',"'"),b,
        [{"@context":"https://schema.org","@type":"CollectionPage","name":h1.replace('&amp;','&'),"url":BASE+f.replace('.html',''),"description":intro.replace('&amp;','&')},bc_ld(items),faq_ld(qs)],section='Collections')
tiles=''.join(f'<a class="prob" href="{f}"><span class="prob__art" style="--bg:{T[t][1]}"><svg viewBox="0 0 100 160" aria-hidden="true" style="color:{T[t][0]}"><use href="#i-{s}"/></svg></span><span class="prob__body"><h3>{n}</h3><p>{e(intro[:90].rsplit(" ",1)[0])}…</p><span class="link">Shop {n}</span></span></a>' for (k,f,n,h1,intro,qs),(t,s) in zip(COLL,[('yel','dropper'),('sun','tube'),('hair','bottle'),('body','pump'),('rose','perfume'),('lip','lipstick'),('men','tube'),('grn','jar'),('blu','bottle'),('pink','dropper'),('pink','dropper'),('vp','perfume')]))
doc('collections.html','All Collections | Nexus Beauty','Browse every Nexus Beauty collection: skincare, sun care, hair care, body care, fragrance, makeup, Pakistani derm brands, minis and new arrivals.',
 phead([('Home','index.html'),('Collections',None)],'All collections','Every way to shop Nexus Beauty, from sunscreen to Pakistani derm brands.')+f'<section class="section"><div class="wrap"><div class="probs">{tiles}</div></div></section>'+
 f'<section class="section section--tint"><div class="wrap"><div class="sec-head"><div><p class="eyebrow">Shop by problem</p><h2 class="h2">Complete routines</h2></div><a class="link" href="solutions.html">See all routines</a></div><div class="jump">'+''.join(f'<a href="solutions.html#rg-{r["key"]}">{e(r["name"])}</a>' for r in N.RG)+'</div></div></section>',
 [bc_ld([('Home','index.html'),('Collections',None)])],section='Collections')

# ================= PRODUCT, ROUTINES, BRAND =================
pb=NP.prod_body.replace('<section class="section" aria-labelledby="rel-h">',RECENT.replace('rec-h','rec-h2')+'\n<section class="section" aria-labelledby="rel-h">',1)
pb=pb.replace('<a href="product.html" style="text-decoration:underline">Anua Azelaic Acid</a>','<a href="collection-skincare.html" style="text-decoration:underline">Anua Azelaic Acid</a>')
pb=pb.replace('<a href="#faq">6 questions answered</a>','<a href="#faq">6 questions answered</a><a href="vs-sunscreen.html">Which sunscreen to pair?</a>',1)
doc('product.html','AXIS-Y Dark Spot Correcting Glow Serum Price in Pakistan | Nexus Beauty',
 'Buy original AXIS-Y Dark Spot Correcting Glow Serum in Pakistan: 5 ml mini Rs 799, 50 ml Rs 4,049. 5% niacinamide for dark spots and acne marks. Batch and expiry on every order. Cash on delivery.',
 pb,NP.prod_ld,og='product',section='Products',canon=BASE+'products/axis-y-dark-spot-correcting-glow-serum')
doc('solutions.html','Brightening, Acne &amp; Hair Fall Routines for Pakistan | Nexus Beauty',
 'Complete skincare and hair care kits for brightening, acne, hair fall and dry skin, with step-by-step guides. Original products, 15% off 3 or more, cash on delivery across Pakistan.',NP.sol_body,NP.sol_ld,section='Shop')
bb=NP.brand_body.replace('<a class="link" href="shop.html#derm">All derm brands</a>','<a class="link" href="collection-pakistani-derm.html">All derm brands</a>').replace('<li><a href="index.html#brands">Brands</a></li>','<li><a href="brands.html">Brands</a></li>')
doc('brand.html','Rederm Products Price in Pakistan: Tressfix Shampoo &amp; More | Nexus Beauty',
 'Buy original Rederm products in Pakistan: Tressfix Hairfall &amp; Anti-Dandruff Shampoo and Clariderm Face Wash. From the authorised distributor, batch and expiry on every order. Cash on delivery.',bb,NP.brand_ld,section='Brands',canon=BASE+'brands/rederm')

# ================= BRANDS A–Z =================
allb=sorted(N.PK+N.INTL,key=lambda x:x[0].lower().lstrip("'"))
groups={}
for n,d in allb: groups.setdefault(n[0].upper() if n[0].isalpha() else '#',[]).append((n,d))
letters=''.join(f'<a href="#az-{L}">{L}</a>' for L in groups)
az=''.join(f'<div class="az-group" id="az-{L}"><h2>{L}</h2><div class="brand-grid">'+''.join(f'<a class="brand{" brand--own" if n=="Koh-e-Noor" else ""}" href="{"collection-koh-e-noor.html" if n=="Koh-e-Noor" else "brand.html"}" data-name="{e(n.lower())}"><span class="brand__mark" aria-hidden="true">{e(N.initials(n))}</span><b>{e(n)}</b><span>{e(d)}</span></a>' for n,d in g)+'</div></div>' for L,g in groups.items())
doc('brands.html','All Brands A–Z | Nexus Beauty',f'Shop {len(allb)} original beauty brands in Pakistan, from Rederm and Estelin to Beauty of Joseon and AXIS-Y. Bought from authorised distributors only.',
 phead([('Home','index.html'),('Brands',None)],'Brands A–Z',f'{len(allb)} brands, all bought from the brand or its authorised distributor.',
  f'<div class="sfield" style="max-width:420px;margin-top:18px"><label class="sr-only" for="azSearch">Find a brand</label>{ic("search")}<input id="azSearch" type="search" placeholder="Find a brand" autocomplete="off"></div><nav class="az-letters" aria-label="Jump to letter">{letters}</nav>')+
 f'<section class="section" style="padding-top:8px"><div class="wrap">{az}</div></section>',[bc_ld([('Home','index.html'),('Brands',None)])],section='Brands')

# ================= BLOG =================
POSTS=[('blog-post.html','Buying guides','Best sunscreen for oily skin in Pakistan (2026 guide)','How to choose an SPF that doesn\'t feel greasy in the heat, and our five picks for every budget.','24 Sep 2026','7 min','sun','tube',True),
 ('blog-post.html','Ingredient guides','PDRN vs niacinamide: what to know','What the new biotech ingredient does, and who actually needs it.','20 Sep 2026','6 min','pink','dropper',False),
 ('blog-post.html','Skin science','How to build a routine for Pakistan\'s heat','Light gels, the right sunscreen and when to skip heavy creams.','16 Sep 2026','5 min','blu','jar',False),
 ('blog-post.html','Hair care','Hair fall: what shampoo can and can\'t do','When a derm shampoo helps, and when you should see a doctor.','12 Sep 2026','6 min','hair','bottle',False),
 ('blog-post.html','Fragrance','From body spray to perfume','How to layer sweet, oud and musk scents so they last all day.','8 Sep 2026','4 min','oud','perfume',False),
 ('blog-post.html','Buying guides','How to spot fake skincare in Pakistan','Six checks to make before you pay, from batch codes to prices that are too good.','4 Sep 2026','5 min','grn','jar',False),
 ('blog-post.html','Skin science','Fading acne marks: a realistic timeline','What changes in weeks 2, 6 and 12, and which ingredients help most.','31 Aug 2026','6 min','pink','dropper',False),
 ('blog-post.html','Ingredient guides','Vitamin C serums for beginners','How to start, what to pair it with, and how to keep it from going off.','27 Aug 2026','5 min','yel','dropper',False),
 ('blog-post.html','Hair care','Rosemary for hair: hype or help?','What the evidence says, and how to use rosemary products well.','23 Aug 2026','5 min','rosem','jar',False)]
BCATS=sorted({p[1] for p in POSTS})
def post_card(p):
    f,cat,t,ex,d,rt,tone,sh,_=p
    return f'<article class="post" data-cat="{e(cat)}"><div class="post__art" style="--bg:{T[tone][1]};--tone:{T[tone][0]}">{shape(sh)}</div><p class="eyebrow">{e(cat)}</p><h3><a class="stretch" href="{f}">{e(t)}</a></h3><p>{e(ex)}</p><p class="meta">{d} · {rt} read</p></article>'
feat=POSTS[0]
blog_body=phead([('Home','index.html'),('Journal',None)],'The Nexus journal','Honest guides to skincare, hair care and fragrance for Pakistan\'s climate and budgets.')+f'''
<section class="section" style="padding-top:28px"><div class="wrap">
 <article class="blog-feat post" data-cat="{feat[1]}"><div class="post__art" style="--bg:{T[feat[6]][1]};--tone:{T[feat[6]][0]}">{shape(feat[7])}</div><div style="display:grid;gap:10px"><p class="eyebrow">Featured · {e(feat[1])}</p><h2><a class="stretch" href="{feat[0]}">{e(feat[2])}</a></h2><p class="lede">{e(feat[3])}</p><p class="meta">{feat[4]} · {feat[5]} read</p></div></article>
 <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:space-between;align-items:center;margin-bottom:22px">
  <div class="tabs" role="group" aria-label="Filter by topic" style="margin:0"><button class="chip" type="button" data-bcat="all" aria-pressed="true">All</button>{''.join(f'<button class="chip" type="button" data-bcat="{e(c)}" aria-pressed="false">{e(c)}</button>' for c in BCATS)}</div>
  <div class="sfield" style="min-width:min(100%,280px)"><label class="sr-only" for="blogQ">Search articles</label>{ic('search')}<input id="blogQ" type="search" placeholder="Search articles"></div>
 </div>
 <div class="posts" id="blogGrid">{''.join(post_card(p) for p in POSTS[1:])}</div>
 <div class="empty-state" id="blogEmpty" hidden><b>No articles match.</b><p>Try another topic or word.</p></div>
</div></section>
<section class="section section--tint"><div class="wrap signup" style="background:var(--berry)"><div><h2 class="h2">New guides every week</h2><p>One email a week with our newest guides and offers. Get Rs 200 off your first order.</p></div><form data-signup novalidate><label class="sr-only" for="blogEmail">Email address</label><input id="blogEmail" type="email" placeholder="Your email address" autocomplete="email" required><button class="btn btn--d" type="submit">Subscribe</button><small role="status"></small></form></div></section>'''
doc('blog.html','Skincare &amp; Beauty Guides for Pakistan | Nexus Beauty Journal','Honest guides to sunscreen, acne, hair fall, ingredients and fragrance for Pakistan\'s climate. Written by the Nexus Beauty team.',blog_body,
 [{"@context":"https://schema.org","@type":"Blog","name":"The Nexus journal","url":BASE+"blog","blogPost":[{"@type":"BlogPosting","headline":p[2],"url":BASE+"blog/"+str(i)} for i,p in enumerate(POSTS)]},bc_ld([('Home','index.html'),('Journal',None)])],section='Journal')

ART_FAQ=[('Can I skip sunscreen on cloudy days?','No. Most UVA rays pass through clouds and windows, and UVA is what darkens spots and marks.'),
 ('Does sunscreen cause breakouts?','Heavy, oily formulas can. Choose a gel, fluid or "non-comedogenic" sunscreen and wash it off properly at night.'),
 ('Is SPF 70 much better than SPF 50?','Only slightly. SPF 50 blocks about 98% of UVB and SPF 70 a little more. Applying enough and reapplying matters more than the number.')]
toc=[('why','Why oily skin still needs SPF'),('what','What to look for'),('picks','Our five picks'),('how','How much and how often'),('mistakes','Common mistakes'),('faq-a','Questions')]
art=f'''<div class="wrap">{crumbs([('Home','index.html'),('Journal','blog.html'),('Best sunscreen for oily skin',None)])}</div>
<article class="wrap" style="padding-bottom:56px">
 <header style="display:grid;gap:12px;max-width:820px;margin-bottom:28px">
  <p class="eyebrow">Buying guide</p><h1 style="font-size:clamp(30px,4.4vw,50px)">Best sunscreen for oily skin in Pakistan (2026 guide)</h1>
  <p class="lede">How to choose an SPF that doesn't feel greasy in the heat, plus five picks for every budget.</p>
  <div class="byline"><span>By the Nexus Beauty team</span><span>Updated 24 September 2026</span><span>7 min read</span></div>
  <div class="share"><span>Share:</span><a href="https://wa.me/?text=Best%20sunscreen%20for%20oily%20skin%20in%20Pakistan" target="_blank" rel="noopener">WhatsApp</a><a href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fnexusbeauty.pk%2Fblog%2Fbest-sunscreen-oily-skin" target="_blank" rel="noopener">Facebook</a><button type="button" data-copy-link>Copy link</button></div>
 </header>
 <div class="layout2">
  <nav class="toc" aria-label="In this article"><p>In this article</p>{''.join(f'<a href="#{a}">{t}</a>' for a,t in toc)}</nav>
  <div class="prose">
   <h2 id="why">Why oily skin still needs SPF</h2>
   <p>Oily skin doesn't protect you from the sun. UV rays darken acne marks and dark spots, and they break down collagen. In Pakistan the UV index stays high for most of the year, even in winter in Karachi and Lahore.</p>
   <p>The problem is texture. Many sunscreens feel heavy and shiny on oily skin, so people skip them. The fix is choosing the right formula.</p>
   <h2 id="what">What to look for</h2>
   <ul><li><b>SPF 50 or higher</b> for daily use in Pakistan.</li><li><b>PA+++ or PA++++</b>, which means strong UVA protection. UVA causes pigmentation.</li><li><b>A gel, fluid or light cream texture</b> that sinks in within a minute.</li><li><b>"Non-comedogenic"</b> on the label if you get breakouts.</li><li><b>A tint</b> if white cast bothers you on brown skin.</li></ul>
   <h2 id="picks">Our five picks</h2>
   <h3>1. Best overall: Beauty of Joseon Relief Sun</h3><p>Light, dewy and without a white cast. It's the most-reviewed sunscreen on Pakistani beauty stores.</p>{pcall('boj-relief-sun','Best overall')}
   <h3>2. Best tinted: Estelin Tinted Sunscreen SPF 70</h3><p>A Pakistani derm brand. The tint evens out redness and marks, so it can replace a light foundation.</p>{pcall('estelin-70','Best tinted')}
   <h3>3. Best budget: Neophar Neobrella SPF 60</h3><p>Derm-brand protection for under Rs 1,000.</p>{pcall('neobrella-60','Best under Rs 1,000')}
   <h3>4. Best texture: SKIN1004 Hyalu-Cica Sun Serum</h3><p>Feels like a watery serum. Good under makeup on very oily skin.</p>{pcall('skin1004-sun','Lightest texture')}
   <h3>5. Best for reapplying: Tocobo Sun Stick</h3><p>Swipe it over makeup in the afternoon without messy hands.</p>{pcall('tocobo-stick','Best for reapplying')}
   <p>Can't choose between the top two? Read <a href="vs-sunscreen.html">Beauty of Joseon vs Estelin</a>.</p>
   <h2 id="how">How much and how often</h2>
   <ol><li>Use two finger-lengths for your face and neck, about a quarter teaspoon.</li><li>Apply it as the last step of your morning routine, 15 minutes before going out.</li><li>Reapply every 2–3 hours outdoors, and after sweating or washing your face.</li></ol>
   <div class="note">Tip: if your skin gets shiny by noon, blot with tissue first, then reapply. Don't pile sunscreen on top of oil.</div>
   <h2 id="mistakes">Common mistakes</h2>
   <ul><li>Using too little. Half the amount gives much less than half the protection.</li><li>Skipping it indoors. UVA passes through windows.</li><li>Relying on foundation with SPF. You'd need far more foundation than anyone wears.</li><li>Not removing it at night. Use a proper face wash, or cleanse twice.</li></ul>
   <h2 id="faq-a">Questions</h2>
   <div class="faq">{''.join(f'<details><summary>{e(q)}</summary><p>{e(a)}</p></details>' for q,a in ART_FAQ)}</div>
   <div class="box" style="margin-top:12px"><h3>About this guide</h3><p>Written by the Nexus Beauty team from product labels, customer questions and review data from Pakistani beauty stores. This is general advice, not medical advice. For melasma or severe acne, see a dermatologist.</p></div>
  </div>
 </div>
</article>
<section class="section section--tint"><div class="wrap"><div class="sec-head"><div><p class="eyebrow">Keep reading</p><h2 class="h2">More guides</h2></div><a class="link" href="blog.html">All articles</a></div><div class="posts">{''.join(post_card(p) for p in POSTS[1:4])}</div></div></section>'''
doc('blog-post.html','Best Sunscreen for Oily Skin in Pakistan (2026 Guide) | Nexus Beauty','How to choose a sunscreen that doesn\'t feel greasy in Pakistan\'s heat, plus five picks from Rs 930: Beauty of Joseon, Estelin, Neobrella, SKIN1004 and Tocobo.',art,
 [{"@context":"https://schema.org","@type":"BlogPosting","headline":"Best sunscreen for oily skin in Pakistan (2026 guide)","datePublished":"2026-09-24","dateModified":"2026-09-24","author":{"@type":"Organization","name":"Nexus Beauty"},"publisher":{"@type":"Organization","name":"Nexus Beauty"},"mainEntityOfPage":BASE+"blog/best-sunscreen-oily-skin"},bc_ld([('Home','index.html'),('Journal','blog.html'),('Best sunscreen for oily skin',None)]),faq_ld(ART_FAQ)],og='article',section='Journal',canon=BASE+'blog/best-sunscreen-oily-skin')

# ================= VS pages =================
def vs_page(file,a,b,title,lede,rows,verdict_a,verdict_b,qs,crumb):
    pa,pb_=BY[a],BY[b]
    def side(p): return f'<div class="vs__p"><span class="card__media" style="--tone:{p["tone"]};--bg:{p["bg"]}">{shape(p["shape"])}</span><p class="card__brand">{e(p["brand"])}</p><h2>{e(p["name"])}</h2><p class="price">{rs(p["price"])}</p><button class="btn btn--d btn--block" type="button" data-add="{p["id"]}">Add to bag</button></div>'
    tbl='<div class="tbl"><table><thead><tr><th scope="col">&nbsp;</th><th scope="col">'+e(pa['brand'])+'</th><th scope="col">'+e(pb_['brand'])+'</th></tr></thead><tbody>'+''.join(f'<tr><td>{e(r[0])}</td><td>{r[1]}</td><td>{r[2]}</td></tr>' for r in rows)+'</tbody></table></div>'
    body=phead([('Home','index.html'),('Compare','compare.html'),(crumb,None)],title,lede)+f'''
<section class="section" style="padding-top:28px"><div class="wrap" style="display:grid;gap:28px">
 <div class="vs">{side(pa)}<span class="vs__x" aria-hidden="true">VS</span>{side(pb_)}</div>
 <div><h2 class="h2" style="margin-bottom:14px">Side by side</h2>{tbl}<p style="font-size:13px;color:var(--ink-3);margin-top:8px">Review counts are from Highfy.pk, Pakistan's largest online beauty store, on 27 September 2026. Prices are Nexus Beauty prices.</p></div>
 <div class="cards2"><div class="box"><p class="eyebrow">Choose {e(pa["brand"])} if</p><ul>{''.join(f'<li>{e(x)}</li>' for x in verdict_a)}</ul></div><div class="box"><p class="eyebrow">Choose {e(pb_["brand"])} if</p><ul>{''.join(f'<li>{e(x)}</li>' for x in verdict_b)}</ul></div></div>
 <div class="note"><b>Our verdict:</b> both are good, original products. Pick by your skin and what you want from it, not by the number on the bottle.</div>
</div></section>'''+faq_block(qs,'Questions','Still deciding?',True,'vsq')
    doc(file,f'{title} | Nexus Beauty',lede.replace('"',"'"),body,[bc_ld([('Home','index.html'),('Compare','compare.html'),(crumb,None)]),faq_ld(qs),
      {"@context":"https://schema.org","@type":"ItemList","name":title,"itemListElement":[{"@type":"ListItem","position":1,"name":pa['brand']+' '+pa['name']},{"@type":"ListItem","position":2,"name":pb_['brand']+' '+pb_['name']}]}],section='Compare')
vs_page('vs-sunscreen.html','boj-relief-sun','estelin-70','Beauty of Joseon Relief Sun vs Estelin Tinted SPF 70','Pakistan\'s two most-reviewed sunscreens, compared: protection, finish, white cast, price and who each one suits.',
 [('SPF','SPF 50+','SPF 70'),('Tint','No tint','Tinted <span class="win">hides redness</span>'),('Finish','Dewy, light','Natural, light coverage'),('White cast','Minimal on most skin tones','None, thanks to the tint'),('Made in','Korea','Pakistan (derm brand)'),('Price','Rs 1,299 for 50 ml','Rs 1,239 <span class="win">lower</span>'),('Reviews on Highfy','212','319 <span class="win">more</span>'),('Best for','Normal to dry skin, under makeup','Oily skin, uneven tone, no-makeup days')],
 ['You want a light, dewy finish','You wear foundation over sunscreen','Your skin is normal to dry'],['You want to even out redness or marks','You skip foundation and want light coverage','You have oily skin and prefer a local derm brand'],
 [('Can I use both?','Yes. Many people wear Relief Sun on makeup days and Estelin on no-makeup days.'),('Which is better for acne-prone skin?','Both are light. If marks bother you, the tint in Estelin helps hide them while they fade.')],'Sunscreen')
vs_page('vs-hair-fall-shampoo.html','rederm-tressfix','jenpharm-anagrow','Rederm Tressfix vs Jenpharm Anagrow: best hair fall shampoo?','Pakistan\'s two best-selling derm hair fall shampoos, compared: what each targets, price and who should use which.',
 [('Main job','Hair fall and dandruff','Hair fall, with biotin'),('Dandruff control','Yes <span class="win">both in one</span>','Not its focus'),('Brand','Rederm (Pakistan)','Jenpharm (Pakistan)'),('Price','Rs 1,150','Rs 988 <span class="win">lower</span>'),('Reviews on Highfy','329 <span class="win">more</span>','61'),('Best for','Hair fall with flakes or itchy scalp','Hair fall without dandruff')],
 ['You have dandruff or an itchy scalp','You want one shampoo for both problems'],['Your scalp is clear but hair is thinning','You want a lower-priced option'],
 [('Can I alternate them?','Yes. Our Hair Fall Kit uses Tressfix three times a week and Anagrow on other wash days.'),('How long until I see less hair fall?','Give it 8–12 weeks. If hair falls in patches or suddenly, see a doctor.')],'Hair fall shampoo')
cmp=[('vs-sunscreen.html','Beauty of Joseon Relief Sun vs Estelin Tinted SPF 70','Sunscreen','boj-relief-sun','estelin-70'),('vs-hair-fall-shampoo.html','Rederm Tressfix vs Jenpharm Anagrow','Hair fall shampoo','rederm-tressfix','jenpharm-anagrow')]
cmp_cards=''.join(f'<a class="prob" href="{f}"><span class="prob__art" style="--bg:var(--blush)"><svg viewBox="0 0 100 160" aria-hidden="true" style="color:{BY[a]["tone"]}"><use href="#i-{BY[a]["shape"]}"/></svg><svg viewBox="0 0 100 160" aria-hidden="true" style="color:{BY[b]["tone"]}"><use href="#i-{BY[b]["shape"]}"/></svg></span><span class="prob__body"><p class="eyebrow">{e(c)}</p><h3>{e(t)}</h3><span class="link">Compare</span></span></a>' for f,t,c,a,b in cmp)
doc('compare.html','Product Comparisons: Which One Should You Buy? | Nexus Beauty','Side-by-side comparisons of Pakistan\'s best-selling skincare and hair care: sunscreens, hair fall shampoos and more.',
 phead([('Home','index.html'),('Compare',None)],'Which one should you buy?','Honest side-by-side comparisons of products Pakistanis ask us about most.')+f'<section class="section"><div class="wrap"><div class="probs">{cmp_cards}</div><p class="lede" style="margin-top:24px">Want a comparison we haven\'t written yet? <a href="contact.html" style="text-decoration:underline">Tell us</a>.</p></div></section>',
 [bc_ld([('Home','index.html'),('Compare',None)])],section='Compare')

# ================= CART / CHECKOUT / CONFIRM / TRACK / ACCOUNT / WISHLIST / SEARCH =================
PROMO_FORM='<div style="display:grid;gap:6px"><form class="promo" data-promo novalidate><label class="sr-only" for="{id}">Discount code</label><input id="{id}" type="text" placeholder="Discount code" autocomplete="off"><button class="btn btn--g" type="submit">Apply</button></form><p class="formmsg" data-promo-msg role="status"></p></div>'
cart_body=phead([('Home','index.html'),('Your bag',None)],'Your bag','Review your products, add a code, then check out. Cash on delivery available everywhere in Pakistan.')+f'''
<div class="wrap cartwrap">
 <section aria-label="Products in your bag"><div id="cartPage"></div></section>
 <aside class="summary" id="cartSummary" aria-labelledby="sum-h">
  <h2 id="sum-h">Order summary</h2>
  <div id="cartRows"></div>
  <p id="cartShip" style="font-size:14px"></p>
  {PROMO_FORM.replace('{id}','promoCart')}
  <a class="btn btn--p btn--lg btn--block" href="checkout.html">Check out securely</a>
  <p class="secure"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/></svg>Original products · batch and expiry on every item</p>
  <div class="pay" style="color:var(--ink-2)"><span style="border-color:var(--line)">COD</span><span style="border-color:var(--line)">JAZZCASH</span><span style="border-color:var(--line)">EASYPAISA</span><span style="border-color:var(--line)">VISA</span><span style="border-color:var(--line)">MASTERCARD</span></div>
 </aside>
</div>
<section class="section section--tint"><div class="wrap"><div class="sec-head"><div><p class="eyebrow">Often added</p><h2 class="h2">Minis under Rs 1,000</h2></div><a class="link" href="collection-minis.html">All minis</a></div><div class="minis">__MINIS__</div></div></section>'''+RECENT
doc('cart.html','Your Bag | Nexus Beauty','Your Nexus Beauty shopping bag.',cart_body,robots='noindex, follow')
CITIES=[('Karachi','1-2'),('Lahore','1-2'),('Islamabad','2-3'),('Rawalpindi','2-3'),('Faisalabad','3-5'),('Multan','3-5'),('Peshawar','3-5'),('Hyderabad','3-5'),('Gujranwala','3-5'),('Sialkot','3-5'),('Quetta','3-5'),('Other city','3-5')]
PAYS=[('cod','Cash on delivery','Pay the rider in cash when your parcel arrives.',True),('jazzcash','JazzCash','After you place the order, you\'ll get a payment request on your JazzCash number.',False),('easypaisa','Easypaisa','After you place the order, you\'ll get a payment request on your Easypaisa number.',False),('card','Debit or credit card','After you place the order, you\'ll pay on a secure card payment page.',False)]
co_body=f'''<div class="wrap">{crumbs([('Home','index.html'),('Bag','cart.html'),('Checkout',None)])}</div>
<div class="wrap cartwrap" id="checkoutWrap" style="padding-top:4px">
 <form class="form" id="checkoutForm" novalidate aria-labelledby="co-h">
  <div class="co-steps" aria-label="Checkout steps"><span>1. Bag</span><span>›</span><span aria-current="step">2. Details &amp; payment</span><span>›</span><span>3. Done</span></div>
  <h1 id="co-h" style="font-size:clamp(26px,3.4vw,36px)">Checkout</h1>
  <p style="color:var(--ink-2)">No account needed. We'll send your order updates by SMS and WhatsApp.</p>
  <h2 style="font-size:19px;margin-top:8px">Your details</h2>
  <div class="row2"><label class="field" for="coName">Full name<input id="coName" name="name" autocomplete="name" required><span class="err" data-err="name"></span></label>
  <label class="field" for="coPhone">Mobile number<input id="coPhone" name="phone" type="tel" inputmode="tel" autocomplete="tel" placeholder="0300 1234567" required><span class="err" data-err="phone"></span></label></div>
  <label class="field" for="coEmail">Email <small>(optional, for your receipt)</small><input id="coEmail" name="email" type="email" autocomplete="email"><span class="err" data-err="email"></span></label>
  <h2 style="font-size:19px;margin-top:8px">Delivery</h2>
  <label class="field" for="coCity">City<select id="coCity" name="city" autocomplete="address-level2" required><option value="">Choose your city</option>{''.join(f'<option value="{c}" data-days="{d}">{c}</option>' for c,d in CITIES)}</select><span class="err" data-err="city"></span><small id="coEta"></small></label>
  <label class="field" for="coAddr">Full address<textarea id="coAddr" name="address" rows="3" autocomplete="street-address" placeholder="House number, street, area" required></textarea><span class="err" data-err="address"></span></label>
  <label class="field" for="coNotes">Delivery notes <small>(optional)</small><input id="coNotes" name="notes" placeholder="e.g. call before arriving"></label>
  <h2 style="font-size:19px;margin-top:8px">Payment</h2>
  <div class="pay-opts" role="radiogroup" aria-label="Payment method">{''.join(f'<label class="pay-opt"><input type="radio" name="pay" value="{k}" data-note="{e(n)}"{" checked" if c else ""}><span><b>{t}</b><span>{e(n)}</span></span></label>' for k,t,n,c in PAYS)}</div>
  <p id="payNote" class="sr-only" aria-live="polite"></p>
  <button class="btn btn--p btn--lg btn--block" type="submit" id="placeOrder">Place order</button>
  <p class="formmsg" id="coMsg" role="status"></p>
  <p style="font-size:13px;color:var(--ink-3)">By placing your order you agree to our <a href="terms.html" style="text-decoration:underline">terms</a> and <a href="privacy.html" style="text-decoration:underline">privacy policy</a>.</p>
 </form>
 <aside class="summary" aria-labelledby="cs-h">
  <h2 id="cs-h">Your order</h2>
  <div id="coItems" style="display:grid;gap:12px"></div>
  {PROMO_FORM.replace('{id}','promoCo')}
  <div id="coRows"></div>
  <p class="secure"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/></svg>Batch and expiry printed on your parcel sticker</p>
  <p class="secure"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 9h11a5 5 0 010 10H9"/><path d="M8 5L4 9l4 4"/></svg><a href="returns.html" style="text-decoration:underline">7-day returns</a> on unopened products</p>
 </aside>
</div>'''
doc('checkout.html','Checkout | Nexus Beauty','Secure checkout. Cash on delivery, JazzCash, Easypaisa and cards.',co_body,robots='noindex, nofollow')
conf=f'''<div class="phead"><div class="wrap" id="confirm"><p class="eyebrow" style="margin-top:14px">Order placed</p><h1>Thank you, <span id="cName">there</span>.</h1><p class="lede">Your order <b id="cNo" class="mono"></b> is confirmed. We've sent the details to your phone.</p></div></div>
<div class="wrap cartwrap">
 <div style="display:grid;gap:16px">
  <div class="box"><h2 style="font-size:20px">What happens next</h2><ol class="track"><li class="done"><span class="dot">✓</span><span><b>Order confirmed</b><span>You'll get an SMS and a WhatsApp message.</span></span></li><li class="now"><span class="dot">2</span><span><b>We check and pack it</b><span>Every item's batch and expiry goes on your parcel sticker.</span></span></li><li><span class="dot">3</span><span><b>On its way</b><span>Expected <b id="cEta"></b>.</span></span></li><li><span class="dot">4</span><span><b>Delivered</b><span>Check the batch and expiry against the sticker before you pay the rider.</span></span></li></ol></div>
  <div class="cards2"><div class="box"><h3>Delivering to</h3><p id="cAddr"></p><p id="cPhone" class="mono"></p></div><div class="box"><h3>Payment</h3><p id="cPay"></p></div></div>
  <div style="display:flex;gap:10px;flex-wrap:wrap"><a class="btn btn--p" href="track-order.html">Track this order</a><a class="btn btn--g" href="shop.html">Continue shopping</a></div>
 </div>
 <aside class="summary"><h2>Order summary</h2><div id="cItems" style="display:grid;gap:8px"></div></aside>
</div>'''
doc('order-confirmation.html','Order Confirmed | Nexus Beauty','Your Nexus Beauty order is confirmed.',conf,robots='noindex, nofollow')
track=phead([('Home','index.html'),('Track order',None)],'Track your order','Enter the order number from your confirmation message and the mobile number you ordered with.')+'''
<section class="section"><div class="wrap cartwrap" style="padding:0">
 <form class="form box" id="trackForm" novalidate style="align-self:start"><label class="field" for="tOrder">Order number<input id="tOrder" name="order" placeholder="NX-104829" autocomplete="off" required></label><label class="field" for="tPhone">Mobile number<input id="tPhone" name="phone" type="tel" inputmode="tel" placeholder="0300 1234567"></label><button class="btn btn--p btn--lg" type="submit">Track order</button><p class="formmsg" id="trackMsg" role="status"></p><p style="font-size:14px;color:var(--ink-2)">Can't find your order number? <a href="contact.html" style="text-decoration:underline">Contact us</a> with your phone number.</p></form>
 <div id="trackOut" aria-live="polite"></div>
</div></section>'''
doc('track-order.html','Track Your Order | Nexus Beauty','Track your Nexus Beauty order with your order number and mobile number.',track,[bc_ld([('Home','index.html'),('Track order',None)])],section='Help')
acct=phead([('Home','index.html'),('My account',None)],'My account','Sign in with your mobile number. No password to remember.')+'''
<section class="section"><div class="wrap"><div class="acct" id="account"></div></div></section>
<template id="loginTpl"><form class="form box" id="loginForm" novalidate><h2 style="font-size:22px">Sign in or create an account</h2><label class="field" for="lPhone">Mobile number<input id="lPhone" name="phone" type="tel" inputmode="tel" autocomplete="tel" placeholder="0300 1234567"></label><label class="field" for="lOtp" id="otpRow" hidden>4-digit code<input id="lOtp" name="otp" inputmode="numeric" maxlength="4" autocomplete="one-time-code"></label><label class="field" for="lName" id="nameRow" hidden>Your name <small>(new customers)</small><input id="lName" name="name" autocomplete="name"></label><button class="btn btn--p btn--lg" type="submit">Send code</button><p class="formmsg" id="loginMsg" role="status"></p><p class="note">Preview: SMS codes start working once the store is connected to an SMS service. For now, any 4 digits will sign you in.</p></form></template>'''
doc('account.html','My Account | Nexus Beauty','Sign in to see your orders, addresses and wishlist.',acct,robots='noindex, follow')
doc('wishlist.html','My Wishlist | Nexus Beauty','Products you have saved at Nexus Beauty.',phead([('Home','index.html'),('Wishlist',None)],'Your wishlist','Saved on this device. Tap the heart on any product to add it here.','<button class="btn btn--p" type="button" id="wishAll" style="margin-top:16px" hidden>Add all to bag</button>')+'<section class="section"><div class="wrap"><div class="minis" id="wishPage"></div></div></section>'+RECENT,robots='noindex, follow')
doc('search.html','Search | Nexus Beauty','Search original skincare, hair care and fragrance at Nexus Beauty.',phead([('Home','index.html'),('Search',None)],'Search',"Find products by name, brand or concern.",
 f'<form class="sfield" id="searchPageForm" role="search" style="max-width:560px;margin-top:18px"><label class="sr-only" for="sq">Search</label>{ic("search")}<input id="sq" type="search" placeholder="e.g. sunscreen, hair fall, AXIS-Y" autocomplete="off"></form><div class="popular" style="margin-top:12px"><span>Try:</span>'+''.join(f'<button type="button" data-sq="{x}">{x}</button>' for x in ['sunscreen','hair fall','serum','mist','lipstick','derm'])+'</div>')+
 '<section class="section" style="padding-top:24px"><div class="wrap"><p id="sCount" style="margin-bottom:14px;color:var(--ink-2)"></p><div class="minis" id="searchPage"></div></div></section>',robots='noindex, follow')

# ================= INFO PAGES =================
def info(file,title,h1,lede,desc,inner,items=None,ld=(),section='Help',tint_after=''):
    items=items or [('Home','index.html'),(h1,None)]
    doc(file,title,desc,phead(items,h1,lede)+f'<section class="section"><div class="wrap">{inner}</div></section>'+tint_after,[bc_ld(items)]+list(ld),section=section)
info('about.html','About Nexus Beauty | Pakistan\'s Checked Beauty Store','About Nexus Beauty','We started Nexus Beauty because buying skincare online in Pakistan shouldn\'t feel like a gamble.',
 'Nexus Beauty is a Pakistani online beauty store selling only original products from brands and authorised distributors, with the batch and expiry on every order.',
 f'''<div class="layout2"><nav class="toc" aria-label="On this page"><p>On this page</p><a href="#story">Our story</a><a href="#promise">Our promise</a><a href="#different">What makes us different</a><a href="#label">Our own label</a></nav><div class="prose">
 <h2 id="story">Our story</h2><p>Fake and expired products are the biggest worry for anyone buying beauty online in Pakistan. Big stores have huge ranges but show no proof. Small shops promise "100% original" but can't show where their stock came from.</p><p>Nexus Beauty is the meeting point, the <em>nexus</em>, of Pakistan's trusted dermatologist brands, the best Korean and international skincare, and our own Koh-e-Noor fragrance label. Every product is checked before it reaches you.</p>
 <h2 id="promise">Our promise: Beauty, checked.</h2><ul><li>We buy only from brands and their authorised distributors, and keep the invoice for every batch.</li><li>We print the batch number and expiry date of every item on your parcel sticker.</li><li>We never sell "first copy", grey-market or whitening creams without full ingredient lists.</li><li>We only publish reviews from verified orders, and never edit or hide them.</li></ul>
 <h2 id="different">What makes us different</h2><div class="cards3"><div class="box"><h3>Proof over promises</h3><p>Batch and expiry on every order, not just a claim on our homepage.</p></div><div class="box"><h3>Advice by concern</h3><p>Shop for hair fall, acne or dark spots, and get free WhatsApp advice.</p></div><div class="box"><h3>Made for our climate</h3><p>Light textures and sunscreens that work in Pakistan's heat.</p></div></div>
 <h2 id="label">Our own label: Koh-e-Noor</h2><p>Koh-e-Noor hair and body mists are made in Pakistan and priced between body sprays and perfume: 100 ml of long-lasting scent for Rs 1,290. <a href="collection-koh-e-noor.html">Shop Koh-e-Noor</a>.</p>
 <div class="note">Want your brand on Nexus Beauty? <a href="contact.html">Contact our buying team</a>.</div></div></div>''',
 ld=[ORG_LD],section='Company')
info('contact.html','Contact Us | Nexus Beauty','Contact us','Real people, 10am to 10pm, every day. We usually reply within 2 hours.','Contact Nexus Beauty by WhatsApp, email or the contact form. We reply within 2 hours, 10am to 10pm, every day.',
 f'''<div class="cartwrap" style="padding:0"><form class="form box" id="contactForm" novalidate><h2 style="font-size:22px">Send us a message</h2><label class="field" for="cname">Your name<input id="cname" name="cname" autocomplete="name"></label><label class="field" for="cphone">Mobile number or email<input id="cphone" name="cphone" autocomplete="tel"></label><label class="field" for="ctopic">Topic<select id="ctopic" name="ctopic"><option>Question about a product</option><option>My order</option><option>Returns and refunds</option><option>Skin or hair advice</option><option>Stock my brand</option><option>Something else</option></select></label><label class="field" for="cmsg">Message<textarea id="cmsg" name="cmsg" rows="5"></textarea></label><button class="btn btn--p btn--lg" type="submit">Send message</button><p class="formmsg" id="contactMsg" role="status"></p></form>
 <aside style="display:grid;gap:14px"><div class="box"><h3>WhatsApp</h3><p class="mono" style="font-size:18px;color:var(--ink)">+92 300 000 0000</p><p>Fastest for order questions and skin advice.</p><a class="btn btn--d" href="https://wa.me/923000000000" target="_blank" rel="noopener">Open WhatsApp</a></div><div class="box"><h3>Email</h3><p class="mono" style="color:var(--ink)">care@nexusbeauty.pk</p><p>For invoices, brand partnerships and anything with attachments.</p></div><div class="box"><h3>Hours</h3><p>10am to 10pm, every day. Orders placed before 3pm ship the same day (except Sunday).</p></div><div class="box"><h3>Quick links</h3><p><a href="track-order.html" style="text-decoration:underline">Track an order</a> · <a href="returns.html" style="text-decoration:underline">Start a return</a> · <a href="faq.html" style="text-decoration:underline">FAQs</a></p></div></aside></div>''',
 ld=[ORG_LD])
info('authenticity.html','How We Check Authenticity | Nexus Beauty','How we check authenticity','Fake skincare can contain mercury, steroids or bacteria. Here\'s exactly how we make sure yours is original and fresh.','How Nexus Beauty sources from authorised distributors, records batch and expiry, and how you can check your products yourself.',
 f'''<div class="layout2"><nav class="toc" aria-label="On this page"><p>On this page</p><a href="#source">Where we buy</a><a href="#check">What we check</a><a href="#you">Check it yourself</a><a href="#signs">Signs of a fake</a><a href="#report">Report a problem</a></nav><div class="prose">
 <h2 id="source">Where we buy</h2><p>Only from the brand itself or its authorised distributor in Pakistan. We keep the purchase invoice for every batch we stock. We never buy from wholesale markets, "surplus" sellers or individuals.</p>
 <h2 id="check">What we check before a product goes on sale</h2><ol><li>The batch number and expiry date, which we record in our system.</li><li>Sealed packaging, correct labels and an intact barcode.</li><li>At least 6 months of shelf life left (12 months for most products).</li><li>Storage away from heat and sunlight in our warehouse.</li></ol>
 <h2 id="you">Check it yourself</h2><p>Every parcel sticker lists each item with its batch number and expiry date. Before you pay the rider:</p><ul><li>Match the batch and expiry on the sticker with the box.</li><li>Check the seal is intact.</li><li>For many brands, you can check the batch code on the brand's website.</li></ul>
 <h2 id="signs">Signs of a fake product anywhere</h2><ul><li>A price far below every other store.</li><li>Missing batch number or expiry date, or a sticker covering them.</li><li>Spelling mistakes, blurry print or a different colour from the brand's photos.</li><li>Unusual smell, texture or colour.</li><li>"Whitening" creams with no ingredient list.</li></ul>
 <h2 id="report">Something looks wrong?</h2><p>Send a photo of the product and the parcel sticker on WhatsApp. If we can't prove it's original, we refund you in full, including delivery.</p><div class="note">You never need to open a product to return it for an authenticity concern.</div></div></div>''')
ship_rows=''.join(f'<tr><td>{c}</td><td>{d.replace("-","–")} working days</td><td>Rs 250, free over Rs 5,000</td></tr>' for c,d in CITIES)
info('shipping.html','Delivery Information: Times &amp; Charges | Nexus Beauty','Delivery information','We deliver to every city in Pakistan. Orders before 3pm leave the same day.','Nexus Beauty delivers across Pakistan: 1–2 days to Karachi and Lahore, 2–3 to Islamabad, 3–5 elsewhere. Free delivery over Rs 5,000. Cash on delivery available.',
 f'''<div class="prose" style="max-width:none"><div class="cards3"><div class="box"><h3>Rs 250 delivery</h3><p>Free on orders over Rs 5,000.</p></div><div class="box"><h3>Same-day dispatch</h3><p>Order before 3pm, Monday to Saturday.</p></div><div class="box"><h3>Cash on delivery</h3><p>Available everywhere we deliver.</p></div></div>
 <h2>Delivery times by city</h2><div class="tbl"><table><thead><tr><th>City</th><th>Delivery time</th><th>Charge</th></tr></thead><tbody>{ship_rows}</tbody></table></div><p>Working days are Monday to Saturday. Public holidays and bad weather can add a day or two.</p>
 <h2>How it works</h2><ol><li>You get an SMS and WhatsApp confirmation with your order number.</li><li>We check the batch and expiry of every item and seal your parcel.</li><li>Our courier partner delivers it and calls before arriving.</li><li>You check the parcel sticker, then pay the rider if you chose cash on delivery.</li></ol>
 <h2>Cash on delivery rules</h2><ul><li>Please keep the exact amount ready if you can.</li><li>You can check the outer parcel and sticker before paying. Opening sealed products before paying isn't allowed by couriers.</li><li>If you refuse a COD parcel without a reason more than twice, we may ask for advance payment on future orders.</li></ul>
 <p><a href="track-order.html">Track your order</a> · <a href="returns.html">Returns &amp; refunds</a></p></div>''')
RET_FAQ=[('Can I return an opened product?','Only if it arrived damaged, faulty, wrong or you have an authenticity concern. For hygiene reasons we can\'t take back opened products you simply didn\'t like.'),('Who pays for return delivery?','We do if the item was damaged, wrong or faulty. For change-of-mind returns, return delivery is Rs 250.'),('How long do refunds take?','We refund within 2 working days of receiving the return. Bank transfers take 3–5 working days to arrive; JazzCash and Easypaisa are usually instant.')]
info('returns.html','Returns &amp; Refunds Policy | Nexus Beauty','Returns &amp; refunds','7-day returns on unopened products. Damaged, wrong or doubtful items are always on us.','Nexus Beauty returns policy: 7 days for unopened products, free returns for damaged or wrong items, refunds within 2 working days.',
 f'''<div class="layout2"><nav class="toc" aria-label="On this page"><p>On this page</p><a href="#policy">The policy</a><a href="#how">How to return</a><a href="#refunds">Refunds</a><a href="#exchange">Exchanges</a></nav><div class="prose">
 <h2 id="policy">The policy</h2><ul><li><b>Change of mind:</b> return unopened, unused products in original packaging within 7 days of delivery.</li><li><b>Damaged, leaking or wrong item:</b> tell us within 48 hours with a photo. We replace or refund it free.</li><li><b>Authenticity concern:</b> any time before the expiry date. If we can't prove it's original, you get a full refund.</li><li><b>Not returnable once opened:</b> skincare, makeup, fragrance and personal care, unless faulty.</li></ul>
 <h2 id="how">How to return</h2><ol><li>WhatsApp or email us your order number and a photo of the item.</li><li>We confirm within 2 hours and book a courier pickup, or give you our return address.</li><li>Pack the item in its original box.</li></ol>
 <h2 id="refunds">Refunds</h2><p>We refund within 2 working days of receiving your return, to your bank account, JazzCash or Easypaisa. Card payments are refunded to the same card. Delivery charges are refunded when the mistake was ours.</p>
 <h2 id="exchange">Exchanges</h2><p>Want a different size or shade? Return the unopened item and we'll send the new one as soon as it's picked up.</p>
 <div class="faq">{''.join(f'<details><summary>{e(q)}</summary><p>{e(a)}</p></details>' for q,a in RET_FAQ)}</div></div></div>''',ld=[faq_ld(RET_FAQ)])
FAQG=[('Orders & payment',[('How do I place an order?','Add products to your bag and check out. You don\'t need an account.'),('Which payment methods do you accept?','Cash on delivery, JazzCash, Easypaisa, and debit or credit cards.'),('Do you have discount codes?','New customers get Rs 200 off with code NEXUS200 on orders over Rs 1,500. Kits are 15% off when you choose 3 or more products.'),('Can I change or cancel my order?','Yes, until it\'s packed. WhatsApp us with your order number as soon as possible.')]),
 ('Delivery',[('How long does delivery take?','1–2 working days to Karachi and Lahore, 2–3 to Islamabad and Rawalpindi, 3–5 to other cities.'),('How much is delivery?','Rs 250, and free on orders over Rs 5,000.'),('How do I track my order?','Use the Track order page with your order number and mobile number.')]),
 ('Products & authenticity',[('Are your products original?','Yes. We buy only from brands and authorised distributors, and print each item\'s batch and expiry on your parcel sticker.'),('Do you sell whitening creams?','No. We sell brightening products with full ingredient lists, and never unlabelled whitening or fairness creams.'),('Can you help me choose a product?','Yes. WhatsApp us for free advice, or use the routine finder on the homepage.')]),
 ('Returns',[('What is your return policy?','Unopened products within 7 days. Damaged, wrong or faulty items are always replaced or refunded.'),('How fast are refunds?','Within 2 working days of receiving your return.')]),
 ('Account',[('Do I need an account?','No. An account just saves your address and shows your order history.'),('How do I sign in?','With your mobile number and a one-time SMS code. No password.')])]
faq_html=''.join(f'<div class="faqgroup" style="margin-bottom:28px"><h2 class="h2" style="font-size:24px;margin-bottom:6px">{e(g)}</h2><div class="faq">{"".join(f"<details><summary>{e(q)}</summary><p>{e(a)}</p></details>" for q,a in qs)}</div></div>' for g,qs in FAQG)
info('faq.html','Frequently Asked Questions | Nexus Beauty','Frequently asked questions','Orders, delivery, payment, returns and authenticity, answered.','Answers about Nexus Beauty orders, cash on delivery, delivery times, returns and product authenticity.',
 f'<div class="sfield" style="max-width:480px;margin-bottom:28px"><label class="sr-only" for="faqQ">Search questions</label>{ic("search")}<input id="faqQ" type="search" placeholder="Search questions, e.g. refund"></div>{faq_html}<div class="empty-state" id="faqEmpty" hidden><b>No answers match.</b><p><a href="contact.html" style="text-decoration:underline">Ask us directly</a>. We reply within 2 hours.</p></div>',
 ld=[faq_ld([q for g in FAQG for q in g[1]])])
LEGAL_NOTE='<p class="note">Last updated 29 September 2026.</p>'
info('privacy.html','Privacy Policy | Nexus Beauty','Privacy policy','What we collect, why, and how you control it.','How Nexus Beauty collects, uses and protects your personal information.',
 f'''<div class="prose">{LEGAL_NOTE}<h2>What we collect</h2><ul><li><b>Order details:</b> name, mobile number, delivery address, optional email and what you bought.</li><li><b>Payment:</b> we don't store card numbers. Card, JazzCash and Easypaisa payments are handled by the payment provider.</li><li><b>On this device:</b> your bag, wishlist and recently viewed products are saved in your browser, not on our servers.</li><li><b>Analytics cookies:</b> only if you choose "Accept all" in the cookie notice.</li></ul>
 <h2>How we use it</h2><ul><li>To deliver your order and send SMS and WhatsApp updates.</li><li>To handle returns, refunds and questions.</li><li>To send offers, only if you sign up. Every email has an unsubscribe link.</li></ul>
 <h2>Who we share it with</h2><p>Only the courier delivering your parcel, the payment provider you choose, and our SMS and email services. We never sell your data.</p>
 <h2>How long we keep it</h2><p>Order records for as long as the law requires for tax and accounting. Marketing data until you unsubscribe.</p>
 <h2>Your choices</h2><p>Ask us to see, correct or delete your data at any time: <b>care@nexusbeauty.pk</b>. You can clear the data saved on your device by clearing your browser's site data.</p>
 <h2>Security</h2><p>Our website uses HTTPS encryption, and access to customer data is limited to staff who need it.</p></div>''',section='Legal')
info('terms.html','Terms of Service | Nexus Beauty','Terms of service','The rules for buying from Nexus Beauty, in plain English.','Nexus Beauty terms of service: orders, prices, delivery, returns and use of the website.',
 f'''<div class="prose">{LEGAL_NOTE}<h2>1. Who we are</h2><p>Nexus Beauty is an online store selling beauty and personal care products in Pakistan.</p>
 <h2>2. Orders</h2><p>Your order is confirmed when you receive our SMS or WhatsApp confirmation. We may cancel an order if an item is out of stock, the price was shown wrongly, or we can't verify the delivery details. If you paid in advance, we refund you in full.</p>
 <h2>3. Prices and payment</h2><p>Prices are in Pakistani rupees and include taxes. Delivery charges are shown at checkout. Discount codes can't be combined unless stated.</p>
 <h2>4. Delivery</h2><p>Delivery times are estimates. See <a href="shipping.html">Delivery information</a>.</p>
 <h2>5. Returns</h2><p>See our <a href="returns.html">Returns &amp; refunds policy</a>.</p>
 <h2>6. Product information</h2><p>We show product details as provided by the brand. Always read the label, patch test new products and follow the directions. Our routines and guides are general information, not medical advice.</p>
 <h2>7. Reviews</h2><p>Reviews must be honest and about a product you bought from us. We don't publish reviews with offensive content or personal information.</p>
 <h2>8. Governing law</h2><p>These terms are governed by the laws of Pakistan.</p>
 <h2>9. Contact</h2><p>Questions about these terms: <b>care@nexusbeauty.pk</b>.</p></div>''',section='Legal')
offers_inner=f'''<div class="cards3"><div class="box"><p class="eyebrow">New customers</p><h3>Rs 200 off your first order</h3><p>Use code <b class="mono">NEXUS200</b> at checkout on orders over Rs 1,500.</p><a class="btn btn--p" href="shop.html" style="justify-self:start">Shop now</a></div>
<div class="box"><p class="eyebrow">Routine kits</p><h3>15% off 3 or more</h3><p>Choose 3 or more products from any problem kit and the discount applies automatically.</p><a class="btn btn--p" href="solutions.html" style="justify-self:start">See kits</a></div>
<div class="box"><p class="eyebrow">Every order</p><h3>Free delivery over Rs 5,000</h3><p>Anywhere in Pakistan, with cash on delivery.</p><a class="btn btn--p" href="shipping.html" style="justify-self:start">Delivery info</a></div></div>
<div class="sec-head" style="margin-top:48px"><div><p class="eyebrow">Launch prices</p><h2 class="h2">Koh-e-Noor mists: Rs 1,290 (was Rs 1,450)</h2></div><a class="link" href="collection-koh-e-noor.html">Shop Koh-e-Noor</a></div>
<div class="grid">{''.join(N.card(BY[i]) for i in ['kn-vp','kn-oud','kn-musk','kn-vp-30'])}</div>
<p class="note" style="margin-top:28px">We don't run fake "80% off" sales. Our prices are fair every day, and offers here are real.</p>'''
info('offers.html','Offers &amp; Discount Codes | Nexus Beauty','Offers','Real offers, no fake countdowns: discount codes, kit savings and free delivery.','Current Nexus Beauty offers: Rs 200 off your first order with NEXUS200, 15% off routine kits, free delivery over Rs 5,000.',offers_inner,section='Shop')

# ================= 404 + SITEMAP =================
nf=f'''<section class="wrap nf"><b>404</b><h1 style="font-size:clamp(26px,3.4vw,38px)">We can't find that page</h1><p class="lede">It may have moved, or the link may be wrong. Try searching, or start from one of these.</p>
<form class="sfield" style="width:min(100%,480px)" onsubmit="event.preventDefault();var v=this.querySelector('input').value.trim();if(v)location.href='search.html#'+v.toLowerCase().replace(/[^a-z0-9]+/g,'-');"><label class="sr-only" for="nfq">Search</label>{ic('search')}<input id="nfq" type="search" placeholder="Search products"></form>
<div class="jump" style="justify-content:center"><a href="index.html">Home</a><a href="shop.html">Shop all</a><a href="solutions.html">Routines</a><a href="brands.html">Brands</a><a href="contact.html">Contact us</a></div></section>'''+RECENT
doc('404.html','Page Not Found | Nexus Beauty','This page could not be found.',nf,robots='noindex, follow')
sec_order=['Main','Shop','Collections','Products','Brands','Compare','Journal','Help','Company','Legal']
groups={}
for f,t,s in PAGES: groups.setdefault(s,[]).append((f,t))
sm=''.join(f'<section><h2>{s}</h2>'+''.join(f'<a href="{f}">{e(t)}</a>' for f,t in groups[s])+'</section>' for s in sec_order if s in groups)
doc('sitemap.html','Sitemap | Nexus Beauty','Every page on the Nexus Beauty website.',phead([('Home','index.html'),('Sitemap',None)],'Sitemap','Every page on Nexus Beauty.')+f'<section class="section"><div class="wrap sitemap">{sm}</div></section>',robots='noindex, follow')
today=datetime.date(2026,9,29).isoformat()
xml='<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n'+''.join(f'  <url><loc>{BASE}{"" if f=="index.html" else f.replace(".html","")}</loc><lastmod>{today}</lastmod></url>\n' for f,t,s in PAGES)+'</urlset>\n'
open(OUT+'sitemap.xml','w').write(xml)
open(OUT+'robots.txt','w').write(f'User-agent: *\nDisallow: /cart\nDisallow: /checkout\nDisallow: /order-confirmation\nDisallow: /account\nDisallow: /wishlist\nDisallow: /search\n\nSitemap: {BASE}sitemap.xml\n')
# cart minis placeholder
c=open(OUT+'cart.html').read()
mins=[f'<article class="mini"><a href="product.html" class="mini__link" data-add-search="{p["id"]}"><span class="thumb" style="--tone:{p["tone"]};--bg:{p["bg"]}">{shape(p["shape"])}</span><span><small>{e(p["brand"])}</small><b>{e(p["name"])}</b><span class="p">{rs(p["price"])}</span></span></a><button class="btn btn--d" type="button" data-add="{p["id"]}">Add</button></article>' for p in P if p['price']<1000][:6]
open(OUT+'cart.html','w').write(c.replace('__MINIS__',''.join(mins)))
print(len(os.listdir(OUT)),'files;',len(PAGES),'indexable pages')
