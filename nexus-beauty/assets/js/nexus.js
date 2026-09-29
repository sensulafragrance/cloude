
(function(){
"use strict";
var DATA=[{"id": "axis-y-5", "n": "Dark Spot Correcting Glow Serum", "b": "AXIS-Y", "s": "5 ml mini", "p": 799, "sh": "dropper", "t": "#e2a33b", "bg": "#fbf1df", "c": "skincare", "k": "spots dull"}, {"id": "axis-y-50", "n": "Dark Spot Correcting Glow Serum", "b": "AXIS-Y", "s": "50 ml", "p": 4049, "sh": "dropper", "t": "#e2a33b", "bg": "#fbf1df", "c": "skincare", "k": "spots dull"}, {"id": "dr-althea-345", "n": "345 Relief Cream", "b": "Dr. Althea", "s": "50 ml", "p": 2749, "sh": "jar", "t": "#b3a0c9", "bg": "#f0ebf6", "c": "skincare", "k": "dry sensitive"}, {"id": "ponds-gel", "n": "Super Light Gel Moisturiser", "b": "Pond's", "s": "50 ml", "p": 749, "sh": "jar", "t": "#7fa7b8", "bg": "#e9f1f4", "c": "skincare", "k": "dry"}, {"id": "loreal-glycolic", "n": "Glycolic Bright Face Wash", "b": "L'Or\u00e9al Paris", "s": "Face wash", "p": 999, "sh": "tube", "t": "#b04a6b", "bg": "#f7e5eb", "c": "skincare", "k": "spots dull"}, {"id": "garnier-vitc", "n": "Bright Complete Vitamin C Serum", "b": "Garnier", "s": "Serum", "p": 550, "sh": "dropper", "t": "#e2a33b", "bg": "#fbf1df", "c": "skincare", "k": "dull spots"}, {"id": "medicube-pdrn", "n": "PDRN Pink Peptide Serum", "b": "Medicube", "s": "Serum", "p": 3829, "sh": "dropper", "t": "#e58aa8", "bg": "#fbe7ee", "c": "skincare", "k": "dull dry"}, {"id": "anua-azelaic", "n": "Azelaic Acid 10 Hyaluron Redness Soothing Serum", "b": "Anua", "s": "Serum", "p": 6659, "sh": "dropper", "t": "#9bb79e", "bg": "#eaf2eb", "c": "skincare", "k": "acne sensitive spots"}, {"id": "boj-relief-sun", "n": "Relief Sun SPF 50+", "b": "Beauty of Joseon", "s": "50 ml", "p": 1299, "sh": "tube", "t": "#f0c24b", "bg": "#fdf5dc", "c": "sun", "k": "sun spots"}, {"id": "estelin-70", "n": "Tinted Sunscreen SPF 70", "b": "Estelin", "s": "Sunscreen", "p": 1239, "sh": "tube", "t": "#c99a78", "bg": "#f6ece4", "c": "sun", "k": "sun spots"}, {"id": "neobrella-60", "n": "Neobrella Sunscreen SPF 60", "b": "Neophar", "s": "Sunscreen", "p": 930, "sh": "tube", "t": "#f0c24b", "bg": "#fdf5dc", "c": "sun", "k": "sun"}, {"id": "tocobo-stick", "n": "Cotton Soft Sun Stick SPF 50+", "b": "Tocobo", "s": "Stick", "p": 4509, "sh": "tube", "t": "#9bb79e", "bg": "#eaf2eb", "c": "sun", "k": "sun"}, {"id": "skin1004-sun", "n": "Hyalu-Cica Water-fit Sun Serum", "b": "SKIN1004", "s": "Sun serum", "p": 4289, "sh": "tube", "t": "#7fa7b8", "bg": "#e9f1f4", "c": "sun", "k": "sun sensitive"}, {"id": "rederm-tressfix", "n": "Tressfix Hairfall & Anti-Dandruff Shampoo", "b": "Rederm", "s": "Shampoo", "p": 1150, "sh": "bottle", "t": "#9bb79e", "bg": "#eaf2eb", "c": "hair", "k": "hairfall dandruff"}, {"id": "jenpharm-anagrow", "n": "Anagrow Biotin Shampoo", "b": "Jenpharm", "s": "Shampoo", "p": 988, "sh": "bottle", "t": "#7fa7b8", "bg": "#e9f1f4", "c": "hair", "k": "hairfall"}, {"id": "fino-mask", "n": "Premium Touch Hair Mask", "b": "Fino", "s": "Mask", "p": 3299, "sh": "jar", "t": "#c68a3a", "bg": "#f8eedf", "c": "hair", "k": "frizz"}, {"id": "mielle-rosemary", "n": "Rosemary Mint Strengthening Hair Masque", "b": "Mielle", "s": "Masque", "p": 4079, "sh": "jar", "t": "#5f8f6a", "bg": "#e6f0e8", "c": "hair", "k": "hairfall frizz"}, {"id": "vaseline-gluta", "n": "Gluta-Hya Serum Burst Lotion", "b": "Vaseline", "s": "Lotion", "p": 2399, "sh": "pump", "t": "#b04a6b", "bg": "#f7e5eb", "c": "body", "k": "body dull"}, {"id": "vaseline-gel-oil", "n": "Intensive Care Body Gel Oil", "b": "Vaseline", "s": "Gel oil", "p": 3439, "sh": "pump", "t": "#d9b48f", "bg": "#f7efe6", "c": "body", "k": "body dry"}, {"id": "kn-vp", "n": "Vanilla Pistachio Hair & Body Mist", "b": "Koh-e-Noor by Nexus Beauty", "s": "100 ml", "p": 1290, "sh": "perfume", "t": "#c9a55a", "bg": "#f6efdc", "c": "fragrance", "k": "scent"}, {"id": "kn-oud", "n": "Oud Amber Hair & Body Mist", "b": "Koh-e-Noor by Nexus Beauty", "s": "100 ml", "p": 1290, "sh": "perfume", "t": "#8a4b2f", "bg": "#f3e6dc", "c": "fragrance", "k": "scent"}, {"id": "kn-musk", "n": "Fresh Musk Hair & Body Mist", "b": "Koh-e-Noor by Nexus Beauty", "s": "100 ml", "p": 1290, "sh": "perfume", "t": "#86aec2", "bg": "#e8f1f5", "c": "fragrance", "k": "scent"}, {"id": "kn-vp-30", "n": "Vanilla Pistachio Mist, travel size", "b": "Koh-e-Noor by Nexus Beauty", "s": "30 ml", "p": 490, "sh": "perfume", "t": "#c9a55a", "bg": "#f6efdc", "c": "fragrance", "k": "scent"}, {"id": "hype-fruit", "n": "Fruit Frenzy Body Spray", "b": "Hype by Capri", "s": "150 ml", "p": 550, "sh": "bottle", "t": "#e58aa8", "bg": "#fbe7ee", "c": "fragrance", "k": "scent"}, {"id": "medora-matte", "n": "Matte Lipstick", "b": "Medora", "s": "Lipstick", "p": 300, "sh": "lipstick", "t": "#9e2f4a", "bg": "#f6e2e7", "c": "makeup", "k": "lips"}, {"id": "swiss-miss-60", "n": "Natural Matte Lipstick 60", "b": "Swiss Miss", "s": "Lipstick", "p": 400, "sh": "lipstick", "t": "#c99a78", "bg": "#f6ece4", "c": "makeup", "k": "lips"}, {"id": "garnier-men-acno", "n": "Acno Fight Face Wash", "b": "Garnier Men", "s": "Face wash", "p": 1050, "sh": "tube", "t": "#3c3f44", "bg": "#e8e9eb", "c": "men", "k": "acne men"}, {"id": "rivaj-beard", "n": "Beard Oil", "b": "Rivaj", "s": "Beard oil", "p": 695, "sh": "dropper", "t": "#c68a3a", "bg": "#f8eedf", "c": "men", "k": "men"}, {"id": "daisy-plus", "n": "Daisy Plus Razors", "b": "Gillette", "s": "Pack", "p": 599, "sh": "tube", "t": "#e58aa8", "bg": "#fbe7ee", "c": "personal", "k": ""}, {"id": "st-ives-apricot", "n": "Fresh Skin Apricot Face Scrub", "b": "St. Ives", "s": "Face scrub", "p": 1299, "sh": "tube", "t": "#d9b48f", "bg": "#f7efe6", "c": "skincare", "k": "dull spots"}, {"id": "dove-body-polish", "n": "Exfoliating Body Polish", "b": "Dove", "s": "Body polish", "p": 2499, "sh": "jar", "t": "#b04a6b", "bg": "#f7e5eb", "c": "body", "k": "body dull"}, {"id": "cetaphil-gentle", "n": "Gentle Skin Cleanser", "b": "Cetaphil", "s": "Cleanser", "p": 2199, "sh": "pump", "t": "#7fa7b8", "bg": "#e9f1f4", "c": "skincare", "k": "dry sensitive"}, {"id": "rederm-clariderm", "n": "Clariderm Face Wash", "b": "Rederm", "s": "Face wash", "p": 1050, "sh": "tube", "t": "#9bb79e", "bg": "#eaf2eb", "c": "skincare", "k": "acne"}], BY={}; DATA.forEach(function(p){BY[p.id]=p;});
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

if($("#shopGrid")){
/* ===== Shop page ===== */
var sgrid=$("#shopGrid"),scards=$$(".card",sgrid),promoT=$(".promo-tile",sgrid),fl=$("#filters"),sortSel=$("#sort"),activeEl=$("#active"),emptyEl=$("#empty");
var NAMES={"skincare": "Skincare", "sun": "Sun care", "hair": "Hair care", "body": "Body care", "fragrance": "Fragrance", "makeup": "Makeup", "men": "Men's grooming", "personal": "Personal care", "derm": "Pakistani derm brands", "minis": "Minis & under Rs 1,000", "new": "New arrivals", "own": "Koh-e-Noor by Nexus Beauty", "all": "Shop all"};
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
  if(!sgrid.dataset.preset){var title=NAMES[q];$("#shopTitle").textContent=title&&q!=="all"?title:"Shop original beauty products";$("#crumbCur").textContent=title&&q!=="all"?title:"Shop all";}
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
  if(ev.target.closest("[data-clear]")){setOnly(sgrid.dataset.preset||"all");return;}
});
function closeFilters(){fl.classList.remove("is-open");scrim.classList.remove("is-open");}
$("#openFilters").addEventListener("click",function(){fl.classList.add("is-open");scrim.classList.add("is-open");});
$$("[data-close-filters]").forEach(function(b){b.addEventListener("click",closeFilters);});
scrim.addEventListener("click",closeFilters);
document.addEventListener("keydown",function(ev){if(ev.key==="Escape")closeFilters();});
var PRESET=sgrid.dataset.preset||"";var tok=PRESET||(location.hash||"").replace("#","");setOnly(NAMES[tok]?tok:"all");
window.addEventListener("hashchange",function(){var t=location.hash.replace("#","");if(NAMES[t])setOnly(t);});

}
if($("#stage")){
/* ===== Product page ===== */
pushRecent("axis-y-5");
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

}

/* ================= Promo codes + totals ================= */
var PKEY="nexus-beauty-promo",OKEY="nexus-beauty-orders",UKEY="nexus-beauty-user",SHIPFEE=250;
var PROMOS={NEXUS200:{off:200,min:1500,label:"Rs 200 off (orders over Rs 1,500)"}};
function getPromo(){try{return localStorage.getItem(PKEY)||"";}catch(e){return "";}}
function setPromo(c){try{if(c)localStorage.setItem(PKEY,c);else localStorage.removeItem(PKEY);}catch(e){}}
function totals(){var sub=0,n=0;cart.forEach(function(l){var p=lineInfo(l);sub+=p.p*l.qty;n+=l.qty;});
  var code=getPromo(),pr=PROMOS[code],disc=(pr&&sub>=pr.min)?pr.off:0,ship=!sub?0:(sub>=FREE?0:SHIPFEE);
  return {sub:sub,n:n,code:code,disc:disc,ship:ship,total:Math.max(0,sub-disc)+ship};}
function sumRows(t){return '<div class="srow"><span>Subtotal ('+t.n+' item'+(t.n===1?'':'s')+')</span><span>'+rs(t.sub)+'</span></div>'+
  (t.disc?'<div class="srow disc"><span>Code '+esc(t.code)+'</span><span>−'+rs(t.disc)+'</span></div>':'')+
  '<div class="srow"><span>Delivery</span><span>'+(t.ship?rs(t.ship):'Free')+'</span></div>'+
  '<div class="srow tot"><span>Total</span><b>'+rs(t.total)+'</b></div>';}
function orders(){try{return JSON.parse(localStorage.getItem(OKEY))||[];}catch(e){return [];}}
function saveOrders(o){try{localStorage.setItem(OKEY,JSON.stringify(o.slice(0,20)));}catch(e){}}
function getUser(){try{return JSON.parse(localStorage.getItem(UKEY));}catch(e){return null;}}
function setUser(u){try{if(u)localStorage.setItem(UKEY,JSON.stringify(u));else localStorage.removeItem(UKEY);}catch(e){}}
$$("[data-promo]").forEach(function(f){f.addEventListener("submit",function(ev){ev.preventDefault();var inp=$("input",f),m=$("[data-promo-msg]",f.parentNode),c=inp.value.trim().toUpperCase();
  if(!c){m.textContent="Enter a code first.";m.className="formmsg err";return;}
  if(!PROMOS[c]){m.textContent="That code isn't valid. Check the spelling, or try NEXUS200 on your first order.";m.className="formmsg err";return;}
  var t=totals();setPromo(c);m.className="formmsg ok";m.textContent=t.sub>=PROMOS[c].min?"Code applied: "+PROMOS[c].label+".":"Code saved. Add "+rs(PROMOS[c].min-t.sub)+" more to use it.";render();if(typeof coRender==="function")coRender();});});
/* Delivery estimate helper (Pakistan time, Sundays off, 3pm cut-off) */
var DAYS2=["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],MON2=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
function etaText(range){var r=String(range||"3-5").split("-"),now=new Date(Date.now()+5*36e5);function nw(d,n){var x=new Date(d.getTime());while(n>0){x.setUTCDate(x.getUTCDate()+1);if(x.getUTCDay()!==0)n--;}return x;}
  function f(d){return DAYS2[d.getUTCDay()]+" "+d.getUTCDate()+" "+MON2[d.getUTCMonth()];}
  var disp=(now.getUTCDay()!==0&&now.getUTCHours()<15)?now:nw(now,1);return f(nw(disp,+r[0]))+" – "+f(nw(disp,+r[1]));}

/* ================= Cart page ================= */
function cartPage(){var el=$("#cartPage");if(!el)return;var sumBox=$("#cartSummary");
  if(!cart.length){el.innerHTML='<div class="empty-state"><b>Your bag is empty.</b><p>Start with our best sellers, or build a routine for your skin.</p><div style="display:flex;gap:10px;flex-wrap:wrap;justify-content:center"><a class="btn btn--p" href="shop.html">Shop best sellers</a><a class="btn btn--g" href="solutions.html">See routines</a></div></div>';sumBox.hidden=true;return;}
  sumBox.hidden=false;
  el.innerHTML=cart.map(function(l,i){var p=lineInfo(l);return '<div class="cline">'+thumb(p)+'<div><b>'+esc(p.n)+'</b><small>'+esc(p.s)+' · '+rs(p.p)+' each</small><div class="qty"><button type="button" data-q-i="'+i+'" data-d="-1" aria-label="One less">−</button><span>'+l.qty+'</span><button type="button" data-q-i="'+i+'" data-d="1" aria-label="One more">+</button><button class="rm" type="button" data-rm="'+i+'">Remove</button></div></div><span class="p">'+rs(p.p*l.qty)+'</span></div>';}).join("");
  var t=totals();$("#cartRows").innerHTML=sumRows(t);
  var left=Math.max(0,FREE-t.sub);$("#cartShip").innerHTML=left?"Add <b>"+rs(left)+"</b> more for free delivery.":"<b>You've unlocked free delivery.</b>";
  if(t.code&&!t.disc&&PROMOS[t.code]){$("#cartShip").innerHTML+=" Code "+esc(t.code)+" needs "+rs(PROMOS[t.code].min-t.sub)+" more.";}
}
document.addEventListener("click",function(e){var r=e.target.closest("[data-rm]");if(!r)return;cart.splice(+r.dataset.rm,1);save();render();toast("Removed from your bag");});

/* ================= Checkout ================= */
function coRender(){var box=$("#coItems");if(!box)return;
  if(!cart.length){$("#checkoutWrap").innerHTML='<div class="empty-state"><b>Your bag is empty.</b><p>Add something before you check out.</p><a class="btn btn--p" href="shop.html">Shop best sellers</a></div>';return;}
  box.innerHTML=cart.map(function(l){var p=lineInfo(l);return '<div class="co-item"><span class="thumb" style="--tone:'+p.t+';--bg:'+p.bg+'"><svg viewBox="0 0 100 160" aria-hidden="true"><use href="#i-'+p.sh+'"/></svg><i>'+l.qty+'</i></span><span><b>'+esc(p.n)+'</b><br><small>'+esc(p.s)+'</small></span><span class="p">'+rs(p.p*l.qty)+'</span></div>';}).join("");
  $("#coRows").innerHTML=sumRows(totals());var btn=$("#placeOrder");if(btn)btn.textContent="Place order · "+rs(totals().total);}
if($("#checkoutForm")){
  coRender();
  var cf=$("#checkoutForm");
  if(cf){
  var u=getUser();if(u){if(u.name)cf.elements.name.value=u.name;if(u.phone)cf.elements.phone.value=u.phone;}
  var last=orders()[0];if(last&&!cf.elements.address.value){cf.elements.address.value=last.address||"";if(last.cityIndex!=null)cf.elements.city.selectedIndex=last.cityIndex;}
  function cityEta(){var o=cf.elements.city.selectedOptions[0];$("#coEta").textContent=o&&o.value?"Estimated delivery: "+etaText(o.dataset.days)+".":"";}
  cf.elements.city.addEventListener("change",cityEta);cityEta();
  $$('input[name="pay"]',cf).forEach(function(r){r.addEventListener("change",function(){$("#payNote").textContent=r.dataset.note;});});
  function setErr(name,msg){var f=cf.elements[name],err=$('[data-err="'+name+'"]',cf);f.setAttribute("aria-invalid",msg?"true":"false");err.textContent=msg||"";return !msg;}
  cf.addEventListener("submit",function(ev){ev.preventDefault();if(!cart.length)return;
    var v=function(n){return (cf.elements[n].value||"").trim();},ok=true,first=null;
    function chk(name,msg){var good=setErr(name,msg);if(!good&&!first)first=name;ok=ok&&good;}
    chk("name",v("name").length<2?"Enter your full name.":"");
    var ph=v("phone").replace(/[\s-]/g,"");chk("phone",/^(\+92|0092|0)3\d{9}$/.test(ph)?"":"Enter a Pakistani mobile number, like 0300 1234567.");
    var em=v("email");chk("email",em&&!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(em)?"Check your email address, or leave it empty.":"");
    chk("city",v("city")?"":"Choose your city.");
    chk("address",v("address").length<10?"Enter your full address: house, street and area.":"");
    if(!ok){cf.elements[first].focus();$("#coMsg").textContent="Please fix the highlighted fields.";$("#coMsg").className="formmsg err";return;}
    var t=totals(),no="NX-"+String(Math.floor(100000+Math.random()*900000)),pay=$('input[name="pay"]:checked',cf);
    var ord={no:no,date:Date.now(),name:v("name"),phone:ph,email:em,city:cf.elements.city.selectedOptions[0].textContent,cityIndex:cf.elements.city.selectedIndex,address:v("address"),notes:v("notes"),pay:pay.value,
      eta:etaText(cf.elements.city.selectedOptions[0].dataset.days),items:cart.map(function(l){var p=lineInfo(l);return {n:p.n,s:p.s,q:l.qty,p:p.p};}),totals:t};
    var o=orders();o.unshift(ord);saveOrders(o);
    if(!getUser())setUser({name:ord.name,phone:ord.phone,guest:true});
    cart.length=0;save();setPromo("");location.href="order-confirmation.html";
  });
}}

/* ================= Order confirmation ================= */
if($("#confirm")){var oc=orders()[0],cbox=$("#confirm");
  if(!oc){cbox.innerHTML='<div class="empty-state"><b>No recent order found in this browser.</b><p>If you placed an order, check your SMS or WhatsApp for the order number.</p><a class="btn btn--p" href="track-order.html">Track an order</a></div>';}
  else{$("#cNo").textContent=oc.no;$("#cName").textContent=oc.name.split(" ")[0];$("#cEta").textContent=oc.eta;$("#cAddr").textContent=oc.address+", "+oc.city;$("#cPhone").textContent=oc.phone;
    $("#cPay").textContent={cod:"Cash on delivery",jazzcash:"JazzCash",easypaisa:"Easypaisa",card:"Debit or credit card"}[oc.pay]||oc.pay;
    $("#cItems").innerHTML=oc.items.map(function(i){return '<div class="srow"><span>'+i.q+' × '+esc(i.n)+'</span><span>'+rs(i.p*i.q)+'</span></div>';}).join("")+sumRows(oc.totals);}}

/* ================= Track order ================= */
if($("#trackForm")){var tf=$("#trackForm");
  function stage(o){var h=(Date.now()-o.date)/36e5;return h<2?0:h<24?1:h<72?2:3;}
  function showTrack(o){var s=stage(o),steps=[["Order confirmed","We've received your order and sent you a confirmation."],["Checked and packed","Batch and expiry checked, sticker printed, parcel sealed."],["With the courier","On its way to "+esc(o.city)+". Expected "+esc(o.eta)+"."],["Delivered","Pay the rider if you chose cash on delivery."]];
    $("#trackOut").innerHTML='<div class="box"><h3>Order '+esc(o.no)+'</h3><p>'+o.items.length+' item'+(o.items.length>1?'s':'')+' · '+rs(o.totals.total)+' · '+esc(o.city)+'</p><ol class="track">'+steps.map(function(st,i){return '<li class="'+(i<s?"done":i===s?"now":"")+'"><span class="dot">'+(i<s?"✓":i+1)+'</span><span><b>'+st[0]+'</b><span>'+st[1]+'</span></span></li>';}).join("")+'</ol></div>';}
  var o0=orders()[0];if(o0){tf.elements.order.value=o0.no;tf.elements.phone.value=o0.phone;}
  tf.addEventListener("submit",function(ev){ev.preventDefault();var no=tf.elements.order.value.trim().toUpperCase(),ph=tf.elements.phone.value.replace(/[\s-]/g,""),m=$("#trackMsg");
    if(!/^NX-\d{4,}$/.test(no)){m.className="formmsg err";m.textContent="Enter your order number, like NX-104829.";return;}
    var o=orders().filter(function(x){return x.no===no&&(!ph||x.phone.slice(-7)===ph.slice(-7));})[0];
    if(!o){m.className="formmsg err";m.textContent="We can't find that order. Check the number in your confirmation message, or WhatsApp us and we'll look it up.";$("#trackOut").innerHTML="";return;}
    m.className="formmsg ok";m.textContent="Order found.";showTrack(o);});}

/* ================= Account ================= */
if($("#account")){var ab=$("#account");
  function dash(){var u=getUser(),os=orders();
    ab.innerHTML='<div class="box"><p class="eyebrow">My account</p><h2 class="h2" style="font-size:28px">Hello, '+esc((u.name||"there").split(" ")[0])+'</h2><p>Signed in with '+esc(u.phone||"")+'</p><div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:8px"><a class="btn btn--g" href="wishlist.html">Wishlist ('+wish.length+')</a><a class="btn btn--g" href="track-order.html">Track an order</a><button class="btn btn--d" type="button" id="logout">Sign out</button></div></div>'+
    '<div class="box" style="margin-top:16px"><h3>Your orders</h3>'+(os.length?os.map(function(o){return '<div class="srow" style="padding-block:8px;border-bottom:1px solid var(--line)"><span><b>'+esc(o.no)+'</b> · '+new Date(o.date).toLocaleDateString("en-GB",{day:"numeric",month:"short",year:"numeric"})+' · '+o.items.length+' item'+(o.items.length>1?'s':'')+'</span><span>'+rs(o.totals.total)+'</span></div>';}).join(""):'<p>No orders yet. <a href="shop.html" style="text-decoration:underline">Start shopping</a>.</p>')+'</div>'+
    (os[0]?'<div class="box" style="margin-top:16px"><h3>Saved address</h3><p>'+esc(os[0].name)+'<br>'+esc(os[0].address)+', '+esc(os[0].city)+'<br>'+esc(os[0].phone)+'</p></div>':'');
    $("#logout").addEventListener("click",function(){setUser(null);toast("You've signed out");login();});}
  function login(){ab.innerHTML=$("#loginTpl").innerHTML;var lf=$("#loginForm"),step=1;
    lf.addEventListener("submit",function(ev){ev.preventDefault();var m=$("#loginMsg"),ph=lf.elements.phone.value.replace(/[\s-]/g,"");
      if(step===1){if(!/^(\+92|0092|0)3\d{9}$/.test(ph)){m.className="formmsg err";m.textContent="Enter a Pakistani mobile number, like 0300 1234567.";return;}
        step=2;$("#otpRow").hidden=false;$("#nameRow").hidden=false;lf.querySelector("button[type=submit]").textContent="Verify and sign in";m.className="formmsg ok";m.textContent="We've sent a 4-digit code to "+ph+" by SMS.";lf.elements.otp.focus();return;}
      if(!/^\d{4}$/.test(lf.elements.otp.value.trim())){m.className="formmsg err";m.textContent="Enter the 4-digit code from the SMS.";return;}
      var prev=getUser()||{};setUser({name:lf.elements.name.value.trim()||prev.name||"there",phone:ph});toast("You're signed in");dash();});}
  var u0=getUser();if(u0&&!u0.guest)dash();else login();}

/* ================= Wishlist page ================= */
function wishPage(){var box=$("#wishPage");if(!box)return;
  if(!wish.length){box.innerHTML='<div class="empty-state" style="grid-column:1/-1"><b>Your wishlist is empty.</b><p>Tap the heart on any product to save it here.</p><a class="btn btn--p" href="shop.html">Browse products</a></div>';$("#wishAll").hidden=true;return;}
  $("#wishAll").hidden=false;box.innerHTML=wish.map(function(id){return miniCard(BY[id]).replace('</article>','<button class="wish" style="position:static" type="button" data-wish="'+id+'" aria-pressed="true" aria-label="Remove from wishlist"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20s-7.5-4.6-9-9.3C2 7.4 4.2 4.5 7.4 4.5c2 0 3.4 1.1 4.6 2.6 1.2-1.5 2.6-2.6 4.6-2.6 3.2 0 5.4 2.9 4.4 6.2-1.5 4.7-9 9.3-9 9.3z"/></svg></button></article>');}).join("");}
if($("#wishPage")){wishPage();$("#wishAll").addEventListener("click",function(){wish.forEach(function(id){addItem(id,true);});toast(wish.length+" products added to your bag");open("drawer");});}

/* ================= Search page ================= */
if($("#searchPage")){var sq=$("#sq"),sout=$("#searchPage");
  function run(v){v=(v||"").trim().toLowerCase();var words=v.split(/\s+/).filter(Boolean);
    if(!words.length){sout.innerHTML="";$("#sCount").textContent="Type a product, brand or concern.";return;}
    var extra=(ALIAS[v]||"").split(" ").filter(Boolean);
    var hits=DATA.filter(function(p){var hay=(p.n+" "+p.b+" "+p.c+" "+p.k+" "+p.s).toLowerCase();return words.every(function(w){return hay.indexOf(w)>-1;})||extra.some(function(w){return hay.indexOf(w)>-1;});});
    $("#sCount").textContent=hits.length+" result"+(hits.length===1?"":"s")+' for "'+v+'"';
    sout.innerHTML=hits.length?hits.map(miniCard).join(""):'<div class="empty-state" style="grid-column:1/-1"><b>No products match "'+esc(v)+'".</b><p>Try a shorter word like "sunscreen" or "serum", or ask us on WhatsApp.</p></div>';}
  var h=decodeURIComponent((location.hash||"").slice(1)).replace(/-/g," ");sq.value=h;run(h);
  sq.addEventListener("input",function(){run(sq.value);});
  $("#searchPageForm").addEventListener("submit",function(ev){ev.preventDefault();run(sq.value);});
  $$("[data-sq]").forEach(function(b){b.addEventListener("click",function(){sq.value=b.dataset.sq;run(sq.value);});});}

/* ================= Blog archive ================= */
if($("#blogGrid")){var bposts=$$(".post",$("#blogGrid")),bq=$("#blogQ");
  function bf(){var cat=($('[data-bcat][aria-pressed="true"]')||{dataset:{bcat:"all"}}).dataset.bcat,v=(bq.value||"").toLowerCase(),n=0;
    bposts.forEach(function(p){var ok=(cat==="all"||p.dataset.cat===cat)&&p.textContent.toLowerCase().indexOf(v)>-1;p.hidden=!ok;if(ok)n++;});$("#blogEmpty").hidden=n>0;}
  $$("[data-bcat]").forEach(function(b){b.addEventListener("click",function(){$$("[data-bcat]").forEach(function(x){x.setAttribute("aria-pressed",x===b);});bf();});});
  bq.addEventListener("input",bf);}

/* ================= Brands A–Z filter ================= */
if($("#azSearch")){var az=$("#azSearch");az.addEventListener("input",function(){var v=az.value.toLowerCase();$$(".az-group").forEach(function(g){var n=0;$$(".brand",g).forEach(function(b){var ok=b.dataset.name.indexOf(v)>-1;b.hidden=!ok;if(ok)n++;});g.hidden=!n;});});}

/* ================= FAQ page search ================= */
if($("#faqQ")){var fq=$("#faqQ");fq.addEventListener("input",function(){var v=fq.value.toLowerCase(),n=0;$$(".faqgroup details").forEach(function(d){var ok=d.textContent.toLowerCase().indexOf(v)>-1;d.hidden=!ok;if(ok){n++;if(v.length>2)d.open=true;}});$$(".faqgroup").forEach(function(g){g.hidden=!$$("details:not([hidden])",g).length;});$("#faqEmpty").hidden=n>0;});}

/* ================= Contact form ================= */
if($("#contactForm")){var ctf=$("#contactForm");ctf.addEventListener("submit",function(ev){ev.preventDefault();var m=$("#contactMsg"),n=ctf.elements.cname.value.trim(),c=ctf.elements.cphone.value.replace(/[\s-]/g,""),t=ctf.elements.cmsg.value.trim();m.className="formmsg err";
  if(n.length<2){m.textContent="Enter your name.";return;}
  if(!/^(\+92|0092|0)3\d{9}$/.test(c)&&!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(c)){m.textContent="Enter a Pakistani mobile number or an email address so we can reply.";return;}
  if(t.length<10){m.textContent="Tell us a little more (at least 10 characters).";return;}
  m.className="formmsg ok";m.textContent="Thanks, "+n.split(" ")[0]+". We reply within 2 hours, 10am to 10pm.";ctf.reset();});}

/* ================= Copy link (blog share) ================= */
$$("[data-copy-link]").forEach(function(b){b.addEventListener("click",function(){var u=location.href;function ok(){b.textContent="Link copied";setTimeout(function(){b.textContent="Copy link";},1800);}try{navigator.clipboard.writeText(u).then(ok,function(){toast("Copy the link from your address bar");});}catch(e){toast("Copy the link from your address bar");}});});

render();
})();
