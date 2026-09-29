import json, html, sys
sys.path.insert(0,'.')
from common import SVGDEFS as BASEDEFS
from nexus_css import CSS
e=html.escape
def rs(n): return 'Rs '+f'{n:,}'
MARK='<symbol id="mark" viewBox="0 0 120 80"><circle cx="44" cy="40" r="30" fill="none" stroke="currentColor" stroke-width="6"/><circle cx="76" cy="40" r="30" fill="none" stroke="currentColor" stroke-width="6"/><path d="M60 14.62 A30 30 0 0 1 60 65.38 A30 30 0 0 1 60 14.62 Z" style="fill:var(--lens, #8c2350)"/></symbol>'
DEFS=BASEDEFS.replace('</defs></svg>',MARK+'</defs></svg>')
ICON={
 'search':'<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
 'heart':'<path d="M12 20s-7.5-4.6-9-9.3C2 7.4 4.2 4.5 7.4 4.5c2 0 3.4 1.1 4.6 2.6 1.2-1.5 2.6-2.6 4.6-2.6 3.2 0 5.4 2.9 4.4 6.2-1.5 4.7-9 9.3-9 9.3z"/>',
 'bag':'<path d="M5 8h14l-1 12H6z"/><path d="M9 8V6a3 3 0 016 0v2"/>',
 'menu':'<path d="M4 7h16M4 12h16M4 17h16"/>','close':'<path d="M6 6l12 12M18 6L6 18"/>',
 'check':'<path d="M5 12.5l4.5 4.5L19 7.5"/>','down':'<path d="m6 9 6 6 6-6"/>',
 'shield':'<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="M8.5 12l2.5 2.5 4.5-5"/>',
 'cash':'<rect x="2.5" y="6" width="19" height="12" rx="2"/><circle cx="12" cy="12" r="2.6"/>',
 'truck':'<path d="M2 6h12v10H2zM14 10h4l3 3v3h-7z"/><circle cx="6" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
 'return':'<path d="M4 9h11a5 5 0 010 10H9"/><path d="M8 5L4 9l4 4"/>',
 'chat':'<path d="M4 5h16v11H9l-5 4z"/>','spark':'<path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M6 18l2.5-2.5M15.5 8.5 18 6"/>',
}
def ic(n,c=''): return f'<svg viewBox="0 0 24 24" aria-hidden="true"{c}>{ICON[n]}</svg>'
def shape(s,extra=''): return f'<svg viewBox="0 0 100 160" aria-hidden="true"{extra}><use href="#i-{s}"/></svg>'

# ---------- Catalogue (prices from the Pakistan Beauty Battle Book) ----------
T={'yel':('#e2a33b','#fbf1df'),'sun':('#f0c24b','#fdf5dc'),'lav':('#b3a0c9','#f0ebf6'),'blu':('#7fa7b8','#e9f1f4'),'grn':('#9bb79e','#eaf2eb'),
'hair':('#c68a3a','#f8eedf'),'body':('#d9b48f','#f7efe6'),'rose':('#b04a6b','#f7e5eb'),'lip':('#9e2f4a','#f6e2e7'),'men':('#3c3f44','#e8e9eb'),
'vp':('#c9a55a','#f6efdc'),'oud':('#8a4b2f','#f3e6dc'),'musk':('#86aec2','#e8f1f5'),'tint':('#c99a78','#f6ece4'),'pink':('#e58aa8','#fbe7ee'),'rosem':('#5f8f6a','#e6f0e8')}
P=[]
def add(id,brand,name,size,price,shape_,tone,cat,pills,why,concerns='',skin='all',tabs='',was=None):
    P.append(dict(id=id,brand=brand,name=name,size=size,price=price,was=was,shape=shape_,tone=T[tone][0],bg=T[tone][1],cat=cat,pills=pills,why=why,concerns=concerns,skin=skin,tabs=tabs))
add('axis-y-5','AXIS-Y','Dark Spot Correcting Glow Serum','5 ml mini',799,'dropper','yel','skincare',['mini','kr'],"Pakistan's most-reviewed serum. The mini sells as well as the 50 ml.",'spots dull','oily combo normal','best mini')
add('axis-y-50','AXIS-Y','Dark Spot Correcting Glow Serum','50 ml',4049,'dropper','yel','skincare',['kr'],'Full size of the serum Pakistan reviews most','spots dull','oily combo normal','')
add('dr-althea-345','Dr. Althea','345 Relief Cream','50 ml',2749,'jar','lav','skincare',['kr'],'Light barrier repair that suits the heat','dry sensitive','dry sensitive normal combo','best')
add('ponds-gel','Pond\'s','Super Light Gel Moisturiser','50 ml',749,'jar','blu','skincare',[],'A light gel for oily skin in summer','dry','oily combo','mini')
add('loreal-glycolic','L\'Oréal Paris','Glycolic Bright Face Wash','Face wash',999,'tube','rose','skincare',[],'The best-selling "bright" cleanser','spots dull','oily combo normal','')
add('garnier-vitc','Garnier','Bright Complete Vitamin C Serum','Serum',550,'dropper','yel','skincare',[],'An easy first vitamin C at a low price','dull spots','all','mini')
add('medicube-pdrn','Medicube','PDRN Pink Peptide Serum','Serum',3829,'dropper','pink','skincare',['new','kr'],'The number one product on US TikTok Shop this spring','dull dry','all','new')
add('anua-azelaic','Anua','Azelaic Acid 10 Hyaluron Redness Soothing Serum','Serum',6659,'dropper','grn','skincare',['new','kr'],'For redness, bumps and post-acne marks','acne sensitive spots','oily combo sensitive','new')
add('boj-relief-sun','Beauty of Joseon','Relief Sun SPF 50+','50 ml',1299,'tube','sun','sun',['kr'],'The sunscreen with the most reviews in Pakistan','sun spots','all','best')
add('estelin-70','Estelin','Tinted Sunscreen SPF 70','Sunscreen',1239,'tube','tint','sun',['derm','pk'],'A Pakistani derm favourite with no white cast','sun spots','all','best')
add('neobrella-60','Neophar','Neobrella Sunscreen SPF 60','Sunscreen',930,'tube','sun','sun',['derm','pk'],'Derm-brand sun cream under Rs 1,000','sun','all','mini')
add('tocobo-stick','Tocobo','Cotton Soft Sun Stick SPF 50+','Stick',4509,'tube','grn','sun',['new','kr'],'Reapply over makeup or on the go','sun','all','new')
add('skin1004-sun','SKIN1004','Hyalu-Cica Water-fit Sun Serum','Sun serum',4289,'tube','blu','sun',['new','kr'],'SPF that feels like a serum','sun sensitive','all','new')
add('rederm-tressfix','Rederm','Tressfix Hairfall & Anti-Dandruff Shampoo','Shampoo',1150,'bottle','grn','hair',['derm','pk'],"Pakistan's number one hair fall shampoo online",'hairfall dandruff','all','best')
add('jenpharm-anagrow','Jenpharm','Anagrow Biotin Shampoo','Shampoo',988,'bottle','blu','hair',['derm','pk'],'Biotin shampoo from a trusted derm brand','hairfall','all','mini')
add('fino-mask','Fino','Premium Touch Hair Mask','Mask',3299,'jar','hair','hair',[],'The best-loved mask for dry, damaged hair','frizz','all','best')
add('mielle-rosemary','Mielle','Rosemary Mint Strengthening Hair Masque','Masque',4079,'jar','rosem','hair',[],'The rosemary original that started the trend','hairfall frizz','all','new')
add('vaseline-gluta','Vaseline','Gluta-Hya Serum Burst Lotion','Lotion',2399,'pump','rose','body',[],'The body lotion with actives Pakistan buys most','body dull','all','best')
add('vaseline-gel-oil','Vaseline','Intensive Care Body Gel Oil','Gel oil',3439,'pump','body','body',[],'Glow without the grease','body dry','all','')
add('kn-vp','Koh-e-Noor by Nexus Beauty','Vanilla Pistachio Hair & Body Mist','100 ml',1290,'perfume','vp','fragrance',['own','new'],'Our own mist. Launch price.','scent','all','best new',was=1450)
add('kn-oud','Koh-e-Noor by Nexus Beauty','Oud Amber Hair & Body Mist','100 ml',1290,'perfume','oud','fragrance',['own','new'],'Warm oud, light enough for every day','scent','all','',was=1450)
add('kn-musk','Koh-e-Noor by Nexus Beauty','Fresh Musk Hair & Body Mist','100 ml',1290,'perfume','musk','fragrance',['own','new'],'Clean cotton and white musk','scent','all','',was=1450)
add('kn-vp-30','Koh-e-Noor by Nexus Beauty','Vanilla Pistachio Mist, travel size','30 ml',490,'perfume','vp','fragrance',['own','mini'],'Try the scent before the full size','scent','all','mini')
add('hype-fruit','Hype by Capri','Fruit Frenzy Body Spray','150 ml',550,'bottle','pink','fragrance',['pk'],'The best-selling sweet body spray','scent','all','')
add('medora-matte','Medora','Matte Lipstick','Lipstick',300,'lipstick','lip','makeup',['pk'],"Pakistan's most-reviewed lipstick",'lips','all','best mini')
add('swiss-miss-60','Swiss Miss','Natural Matte Lipstick 60','Lipstick',400,'lipstick','tint','makeup',['pk'],'A local matte favourite','lips','all','mini')
add('garnier-men-acno','Garnier Men','Acno Fight Face Wash','Face wash',1050,'tube','men','men',[],'For oily, acne-prone skin','acne men','oily','')
add('rivaj-beard','Rivaj','Beard Oil','Beard oil',695,'dropper','hair','men',['pk'],'Softens and tames the beard','men','all','mini')
add('daisy-plus','Gillette','Daisy Plus Razors','Pack',599,'tube','pink','personal',[],'The top hair removal pick online','','all','mini')
add('st-ives-apricot','St. Ives','Fresh Skin Apricot Face Scrub','Face scrub',1299,'tube','body','skincare',[],'Weekly exfoliation for smoother, brighter skin','dull spots','oily combo normal','')
add('dove-body-polish','Dove','Exfoliating Body Polish','Body polish',2499,'jar','rose','body',[],'Buffs away dry, rough skin on the body','body dull','all','')
add('cetaphil-gentle','Cetaphil','Gentle Skin Cleanser','Cleanser',2199,'pump','blu','skincare',[],'A soap-free cleanser for dry and sensitive skin','dry sensitive','dry sensitive normal','')
add('rederm-clariderm','Rederm','Clariderm Face Wash','Face wash',1050,'tube','grn','skincare',['derm','pk'],'A derm-brand wash for oily, acne-prone skin','acne','oily combo','')
BYID={p['id']:p for p in P}

# ---------- Problem-solution routines ----------
# Each step: (when, role, product id, how to use)
RG=[
 dict(key='brightening',name='Complete Brightening Kit',short='Brightening',tone='yel',
  who='For dull skin, dark spots, acne marks and uneven tone on the face and body.',
  steps=[('Morning & night','Cleanse','loreal-glycolic','Massage for 30 seconds, rinse with lukewarm water.'),
         ('Morning','Vitamin C serum','garnier-vitc','3–4 drops on dry skin before moisturiser.'),
         ('Night','Dark spot serum','axis-y-5','2–3 drops on clean skin. Start with the 5 ml mini.'),
         ('Morning & night','Moisturise','ponds-gel','A pea-sized amount. Light enough for the heat.'),
         ('Morning','Sunscreen','estelin-70','Two finger-lengths for face and neck. Reapply every 2–3 hours outdoors.'),
         ('Once or twice a week','Face scrub','st-ives-apricot','Gently, on damp skin. Skip the night you use the serum.'),
         ('Twice a week','Body polish','dove-body-polish','In the shower on elbows, knees and legs.'),
         ('Daily','Body lotion','vaseline-gluta','After every shower, on slightly damp skin.')],
  tips=['Wear sunscreen every single morning. Without it, spots come back.','Be patient: expect lighter marks in 4–8 weeks, not days.','Exfoliate a maximum of twice a week, and never on the same night as your dark spot serum.','Patch test each new product on your jaw for 2 days.','Add one new product at a time, one week apart.'],
  avoid=['"Whitening" or fairness creams without a full ingredient list. Some contain mercury or steroids.','Lemon, toothpaste or baking soda on your face.','Scrubbing broken or sunburnt skin.']),
 dict(key='acne',name='Clear Skin Kit',short='Acne',tone='grn',
  who='For oily skin, breakouts, bumps and the red marks acne leaves behind.',
  steps=[('Morning & night','Cleanse','rederm-clariderm','Twice a day only. Over-washing makes skin oilier.'),
         ('Night','Treat','anua-azelaic','A thin layer on clean skin. Calms redness and bumps.'),
         ('Morning & night','Moisturise','ponds-gel','Yes, oily skin needs moisture too.'),
         ('Morning','Sunscreen','neobrella-60','Every morning. Acne marks darken in the sun.')],
  tips=['Give it 6–8 weeks before judging results.','Change your pillow cover twice a week.','Keep hands, phone and hair oil away from your face.','See a dermatologist for painful, deep or scarring acne.'],
  avoid=['Popping or squeezing spots.','Using several acne treatments at once.','Heavy oils and thick creams on the face.']),
 dict(key='hairfall',name='Hair Fall Kit',short='Hair fall',tone='hair',
  who='For thinning hair, hair fall after washing and dandruff.',
  steps=[('3 times a week','Anti-dandruff shampoo','rederm-tressfix','Leave the lather on your scalp for 2 minutes, then rinse.'),
         ('On other wash days','Biotin shampoo','jenpharm-anagrow','Massage into the scalp with your fingertips, not nails.'),
         ('Once a week','Strengthening masque','mielle-rosemary','On lengths and ends for 10–15 minutes.'),
         ('Once a week','Repair mask','fino-mask','Alternate with the masque on dry, damaged hair.')],
  tips=['Use it consistently for 8–12 weeks. Hair grows slowly.','Wash your scalp regularly. A clean scalp holds hair better.','Loosen tight ponytails and buns.','If hair falls in patches or suddenly, see a doctor and check iron, vitamin D and thyroid.'],
  avoid=['Hot water on the scalp.','Brushing wet hair hard. Use a wide-tooth comb.','Leaving hair oil in for days.']),
 dict(key='dry',name='Dry & Sensitive Skin Kit',short='Dry skin',tone='lav',
  who='For tight, flaky or easily irritated skin, especially in winter.',
  steps=[('Morning & night','Gentle cleanser','cetaphil-gentle','Soap-free. Pat dry, don\'t rub.'),
         ('Morning','Hydrating serum','medicube-pdrn','On slightly damp skin to hold in water.'),
         ('Morning & night','Barrier cream','dr-althea-345','A generous layer, especially at night.'),
         ('Morning','Sunscreen','boj-relief-sun','A creamy SPF 50+ that doesn\'t dry skin out.')],
  tips=['Apply moisturiser within a minute of washing.','Use lukewarm water, never hot.','Keep your routine short: 3–4 products.','Use a humidifier or keep a bowl of water in heated rooms in winter.'],
  avoid=['Foaming, "deep cleansing" face washes.','Scrubs while your skin is flaking.','Products with strong fragrance or alcohol.']),
]
RGBY={r['key']:r for r in RG}
def rg_block(r, full=True, heading='h3'):
    seen=[]; rows=''
    for when,role,pid,how in r['steps']:
        p=BYID[pid]
        rows+=f'''<label class="rg__item"><input type="checkbox" checked data-rg-id="{pid}" data-price="{p['price']}"><span class="thumb" style="--tone:{p['tone']};--bg:{p['bg']}">{shape(p['shape'])}</span><span class="rg__txt"><small>{e(when)} · {e(role)}</small><b>{e(p['brand'].replace(' by Nexus Beauty',''))} {e(p['name'])}</b>{f'<em>{e(how)}</em>' if full else ''}</span><span class="p">{rs(p['price'])}</span></label>'''
    tot=sum(BYID[x[2]]['price'] for x in r['steps']); disc=int(round(tot*.85))
    tips=''.join(f'<li>{e(t)}</li>' for t in r['tips']); avoid=''.join(f'<li>{e(t)}</li>' for t in r['avoid'])
    guide=f'''<div class="rg__tips"><h4>Get the best results</h4><ol>{tips}</ol><h4>Avoid</h4><ul>{avoid}</ul></div>''' if full else ''
    return f'''<article class="regimen" id="rg-{r['key']}" data-rg data-rg-name="{e(r['name'])}">
 <div class="rg__head"><p class="eyebrow">{len(r['steps'])}-product routine · 15% off 3 or more</p><{heading} class="rg__title">{e(r['name'])}</{heading}><p>{e(r['who'])}</p></div>
 <div class="rg__grid">
  <div class="rg__list" role="group" aria-label="Products in the {e(r['name'])}">{rows}</div>
  <div class="rg__side">
   <div class="rg__buy"><div class="rg__line"><span>Selected: <b data-rg-count>{len(r['steps'])}</b> products</span><span data-rg-sub>{rs(tot)}</span></div><div class="rg__line rg__disc" data-rg-disc-row><span>Kit discount (15%)</span><span data-rg-disc>−{rs(tot-disc)}</span></div><div class="rg__line rg__tot"><span>Total</span><b data-rg-total>{rs(disc)}</b></div><button class="btn btn--p btn--lg btn--block" type="button" data-rg-add>Add {len(r['steps'])} products to bag</button><small data-rg-note>Untick anything you already own.</small></div>
   {guide}
  </div>
 </div>
</article>'''

# ---------- Videos ----------
# Links open YouTube search results. Paste a real video ID into data-yt to play it inside the page on your live site.
VIDEOS=[('Review','AXIS-Y Dark Spot Serum: honest reviews','axis-y dark spot correcting glow serum review','yel','dropper',''),
 ('How to','How to apply sunscreen the right way','how to apply sunscreen correctly two finger rule','sun','tube',''),
 ('Review','Beauty of Joseon Relief Sun on oily skin','beauty of joseon relief sun review oily skin','sun','tube',''),
 ('How to','A simple routine for hair fall','hair fall routine shampoo scalp care dermatologist','hair','bottle','')]
def video_cards(vs):
    out=''
    for tag,title,q,t,sh,yt in vs:
        url='https://www.youtube.com/results?search_query='+q.replace(' ','+')
        out+=f'''<article class="video"><a class="video__poster" href="{url}" target="_blank" rel="noopener" data-yt="{yt}" data-title="{e(title)}" style="--tone:{T[t][0]};--bg:{T[t][1]}" aria-label="Watch: {e(title)} (opens YouTube)">{shape(sh)}<span class="video__play" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></span><span class="video__tag">{e(tag)}</span></a><h3>{e(title)}</h3><p>Watch on YouTube ↗</p></article>'''
    return out

PILL={'mini':('pill--mini','Mini'),'new':('pill--new','New'),'derm':('pill--derm','Derm brand'),'pk':('pill--pk','Pakistani'),'own':('pill--own','Our label'),'kr':('','Korea')}
def card(p):
    pills=''.join(f'<span class="pill {PILL[k][0]}">{PILL[k][1]}</span>' for k in p['pills'])
    was=f'<del><span class="sr-only">was </span>{rs(p["was"])}</del>' if p['was'] else ''
    return f'''<article class="card" data-id="{p['id']}" data-tabs="{p['tabs']}">
<div class="card__media" style="--tone:{p['tone']};--bg:{p['bg']}">{shape(p['shape'])}<div class="badges">{pills}</div></div>
<button class="wish" type="button" aria-pressed="false" data-wish="{p['id']}" aria-label="Save {e(p['name'])} to wishlist">{ic('heart')}</button>
<p class="card__brand">{e(p['brand'])}</p>
<h3 class="card__title"><a href="product.html">{e(p['name'])}</a></h3>
<p class="card__why">{e(p['why'])}</p>
<div class="card__meta"><p class="price">{rs(p['price'])}{was}</p><span class="size">{e(p['size'])}</span></div>
<button class="btn btn--d btn--block" type="button" data-add="{p['id']}">Add to bag</button>
</article>'''

LOGO=lambda tag='a',extra='': f'''<{tag} class="logo" {'href="index.html" aria-label="Nexus Beauty home"' if tag=='a' else ''}{extra}><svg viewBox="0 0 120 80" aria-hidden="true"><use href="#mark"/></svg><span class="logo__word"><b>Nexus</b><small>BEAUTY</small></span></{tag}>'''

COLS=[('Skincare','Serums, creams, cleansers','dropper','yel','collection-skincare.html'),('Sun Care','SPF, sticks, tinted','tube','sun','collection-sun-care.html'),('Hair Care','Hair fall, masks','bottle','hair','collection-hair-care.html'),
('Body Care','Lotions, gel oils','pump','body','collection-body-care.html'),('Fragrance','Mists, perfume','perfume','rose','collection-fragrance.html'),('Makeup','Lips, blush, face','lipstick','lip','collection-makeup.html'),
("Men's Grooming",'Face wash, beard','tube','men','collection-mens-grooming.html'),('Personal Care','Razors, hygiene','jar','grn','collection-personal-care.html'),('Pakistani Derm','Rederm, Estelin, Jenpharm','bottle','blu','collection-pakistani-derm.html'),
('Minis & Trial Sizes','Under Rs 1,000','dropper','pink','collection-minis.html'),('Koh-e-Noor','Our own label','perfume','vp','collection-koh-e-noor.html'),('Gifts & Sets','Routine kits','jar','lav','solutions.html')]
cols=''.join(f'<a class="col" href="{h}"><span class="col__img" style="--tone:{T[t][0]};--tile:{T[t][1]}">{shape(s)}</span><span>{e(n)}<small>{e(d)}</small></span></a>' for n,d,s,t,h in COLS)
MEGA='<a href="shop.html"><b>Shop all products</b><small>'+str(len(P))+' products</small></a>'+''.join(f'<a href="{h}">{e(n)}<small>{e(d)}</small></a>' for n,d,_,_,h in COLS)+'<a href="collection-new-arrivals.html">New arrivals<small>Just landed</small></a><a href="brands.html">Brands A–Z<small>40+ brands</small></a><a href="offers.html">Offers<small>Kits, codes, free delivery</small></a><a href="compare.html">Compare<small>Product vs product</small></a>'

TABS=[('best','Best sellers','The products Pakistan buys most, one or two from each category.'),('new','New arrivals','Trending in the US and Korea, and only just arriving in Pakistan.'),('mini','Under Rs 1,000','Minis and everyday favourites to try without spending much.')]
tabs=''.join(f'<button class="chip" type="button" role="tab" aria-selected="{str(i==0).lower()}" data-tab="{k}" data-note="{e(n)}">{e(l)}</button>' for i,(k,l,n) in enumerate(TABS))
picks=''.join(card(p) for p in P if p['tabs'])

CONCERNS=[('hairfall','Hair fall'),('acne','Acne & breakouts'),('spots','Pigmentation & dark spots'),('dull','Dullness'),('dry','Dehydration & barrier'),('dandruff','Dandruff'),('sun','Sun protection'),('body','Body texture'),('scent','Long-lasting scent'),('lips','Lips'),('men',"Men's grooming")]
concerns=''.join(f'<button type="button" data-concern="{k}">{ic("spark")}{e(n)}</button>' for k,n in CONCERNS)

KITS=[('The Bright & Protected Edit','For dull skin and dark spots',['garnier-vitc','axis-y-5','boj-relief-sun'],'yel'),
('Hair Fall Reset','For hair fall and dandruff',['rederm-tressfix','jenpharm-anagrow'],'grn'),
('Barrier Comfort Ritual','For dry, sensitive skin',['dr-althea-345','estelin-70'],'lav'),
('Gourmand Glow Set','Scent that lasts all day',['vaseline-gluta','kn-vp'],'vp')]
def kit(name,sub,ids,t):
    tot=sum(BYID[i]['price'] for i in ids); pr=round(tot*.85/10)*10-1 if False else int(round(tot*.85)); save=tot-pr
    art=''.join(f'<svg viewBox="0 0 100 160" aria-hidden="true" style="color:{BYID[i]["tone"]}"><use href="#i-{BYID[i]["shape"]}"/></svg>' for i in ids)
    lis=''.join(f'<li><span>{e(BYID[i]["brand"].replace(" by Nexus Beauty",""))} {e(BYID[i]["name"])}</span><span>{rs(BYID[i]["price"])}</span></li>' for i in ids)
    kid='kit-'+name.lower().replace(' ','-').replace('&','and')
    return f'''<article class="kit"><div class="kit__art" style="--bg:{T[t][1]}">{art}</div><div class="kit__body"><p class="eyebrow">Routine kit · 15% off</p><h3>{e(name)}</h3><p class="card__why">{e(sub)}</p><ul>{lis}</ul><div class="kit__price"><b>{rs(pr)}</b><del class="size">{rs(tot)}</del><span class="save">Save {rs(save)}</span></div></div><button class="btn btn--p" type="button" data-kit="{','.join(ids)}" data-kit-name="{e(name)}" data-kit-price="{pr}">Add kit to bag</button></article>'''
kits=''.join(kit(*k) for k in KITS)

PK=[('Rederm','Hair fall and acne care'),('Neophar','Everyday sunscreen'),('Estelin','Sun care and vitamin C'),('Jenpharm','Hair growth care'),('B&B Dermaceuticals','Gel SPF and body wash'),('Beautenic','Modern actives, made locally'),('Glow & Glee','Everyday skincare'),('Conatural','Plant-based body care'),('Rivaj','Colour cosmetics'),('Medora','Classic matte lipsticks'),('Swiss Miss','Soft colour and care'),('Hype by Capri','Sweet body sprays'),('Hemani','Oils and scent'),('Saeed Ghani','Rose water and herbal care'),('Koh-e-Noor','Our own fragrance label')]
INTL=[("L'Oréal Paris",'France'),('Garnier','France'),("Pond's",'Everyday hydration'),('Vaseline','Body care'),('Beauty of Joseon','Korea'),('AXIS-Y','Korea'),('Dr. Althea','Korea'),('Medicube','Korea'),('Anua','Korea'),('SKIN1004','Korea'),('Tocobo','Korea'),('Laneige','Korea'),('Fino','Japan'),('TRESemmé','Hair care'),('Dove','Body and hair'),('OGX','Hair care'),('Mielle','USA'),('Maybelline','Makeup'),('Lattafa','UAE fragrance'),('Garnier Men','Men')]
def initials(n):
    w=[x for x in n.replace('&',' ').replace("'",'').replace('.',' ').split() if x[0].isalpha()]
    return (w[0][0]+(w[1][0] if len(w)>1 else '')).upper()
def brands(lst):
    return ''.join(f'<a class="brand{" brand--own" if n=="Koh-e-Noor" else ""}" href="brand.html"><span class="brand__mark" aria-hidden="true">{e(initials(n))}</span><b>{e(n)}</b><span>{e(d)}</span></a>' for n,d in lst)

SEARCH=[dict(id=p['id'],n=p['name'],b=p['brand'],s=p['size'],p=p['price'],sh=p['shape'],t=p['tone'],bg=p['bg'],c=p['cat'],k=p['concerns']) for p in P]
STEPS=[('We buy from the source','Every product comes from the brand or its authorised distributor. We keep the invoice for every batch.'),
('We check every batch','Before a product goes on the shelf we record its batch number and expiry date.'),
('We print it on your order','Your parcel sticker lists the batch and expiry of each item, so you can check it before you open it.'),
('You pay when it arrives','Cash on delivery anywhere in Pakistan. If anything looks wrong, send a photo and we refund you in full.')]
steps=''.join(f'<div class="step"><span class="step__n">Step {i+1}</span><h3>{e(a)}</h3><p>{e(b)}</p></div>' for i,(a,b) in enumerate(STEPS))
FAQ=[('Are your products original?','Yes. We buy only from brands and their authorised distributors, and every order shows the batch number and expiry date of each product. If anything looks wrong when it arrives, send us a photo and we refund you in full.'),
('Can I pay cash on delivery?','Yes. Cash on delivery works everywhere in Pakistan. You can also pay by card, JazzCash or Easypaisa.'),
('How long does delivery take?','Orders placed before 3pm leave the same day. Karachi and Lahore: 1–2 working days. Islamabad and Rawalpindi: 2–3 working days. Other cities: 3–5 working days.'),
('How much is delivery?','Delivery is free on orders over Rs 5,000. Below that it costs Rs 250.'),
('What is your return policy?','You can return unopened, unused products in their original packaging within 7 days. For hygiene reasons, opened products can only be returned if they arrived damaged or faulty.'),
('How do you source international brands?','Through each brand\'s authorised distributor or importer in Pakistan. We never sell "first copy" or grey-market stock. Ask us on WhatsApp for the source of any product before you buy.')]
faq=''.join(f'<details><summary>{e(q)}</summary><p>{e(a)}</p></details>' for q,a in FAQ)
ld=[{"@context":"https://schema.org","@type":"OnlineStore","name":"Nexus Beauty","slogan":"Beauty, checked.","areaServed":"PK","currenciesAccepted":"PKR","paymentAccepted":"Cash on delivery, Card, JazzCash, Easypaisa"},
{"@context":"https://schema.org","@type":"FAQPage","mainEntity":[{"@type":"Question","name":q,"acceptedAnswer":{"@type":"Answer","text":a}} for q,a in FAQ]}]
POSTS=[('Skin science','How to build a routine for Pakistan\'s heat','Light gels, the right sunscreen and when to skip heavy creams.','5 min read','sun','tube'),
('Ingredient guide','PDRN vs niacinamide: what to know','What the new biotech ingredient does, and who actually needs it.','6 min read','pink','dropper'),
('Fragrance','From body spray to perfume','How to layer sweet, oud and musk scents so they last all day.','4 min read','oud','perfume')]
posts=''.join(f'<article class="post"><div class="post__art" style="--bg:{T[t][1]};--tone:{T[t][0]}">{shape(s)}</div><p class="eyebrow">{e(a)}</p><h3>{e(b)}</h3><p>{e(c)}</p><p class="meta">{e(d)} · Coming soon</p></article>' for a,b,c,d,t,s in POSTS)

LOGO_HOME=LOGO()
ICON_USER='<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/></svg>'
RG_TABS=''.join('<button class="chip" type="button" role="tab" aria-selected="%s" data-rg-tab="%s">%s</button>' % (str(i==0).lower(), r['key'], e(r['short'])) for i,r in enumerate(RG))
RG_PANELS=''.join(rg_block(r).replace('<article class="regimen"','<article class="regimen"'+('' if i==0 else ' hidden'),1) for i,r in enumerate(RG))
NAVLINKS='<a href="solutions.html">Routines</a><a href="brands.html">Brands</a><a href="offers.html">Offers</a><a href="blog.html">Journal</a><a href="about.html">About</a>'
BODY=f'''<a class="skip" href="#main">Skip to content</a>
{DEFS}
<div class="announce" aria-live="polite"><span id="announce">Free delivery on orders over <b>Rs 5,000</b></span></div>
<header class="header">
 <div class="wrap header__bar">
  <button class="icon-btn menu-btn" type="button" data-open="mnav" aria-label="Open menu">{ic('menu')}</button>
  {LOGO_HOME}
  <nav class="nav" aria-label="Main">
   <div class="mega-wrap"><button type="button" id="shopBtn" aria-expanded="false" aria-controls="mega">Shop {ic('down',' style="width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2"')}</button>
    <div class="mega" id="mega" hidden><div class="wrap">{MEGA}</div></div></div>
   {NAVLINKS}
  </nav>
  <div class="actions">
   <button class="icon-btn" type="button" id="searchBtn" aria-expanded="false" aria-controls="searchpanel" aria-label="Search">{ic('search')}</button>
   <a class="icon-btn hide-sm" href="account.html" aria-label="Your account">{ICON_USER}</a>
   <a class="icon-btn" href="wishlist.html" id="wishBtn" aria-label="Wishlist">{ic('heart')}<span class="count" id="wishCount" hidden>0</span></a>
   <button class="icon-btn" type="button" data-open="drawer" aria-label="Open bag">{ic('bag')}<span class="count" id="cartCount" hidden>0</span></button>
  </div>
 </div>
 <div class="searchpanel" id="searchpanel" hidden>
  <div class="wrap">
   <form class="sfield" role="search" id="searchForm"><label class="sr-only" for="q">Search products, brands and concerns</label>{ic('search')}<input id="q" type="search" placeholder="Search a product, brand or concern, e.g. hair fall" autocomplete="off"></form>
   <div class="popular"><span>Popular:</span><button type="button" data-q="sunscreen">Sunscreen</button><button type="button" data-q="hair fall">Hair fall</button><button type="button" data-q="AXIS-Y">AXIS-Y</button><button type="button" data-q="PDRN">PDRN</button><button type="button" data-q="mist">Body mist</button><button type="button" data-q="rosemary">Rosemary</button></div>
   <div class="results" id="results" aria-live="polite"></div>
  </div>
 </div>
</header>
<nav class="mnav" id="mnav" aria-label="Mobile" aria-hidden="true">
 <div class="mnav__head">{LOGO('span')}<button class="icon-btn" type="button" data-close aria-label="Close menu">{ic('close')}</button></div>
 <p class="eyebrow">Shop</p>{MEGA}
 <p class="eyebrow">Explore</p>{NAVLINKS}
</nav>

<main id="main">
<section class="hero" id="top" aria-labelledby="hero-h">
 <div class="wrap hero__grid">
  <div>
   <p class="eyebrow">Beauty, checked.</p>
   <h1 id="hero-h">Good skin.<br><em>Good sense.</em></h1>
   <p class="lede">Original Pakistani derm heroes, Korean icons and the next wave of beauty, chosen for your routine, your budget and our climate. Every order shows the batch number and expiry date.</p>
   <div class="hero__ctas"><a class="btn btn--p btn--lg" href="#picks">Shop best sellers</a><a class="btn btn--g btn--lg" href="#finder">Find my routine</a></div>
   <ul class="checks"><li>{ic('check')}Cash on delivery</li><li>{ic('check')}Delivered across Pakistan</li><li>{ic('check')}Minis from Rs 490</li></ul>
  </div>
  <div class="hero__art" aria-hidden="true">
   <div class="disc"></div>
   <div class="p p1">{shape('tube')}</div><div class="p p3">{shape('perfume')}</div><div class="p p2">{shape('dropper')}</div>
   <div class="batch"><b>✓ Verified original</b><span>BATCH&nbsp;&nbsp;AX2609K</span><span>MFG&nbsp;&nbsp;&nbsp;&nbsp;03/2026</span><span>EXP&nbsp;&nbsp;&nbsp;&nbsp;02/2029</span></div>
   <div class="hero__note"><b>New: Koh-e-Noor</b>Our own hair &amp; body mists, Rs 1,290</div>
  </div>
 </div>
</section>

<section class="trust" aria-label="Why shop with us"><ul class="wrap">
 <li>{ic('shield')}<div><b>Batch &amp; expiry shown</b><span>On every order</span></div></li>
 <li>{ic('cash')}<div><b>Cash on delivery</b><span>Anywhere in Pakistan</span></div></li>
 <li>{ic('truck')}<div><b>Same-day dispatch</b><span>Order before 3pm</span></div></li>
 <li>{ic('return')}<div><b>7-day returns</b><span>Unopened products</span></div></li>
</ul></section>

<section class="section" id="shop" aria-labelledby="col-h">
 <div class="wrap">
  <div class="sec-head"><div><p class="eyebrow">Start here</p><h2 class="h2" id="col-h">Shop by collection</h2></div><p class="lede" style="max-width:44ch">From pharmacy-trusted derm care to scent that lasts. Everything you need, nothing you don't.</p></div>
  <div class="cols">{cols}</div>
 </div>
</section>

<section class="section section--tint" id="picks" aria-labelledby="picks-h">
 <div class="wrap">
  <div class="sec-head"><div><p class="eyebrow">Proven in Pakistan</p><h2 class="h2" id="picks-h">Best sellers &amp; new arrivals</h2></div><a class="link" href="shop.html#all">Shop all products</a></div>
  <div class="tabs" role="tablist" aria-label="Product lists">{tabs}</div>
  <p class="tab-note" id="tabNote">{e(TABS[0][2])}</p>
  <div class="grid" id="pickGrid">{picks}</div>
 </div>
</section>

<section class="section" id="concerns" aria-labelledby="con-h">
 <div class="wrap">
  <div class="sec-head"><div><p class="eyebrow">Your skin, your starting point</p><h2 class="h2" id="con-h">Shop by concern</h2><p class="lede">Pick a concern and we'll show every product that targets it.</p></div></div>
  <div class="concerns">{concerns}</div>
 </div>
</section>

<section class="section section--tint" id="kits" aria-labelledby="kit-h">
 <div class="wrap">
  <div class="sec-head"><div><p class="eyebrow">Shop by problem</p><h2 class="h2" id="kit-h">Complete kits, with a guide to get results</h2><p class="lede">Every product you need for one problem, in the order you use them, plus what to do and what to avoid. 15% off when you choose 3 or more.</p></div><a class="link" href="solutions.html">See all routines</a></div>
  <div class="tabs" role="tablist" aria-label="Choose a problem">{RG_TABS}</div>
  <div class="rg-panels" style="margin-top:18px">{RG_PANELS}</div>
 </div>
</section>

<section class="section" aria-labelledby="own-h">
 <div class="wrap own">
  <div class="own__copy">
   <p class="eyebrow">Koh-e-Noor by Nexus Beauty</p>
   <h2 class="h2" id="own-h">Our own hair &amp; body mists</h2>
   <p>Body sprays cost Rs 550 and fade fast. Perfume mists cost Rs 2,000 or more. Koh-e-Noor sits in between: 100 ml of long-lasting scent for your hair, skin and clothes, made in Pakistan.</p>
   <button class="btn btn--w btn--lg" type="button" data-add="kn-vp-30">Try a 30 ml for Rs 490</button>
  </div>
  <div class="scents">
   {''.join(f'<button class="scent" type="button" data-add="{i}"><span class="scent__b" style="--tone:{BYID[i]["tone"]};--bg:{BYID[i]["bg"]}">{shape("perfume")}</span><b>{e(BYID[i]["name"].replace(" Hair & Body Mist",""))}</b><small>{d}</small><span class="price">{rs(1290)}</span></button>' for i,d in [('kn-vp','Pistachio, vanilla, soft musk'),('kn-oud','Saffron, oud, warm amber'),('kn-musk','Cotton flower, white musk')])}
  </div>
 </div>
</section>

<section class="section section--tint" id="brands" aria-labelledby="br-h">
 <div class="wrap">
  <div class="sec-head"><div><p class="eyebrow">Know what's inside</p><h2 class="h2" id="br-h">Pakistani favourites &amp; global icons</h2><p class="lede">Trusted local derm names next to the international brands worth bringing home.</p></div></div>
  <div class="tabs" role="tablist" aria-label="Brand lists"><button class="chip" type="button" role="tab" aria-selected="true" data-brands="pk">Pakistani brands</button><button class="chip" type="button" role="tab" aria-selected="false" data-brands="intl">International brands</button></div>
  <div class="brand-grid" id="brands-pk" style="margin-top:18px">{brands(PK)}</div>
  <div class="brand-grid" id="brands-intl" style="margin-top:18px" hidden>{brands(INTL)}</div>
 </div>
</section>

<section class="section" aria-labelledby="off-h">
 <div class="wrap offer">
  <div><p class="eyebrow">Deal of the week</p><h2 class="h2" id="off-h">Any 3 routine essentials, 15% off</h2><p>Pick any three products from a problem kit, or build your own routine. The offer ends Sunday at midnight, Pakistan time.</p><a class="btn btn--w" href="#kits">Shop routine kits</a></div>
  <div class="timer" id="timer" aria-label="Time left in this week's offer"><div><b>00</b><small>days</small></div><div><b>00</b><small>hours</small></div><div><b>00</b><small>mins</small></div><div><b>00</b><small>secs</small></div></div>
 </div>
</section>

<section class="section section--tint" id="checked" aria-labelledby="chk-h">
 <div class="wrap">
  <div class="sec-head"><div><p class="eyebrow">Proof over promises</p><h2 class="h2" id="chk-h">How we check every order</h2><p class="lede">Fake and expired products are the biggest worry when buying beauty online in Pakistan. This is how we make sure yours is original and fresh.</p></div></div>
  <div class="steps">{steps}</div>
  <div class="proof"><div class="batch"><b>✓ Checked before packing</b><span>ORDER&nbsp;&nbsp;NX-104829</span><span>AXIS-Y Glow Serum 5 ml</span><span>BATCH&nbsp;&nbsp;AX2609K · EXP 02/2029</span></div><div><h3>This is what your parcel sticker looks like</h3><p>Every item is listed with its batch number and expiry date. Check it against the box before you pay the rider. We're a new store, so we don't have customer reviews yet. We'd rather earn them than fake them.</p></div></div>
 </div>
</section>

<section class="section" id="videos" aria-labelledby="vid-h">
 <div class="wrap">
  <div class="sec-head"><div><p class="eyebrow">Watch before you buy</p><h2 class="h2" id="vid-h">Reviews and how-to videos</h2><p class="lede">Real reviews from independent creators, and short guides to using your products well.</p></div></div>
  <div class="videos">{video_cards(VIDEOS)}</div>
 </div>
</section>

<section class="section" aria-labelledby="why-h">
 <div class="wrap">
  <div class="sec-head"><div><p class="eyebrow">Why we exist</p><h2 class="h2" id="why-h">Why Nexus Beauty</h2></div></div>
  <div class="why">
   <div><h3>Proof over promises</h3><p>Batch and expiry on every order, and sourcing we can show you.</p></div>
   <div><h3>Local trust, global curiosity</h3><p>A proper home for Pakistani derm brands, next to the best international names.</p></div>
   <div><h3>Advice by concern</h3><p>Shop for hair fall, acne or dark spots, and get free WhatsApp advice from our team.</p></div>
   <div><h3>Made for our climate</h3><p>Light textures, sunscreen you'll actually reapply, and scents that last in the heat.</p></div>
  </div>
 </div>
</section>

<section class="section section--tint" id="finder" aria-labelledby="fd-h">
 <div class="wrap finder">
  <div class="finder__intro">
   <p class="eyebrow">Not sure where to begin?</p>
   <h2 class="h2" id="fd-h">Find your routine in 3 taps</h2>
   <p class="lede">Choose your main concern, your skin type and your budget. We'll suggest a simple routine from products that already work for people in Pakistan.</p>
  </div>
  <div style="display:grid;gap:18px">
   <form id="finderForm">
    <fieldset class="q"><legend>1. What's your main concern?</legend><div class="opts">
     {''.join(f'<label><input type="radio" name="concern" value="{k}"{" checked" if k=="spots" else ""}><span>{n}</span></label>' for k,n in [('spots','Dark spots'),('acne','Acne'),('dull','Dullness'),('dry','Dryness'),('hairfall','Hair fall'),('sun','Sun protection')])}
    </div></fieldset>
    <fieldset class="q"><legend>2. Your skin type</legend><div class="opts">
     {''.join(f'<label><input type="radio" name="skin" value="{k}"{" checked" if k=="oily" else ""}><span>{n}</span></label>' for k,n in [('oily','Oily'),('combo','Combination'),('dry','Dry'),('sensitive','Sensitive'),('normal','Normal')])}
    </div></fieldset>
    <fieldset class="q"><legend>3. Your budget</legend><div class="opts">
     {''.join(f'<label><input type="radio" name="budget" value="{k}"{" checked" if k=="3000" else ""}><span>{n}</span></label>' for k,n in [('3000','Under Rs 3,000'),('6000','Rs 3,000–6,000'),('99999','No limit')])}
    </div></fieldset>
   </form>
   <div class="routine" id="routine" aria-live="polite"></div>
  </div>
 </div>
</section>

<section class="section" aria-labelledby="faq-h">
 <div class="wrap" style="display:grid;gap:24px">
  <div><p class="eyebrow">The reassuring bits</p><h2 class="h2" id="faq-h">Questions, answered</h2></div>
  <div class="faq">{faq}</div>
 </div>
</section>

<section class="section section--tint" id="journal" aria-labelledby="jr-h">
 <div class="wrap">
  <div class="sec-head"><div><p class="eyebrow">The Nexus journal</p><h2 class="h2" id="jr-h">Guides for better routines</h2></div></div>
  <div class="posts">{posts}</div>
 </div>
</section>

<section class="section" aria-labelledby="su-h">
 <div class="wrap signup">
  <div><h2 class="h2" id="su-h">Get Rs 200 off your first order</h2><p>Join the Nexus list for routine tips, new arrivals and WhatsApp-only offers. One or two emails a week.</p></div>
  <form id="signupForm" data-signup novalidate><label class="sr-only" for="email">Email address</label><input id="email" type="email" placeholder="Your email address" autocomplete="email" required><button class="btn btn--d" type="submit">Join the list</button><small id="signupMsg" role="status"></small></form>
 </div>
</section>
</main>

<footer class="footer">
 <div class="wrap">
  <div class="footer__grid">
   <div class="footer__about">{LOGO('span')}<p>Pakistan's checked beauty store. Original skincare, hair care, fragrance and makeup, with the batch and expiry on every order.</p><p>WhatsApp: <b>+92 300 000 0000</b><br>Email: <b>care@nexusbeauty.pk</b><br>10am–10pm, every day</p></div>
   <div><h3>Shop</h3><ul><li><a href="shop.html">Shop all</a></li>{''.join(f'<li><a href="{h}">{e(n)}</a></li>' for n,_,_,_,h in COLS[:6])}<li><a href="collections.html">All collections</a></li></ul></div>
   <div><h3>Help</h3><ul><li><a href="track-order.html">Track my order</a></li><li><a href="shipping.html">Delivery information</a></li><li><a href="returns.html">Returns &amp; refunds</a></li><li><a href="authenticity.html">How we check authenticity</a></li><li><a href="faq.html">FAQs</a></li><li><a href="contact.html">Contact us</a></li></ul></div>
   <div><h3>Nexus Beauty</h3><ul><li><a href="about.html">About us</a></li><li><a href="blog.html">Journal</a></li><li><a href="compare.html">Product comparisons</a></li><li><a href="solutions.html">Routines</a></li><li><a href="account.html">My account</a></li><li><a href="sitemap.html">Sitemap</a></li></ul></div>
  </div>
  <div class="footer__bottom"><span>© 2026 Nexus Beauty. Beauty, checked. · <a href="privacy.html">Privacy</a> · <a href="terms.html">Terms</a></span><div class="pay" aria-label="Payment methods"><span>COD</span><span>VISA</span><span>MASTERCARD</span><span>JAZZCASH</span><span>EASYPAISA</span></div></div>
 </div>
</footer>

<div class="scrim" id="scrim"></div>
<aside class="drawer" id="drawer" aria-label="Shopping bag" aria-hidden="true">
 <div class="drawer__head"><h2>Your bag</h2><button class="icon-btn" type="button" data-close aria-label="Close bag">{ic('close')}</button></div>
 <div class="ship"><span id="shipText">Free delivery over Rs 5,000</span><div class="ship__track"><div class="ship__fill" id="shipFill"></div></div></div>
 <div class="drawer__items" id="cartItems"></div>
 <div class="drawer__foot"><div class="total"><span>Subtotal</span><span id="subtotal">Rs 0</span></div><button class="btn btn--p btn--lg btn--block" type="button" id="checkout">Checkout securely</button><small>Cash on delivery, cards, JazzCash and Easypaisa accepted.</small></div>
</aside>
<div class="toast" id="toast" role="status" aria-live="polite"></div>
<a class="wa-float" href="https://wa.me/923000000000" target="_blank" rel="noopener" aria-label="Chat with us on WhatsApp"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.6-1.2A9 9 0 1 0 12 3Z"/><path d="M8.8 8.6c.2-.5.4-.5.7-.5h.5c.2 0 .4 0 .5.4l.7 1.7c.1.2 0 .4-.1.5l-.5.6c-.1.1-.2.3 0 .5.4.8 1.6 2 2.5 2.4.2.1.4.1.5 0l.6-.7c.1-.2.3-.2.5-.1l1.6.8c.2.1.3.2.3.4 0 .6-.3 1.4-1 1.7-.6.3-1.4.4-3.2-.4-2-1-3.4-3-3.6-3.5-.3-.5-.7-1.5-.3-2.3Z"/></svg></a>
<button class="to-top" type="button" id="toTop" aria-label="Back to top" hidden><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 15 6-6 6 6"/></svg></button>
<div class="cookie" id="cookie" role="region" aria-label="Cookie notice" hidden><p>We use a few cookies to keep your bag and wishlist saved and to understand what shoppers like. <a href="privacy.html">Privacy policy</a></p><div><button class="btn btn--g" type="button" data-cookie="essential">Essential only</button><button class="btn btn--p" type="button" data-cookie="all">Accept all</button></div></div>
'''
JS=r'''
(function(){
"use strict";
var DATA=__DATA__, BY={}; DATA.forEach(function(p){BY[p.id]=p;});
var FREE=5000, KEY="nexus-beauty-bag";
function $(s,r){return (r||document).querySelector(s);} function $$(s,r){return [].slice.call((r||document).querySelectorAll(s));}
function rs(n){return "Rs "+Math.round(n).toLocaleString("en-US");}
function esc(s){return String(s).replace(/[&<>"]/g,function(c){return {"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;"}[c];});}
function thumb(p){return '<span class="thumb" style="--tone:'+p.t+';--bg:'+p.bg+'"><svg viewBox="0 0 100 160" aria-hidden="true"><use href="#i-'+p.sh+'"/></svg></span>';}

/* Announcement bar */
var msgs=["Free delivery on orders over <b>Rs 5,000</b>","Cash on delivery <b>anywhere in Pakistan</b>","Batch and expiry <b>shown on every order</b>"],ai=0,an=$("#announce");
if(!window.matchMedia("(prefers-reduced-motion: reduce)").matches) setInterval(function(){ai=(ai+1)%msgs.length;an.innerHTML=msgs[ai];},4000);

/* Toast */
var toastEl=$("#toast"),tt; function toast(m){toastEl.textContent=m;toastEl.classList.add("is-on");clearTimeout(tt);tt=setTimeout(function(){toastEl.classList.remove("is-on");},2200);}

/* Panels */
var scrim=$("#scrim");
function open(id){var el=document.getElementById(id);el.classList.add("is-open");el.setAttribute("aria-hidden","false");scrim.classList.add("is-open");}
function closeAll(){$$(".drawer,.mnav").forEach(function(el){el.classList.remove("is-open");el.setAttribute("aria-hidden","true");});scrim.classList.remove("is-open");toggleSearch(false);toggleMega(false);}
$$("[data-open]").forEach(function(b){b.addEventListener("click",function(){open(b.dataset.open);});});
$$("[data-close]").forEach(function(b){b.addEventListener("click",closeAll);});
scrim.addEventListener("click",closeAll);
document.addEventListener("keydown",function(e){if(e.key==="Escape")closeAll();});
$$(".mnav a").forEach(function(a){a.addEventListener("click",closeAll);});

/* Mega menu */
var shopBtn=$("#shopBtn"),mega=$("#mega");
function toggleMega(on){if(!mega)return;mega.hidden=!on;shopBtn.setAttribute("aria-expanded",on);}
shopBtn.addEventListener("click",function(e){e.stopPropagation();toggleMega(mega.hidden);toggleSearch(false);});
$$("#mega a").forEach(function(a){a.addEventListener("click",function(){toggleMega(false);});});
document.addEventListener("click",function(e){if(!e.target.closest(".mega-wrap"))toggleMega(false);if(!e.target.closest(".searchpanel")&&!e.target.closest("#searchBtn"))toggleSearch(false);});

/* Search */
var sp=$("#searchpanel"),sb=$("#searchBtn"),q=$("#q"),res=$("#results");
function toggleSearch(on){if(!sp)return;sp.hidden=!on;sb.setAttribute("aria-expanded",on);if(on){setTimeout(function(){q.focus();},30);}}
sb.addEventListener("click",function(){toggleSearch(sp.hidden);toggleMega(false);});
var ALIAS={"hair fall":"hairfall","sunscreen":"sun spf","spf":"sun","mist":"scent fragrance","perfume":"scent fragrance","acne":"acne","dark spots":"spots","pigmentation":"spots","dandruff":"dandruff","lipstick":"lips makeup"};
function search(v){
  v=(v||"").trim().toLowerCase(); if(!v){res.innerHTML="";return;}
  var extra=ALIAS[v]||"";
  var hits=DATA.filter(function(p){var hay=(p.n+" "+p.b+" "+p.c+" "+p.k+" "+p.s).toLowerCase();return hay.indexOf(v)>-1||(extra&&extra.split(" ").some(function(w){return hay.indexOf(w)>-1;}));});
  res.innerHTML=hits.length?hits.slice(0,8).map(function(p){return '<a href="product.html" data-add-search="'+p.id+'">'+thumb(p)+'<span><b>'+esc(p.n)+'</b><small>'+esc(p.b)+' · '+esc(p.s)+'</small></span><span class="p">'+rs(p.p)+'</span></a>';}).join(""):'<p>No products match "'+esc(v)+'". Try "sunscreen", "hair fall" or a brand name, or message us on WhatsApp.</p>';
}
q.addEventListener("input",function(){search(q.value);});
$("#searchForm").addEventListener("submit",function(e){e.preventDefault();var v=q.value.trim();if(v)location.href="search.html#"+v.toLowerCase().replace(/[^a-z0-9]+/g,"-").replace(/^-|-$/g,"");});
$$("[data-q]").forEach(function(b){b.addEventListener("click",function(){q.value=b.dataset.q;search(q.value);q.focus();});});
res.addEventListener("click",function(e){var a=e.target.closest("[data-add-search]");if(a)pushRecent(a.dataset.addSearch);});

/* Cart */
function load(){try{return JSON.parse(localStorage.getItem(KEY))||[];}catch(e){return [];}}
function save(){try{localStorage.setItem(KEY,JSON.stringify(cart));}catch(e){}}
var cart=load().filter(function(l){return l.kit||BY[l.id];});
function lineInfo(l){return l.kit?{n:l.name,s:"Routine kit",p:l.price,sh:"jar",t:"#8c2350",bg:"#f6e3eb"}:BY[l.id];}
function render(){
  var count=0,sum=0;cart.forEach(function(l){var p=lineInfo(l);count+=l.qty;sum+=p.p*l.qty;});
  var cc=$("#cartCount");cc.textContent=count;cc.hidden=!count;
  $("#subtotal").textContent=rs(sum);
  var left=Math.max(0,FREE-sum);
  $("#shipText").innerHTML=left>0?"You're <b>"+rs(left)+"</b> away from <b>free delivery</b>":"<b>You've unlocked free delivery.</b>";
  $("#shipFill").style.width=Math.min(100,sum/FREE*100)+"%";
  var el=$("#cartItems");
  el.innerHTML=cart.length?cart.map(function(l,i){var p=lineInfo(l);return '<div class="line">'+thumb(p)+'<div><b>'+esc(p.n)+'</b><small>'+esc(p.s)+'</small><div class="qty"><button type="button" data-q-i="'+i+'" data-d="-1" aria-label="One less">−</button><span>'+l.qty+'</span><button type="button" data-q-i="'+i+'" data-d="1" aria-label="One more">+</button></div></div><span class="p">'+rs(p.p*l.qty)+'</span></div>';}).join(""):'<div class="empty"><p>Your bag is empty.</p><a class="btn btn--d" href="#picks" data-close-link>Shop best sellers</a></div>';
if(typeof cartPage==="function")cartPage();
}
function addItem(id,silent){var l=cart.filter(function(x){return x.id===id;})[0];if(l)l.qty++;else cart.push({id:id,qty:1});save();render();if(!silent){toast(BY[id].n+" added to your bag");open("drawer");}}
function addKit(ids,name,price){var id="kit:"+name+":"+ids.join("+");var l=cart.filter(function(x){return x.id===id;})[0];if(l)l.qty++;else cart.push({id:id,kit:ids,name:name,price:+price,qty:1});cart.forEach(function(x){if(x.kit){x.p=x.price;}});save();render();toast(name+" added to your bag");open("drawer");}
document.addEventListener("click",function(e){
  var a=e.target.closest("[data-add]");if(a){addItem(a.dataset.add);return;}
  var k=e.target.closest("[data-kit]");if(k){addKit(k.dataset.kit.split(","),k.dataset.kitName,k.dataset.kitPrice);return;}
  var qb=e.target.closest("[data-q-i]");if(qb){var l=cart[+qb.dataset.qI];l.qty+= +qb.dataset.d;if(l.qty<=0)cart.splice(+qb.dataset.qI,1);save();render();return;}
  if(e.target.closest("[data-close-link]")){closeAll();return;}
  var w=e.target.closest("[data-wish]");if(w){var id=w.dataset.wish,i=wish.indexOf(id);if(i<0)wish.push(id);else wish.splice(i,1);saveWish();toast(i<0?"Saved to your wishlist":"Removed from your wishlist");if(typeof wishPage==="function")wishPage();}
  var ta=e.target.closest(".card__title a");if(ta){var art=ta.closest("[data-id]");if(art)pushRecent(art.dataset.id);}
});
/* kits store their own price */
lineInfo=function(l){return l.kit?{n:l.name,s:"Routine kit · "+l.kit.length+" products",p:l.price,sh:"jar",t:"#8c2350",bg:"#f6e3eb"}:BY[l.id];};
$("#checkout").addEventListener("click",function(){if(!cart.length){toast("Your bag is empty");return;}location.href="checkout.html";});
/* Wishlist (saved in this browser) */
var WKEY="nexus-beauty-wish",wish=(function(){try{return JSON.parse(localStorage.getItem(WKEY))||[];}catch(e){return [];}})().filter(function(id){return BY[id];});
function saveWish(){try{localStorage.setItem(WKEY,JSON.stringify(wish));}catch(e){}paintWish();}
function paintWish(){$$("[data-wish]").forEach(function(b){b.setAttribute("aria-pressed",wish.indexOf(b.dataset.wish)>-1);});var wc=$("#wishCount");wc.textContent=wish.length;wc.hidden=!wish.length;}
paintWish();
/* Recently viewed */
var RKEY="nexus-beauty-recent";
function getRecent(){try{return (JSON.parse(localStorage.getItem(RKEY))||[]).filter(function(id){return BY[id];});}catch(e){return [];}}
function pushRecent(id){if(!BY[id])return;var r=getRecent().filter(function(x){return x!==id;});r.unshift(id);try{localStorage.setItem(RKEY,JSON.stringify(r.slice(0,8)));}catch(e){}}
function miniCard(p){return '<article class="mini"><a href="product.html" data-add-search="'+p.id+'" class="mini__link">'+thumb(p)+'<span><small>'+esc(p.b)+'</small><b>'+esc(p.n)+'</b><span class="p">'+rs(p.p)+'</span></span></a><button class="btn btn--d" type="button" data-add="'+p.id+'">Add</button></article>';}
$$("[data-recent]").forEach(function(box){var r=getRecent();var wrap=box.closest("[data-recent-wrap]");if(!r.length){if(wrap)wrap.hidden=true;return;}box.innerHTML=r.map(function(id){return miniCard(BY[id]);}).join("");});
document.addEventListener("click",function(e){var a=e.target.closest(".mini__link");if(a)pushRecent(a.dataset.addSearch);});
/* Cookie notice */
var ck=$("#cookie");try{if(!localStorage.getItem("nexus-beauty-cookies"))ck.hidden=false;}catch(e){ck.hidden=false;}
$$("[data-cookie]").forEach(function(b){b.addEventListener("click",function(){try{localStorage.setItem("nexus-beauty-cookies",b.dataset.cookie);}catch(e){}ck.hidden=true;});});
/* Back to top */
var tt2=$("#toTop");window.addEventListener("scroll",function(){tt2.hidden=window.scrollY<900;},{passive:true});tt2.addEventListener("click",function(){window.scrollTo({top:0,behavior:"smooth"});});

/* Problem routines */
function rgUpdate(r){var boxes=$$("[data-rg-id]",r),on=boxes.filter(function(b){return b.checked;}),sub=0;on.forEach(function(b){sub+=+b.dataset.price;});
  var n=on.length,disc=n>=3?Math.round(sub*.15):0;
  $("[data-rg-count]",r).textContent=n;$("[data-rg-sub]",r).textContent=rs(sub);$("[data-rg-disc]",r).textContent="−"+rs(disc);$("[data-rg-disc-row]",r).hidden=!disc;$("[data-rg-total]",r).textContent=rs(sub-disc);
  var btn=$("[data-rg-add]",r);btn.disabled=!n;btn.textContent=n?"Add "+n+" product"+(n>1?"s":"")+" to bag":"Choose at least one product";
  $("[data-rg-note]",r).textContent=n>=3?"Untick anything you already own.":n?"Choose "+(3-n)+" more to get 15% off.":"";}
$$("[data-rg]").forEach(function(r){$$("[data-rg-id]",r).forEach(function(b){b.addEventListener("change",function(){rgUpdate(r);});});rgUpdate(r);
  $("[data-rg-add]",r).addEventListener("click",function(){var ids=$$("[data-rg-id]",r).filter(function(b){return b.checked;}).map(function(b){return b.dataset.rgId;});if(!ids.length)return;
    if(ids.length>=3){var sub=ids.reduce(function(t,id){return t+BY[id].p;},0);addKit(ids,r.dataset.rgName,Math.round(sub*.85));}else{ids.forEach(function(id){addItem(id,true);});toast(ids.length+" product"+(ids.length>1?"s":"")+" added to your bag");open("drawer");}});});
$$("[data-rg-tab]").forEach(function(t){t.addEventListener("click",function(){$$("[data-rg-tab]").forEach(function(x){x.setAttribute("aria-selected",x===t);});$$(".rg-panels .regimen").forEach(function(p){p.hidden=p.id!=="rg-"+t.dataset.rgTab;});});});
/* Videos: play inside the page only when a real video ID is set; otherwise open YouTube */
$$("[data-yt]").forEach(function(a){if(!a.dataset.yt)return;a.addEventListener("click",function(ev){ev.preventDefault();var f=document.createElement("iframe");f.src="https://www.youtube-nocookie.com/embed/"+encodeURIComponent(a.dataset.yt)+"?autoplay=1&rel=0";f.title=a.dataset.title;f.allow="autoplay; encrypted-media; picture-in-picture";f.allowFullscreen=true;f.className="video__frame";a.replaceWith(f);});});

/* Product tabs */
if($("#pickGrid")){
var grid=$("#pickGrid"),cards=$$(".card",grid),note=$("#tabNote");
function showTab(t){$$("[data-tab]").forEach(function(b){b.setAttribute("aria-selected",b.dataset.tab===t);});cards.forEach(function(c){c.hidden=(" "+c.dataset.tabs+" ").indexOf(" "+t+" ")<0;});var b=$('[data-tab="'+t+'"]');note.textContent=b.dataset.note;}
$$("[data-tab]").forEach(function(b){b.addEventListener("click",function(){showTab(b.dataset.tab);});});
showTab("best");
}

/* Concerns open search with that concern */
$$("[data-concern]").forEach(function(b){b.addEventListener("click",function(){var hits=DATA.filter(function(p){return (" "+p.k+" ").indexOf(" "+b.dataset.concern+" ")>-1;});window.scrollTo({top:0,behavior:"smooth"});toggleSearch(true);q.value=b.textContent.trim();res.innerHTML=hits.map(function(p){return '<a href="product.html" data-add-search="'+p.id+'">'+thumb(p)+'<span><b>'+esc(p.n)+'</b><small>'+esc(p.b)+' · '+esc(p.s)+'</small></span><span class="p">'+rs(p.p)+'</span></a>';}).join("")||"<p>We're adding products for this concern soon.</p>";});});

/* Brand tabs */
$$("[data-brands]").forEach(function(b){b.addEventListener("click",function(){$$("[data-brands]").forEach(function(x){x.setAttribute("aria-selected",x===b);});$("#brands-pk").hidden=b.dataset.brands!=="pk";$("#brands-intl").hidden=b.dataset.brands!=="intl";});});

/* Countdown to Sunday 23:59:59 Pakistan time (UTC+5) */
var tb=$$("#timer b");
function nextSunday(){var now=Date.now(),pk=new Date(now+5*3600e3);var day=pk.getUTCDay();var add=(7-day)%7;var end=Date.UTC(pk.getUTCFullYear(),pk.getUTCMonth(),pk.getUTCDate()+add,23,59,59)-5*3600e3;if(end<=now)end+=7*864e5;return end;}
var END=nextSunday();
function tick(){var d=Math.max(0,END-Date.now());if(!d)END=nextSunday();var v=[Math.floor(d/864e5),Math.floor(d/36e5)%24,Math.floor(d/6e4)%60,Math.floor(d/1e3)%60];tb.forEach(function(b,i){b.textContent=String(v[i]).padStart(2,"0");});}
tick();setInterval(tick,1000);

/* Routine finder */
var PLAN={
 spots:[["Cleanse","loreal-glycolic","garnier-men-acno"],["Treat","axis-y-5","garnier-vitc","axis-y-50"],["Protect","estelin-70","boj-relief-sun","neobrella-60"]],
 acne:[["Cleanse","garnier-men-acno","loreal-glycolic"],["Treat","anua-azelaic","axis-y-5"],["Moisturise","ponds-gel","dr-althea-345"],["Protect","neobrella-60","boj-relief-sun"]],
 dull:[["Cleanse","loreal-glycolic"],["Treat","garnier-vitc","medicube-pdrn","axis-y-5"],["Protect","boj-relief-sun","estelin-70"]],
 dry:[["Treat","medicube-pdrn","garnier-vitc"],["Moisturise","dr-althea-345","ponds-gel"],["Protect","boj-relief-sun","skin1004-sun"]],
 hairfall:[["Wash","rederm-tressfix","jenpharm-anagrow"],["Strengthen","jenpharm-anagrow","mielle-rosemary"],["Repair","fino-mask","mielle-rosemary"]],
 sun:[["Daily SPF","boj-relief-sun","estelin-70","neobrella-60"],["Reapply","tocobo-stick","skin1004-sun","neobrella-60"]]
};
var SKIN_AVOID={dry:["ponds-gel","garnier-men-acno"],sensitive:["loreal-glycolic","garnier-men-acno"],oily:["dr-althea-345"]};
function build(){
  var f=new FormData($("#finderForm")),c=f.get("concern"),s=f.get("skin"),bud=+f.get("budget");
  var avoid=SKIN_AVOID[s]||[],used={},steps=[];
  PLAN[c].forEach(function(st){var opts=st.slice(1).filter(function(id){return avoid.indexOf(id)<0&&!used[id];});if(!opts.length)opts=st.slice(1).filter(function(id){return !used[id];});if(!opts.length)return;steps.push({label:st[0],opts:opts,pick:0});used[opts[0]]=1;});
  function total(){return steps.reduce(function(t,x){return t+BY[x.opts[x.pick]].p;},0);}
  // swap to cheaper options until within budget
  var guard=0;while(total()>bud&&guard++<20){var best=null;steps.forEach(function(x,i){x.opts.forEach(function(id,j){if(j===x.pick)return;var diff=BY[x.opts[x.pick]].p-BY[id].p;if(diff>0&&(!best||diff>best.d))best={i:i,j:j,d:diff};});});if(!best)break;steps[best.i].pick=best.j;}
  if(total()>bud&&steps.length>2){steps.pop();}
  var ids=steps.map(function(x){return x.opts[x.pick];}),t=total();
  $("#routine").innerHTML='<h3>Your '+steps.length+'-step routine</h3><ol>'+steps.map(function(x,i){var p=BY[x.opts[x.pick]];return '<li><span class="n">'+String(i+1).padStart(2,"0")+'</span>'+thumb(p)+'<span><b>'+esc(p.n)+'</b><small>'+esc(x.label)+' · '+esc(p.b)+'</small></span><span class="p">'+rs(p.p)+'</span></li>';}).join("")+'</ol><div class="routine__foot"><span>Total <b>'+rs(t)+'</b>'+(t>bud?' · a little over budget':'')+'</span><button class="btn btn--p" type="button" data-routine="'+ids.join(",")+'">Add routine to bag</button></div>';
}
if($("#finderForm")){$("#finderForm").addEventListener("change",build);build();}
document.addEventListener("click",function(e){var r=e.target.closest("[data-routine]");if(!r)return;r.dataset.routine.split(",").forEach(function(id){addItem(id,true);});toast("Routine added to your bag");open("drawer");});

/* Newsletter */
$$("[data-signup]").forEach(function(f){f.addEventListener("submit",function(e){e.preventDefault();var em=$("input[type=email]",f),m=$("small",f);if(!em.checkValidity()||!em.value){m.textContent="Please enter a valid email address, like name@example.com.";em.focus();return;}m.textContent="You're on the list. Use code NEXUS200 for Rs 200 off your first order.";em.value="";});});

render();
})();
'''
SEARCHDATA=[dict(id=p['id'],n=p['name'],b=p['brand'],s=p['size'],p=p['price'],sh=p['shape'],t=p['tone'],bg=p['bg'],c=p['cat'],k=p['concerns']) for p in P]
JS=JS.replace('__DATA__',json.dumps(SEARCHDATA))
TITLE='Nexus Beauty'
DESC='Original skincare, sun care, hair care, fragrance and makeup delivered across Pakistan. Batch and expiry on every order. Cash on delivery.'
FONTS='<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Figtree:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap">'
HEAD=f'<title>{TITLE}</title>\n<meta name="description" content="{DESC}">\n<meta name="theme-color" content="#8c2350">\n{FONTS}\n<style>{CSS}</style>\n'+''.join(f'<script type="application/ld+json">{json.dumps(x)}</script>' for x in ld)
fragment=HEAD+'\n'+BODY+f'<script>{JS}</script>\n'
open('/tmp/claude-0/-home-user-cloude/2682baff-9cb6-53bf-af5e-3bcfa84e0a83/scratchpad/nexus-beauty.html','w').write(fragment)
full=('<!doctype html>\n<html lang="en">\n<head>\n<meta charset="utf-8">\n<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">\n'
 +HEAD+'\n</head>\n<body>\n'+BODY+f'<script>{JS}</script>\n</body>\n</html>\n')
open('/home/user/cloude/nexus-beauty/index.html','w').write(full)
print(len(full))
