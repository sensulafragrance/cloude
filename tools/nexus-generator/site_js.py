SITE_JS = r'''
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
'''
