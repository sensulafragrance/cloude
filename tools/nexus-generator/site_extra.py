EXTRA_CSS = r"""
@media (max-width:560px){.hide-sm{display:none!important}}
/* Floating WhatsApp, back to top, cookie notice */
.wa-float{position:fixed;right:18px;bottom:calc(18px + env(safe-area-inset-bottom,0px));z-index:44;width:54px;height:54px;border-radius:50%;background:#1f8f4e;color:#fff;display:grid;place-items:center;box-shadow:var(--shadow)}
.wa-float svg{width:28px;height:28px;fill:none;stroke:#fff;stroke-width:1.6;stroke-linejoin:round}
.wa-float:hover{background:#17763f}
.to-top{position:fixed;right:22px;bottom:calc(84px + env(safe-area-inset-bottom,0px));z-index:44;width:44px;height:44px;border-radius:50%;border:1px solid var(--line);background:#fff;display:grid;place-items:center;box-shadow:var(--shadow)}
.to-top svg{width:20px;height:20px;fill:none;stroke:var(--ink);stroke-width:2;stroke-linecap:round}
body:has(.sticky-atc.is-on) .wa-float,body:has(.sticky-atc.is-on) .to-top{transform:translateY(-70px)}
.cookie{position:fixed;left:16px;right:16px;bottom:calc(16px + env(safe-area-inset-bottom,0px));z-index:60;max-width:560px;background:var(--ink);color:#f3e9ee;border-radius:var(--r);padding:16px 18px;display:grid;gap:12px;box-shadow:var(--shadow);font-size:14px}
.cookie a{text-decoration:underline;color:#fff}
.cookie div{display:flex;gap:8px;flex-wrap:wrap}
.cookie .btn{padding:10px 16px;font-size:14px}
.cookie .btn--g{border-color:#fff;color:#fff}
/* Mini product rows (recently viewed, wishlist, search) */
.minis{display:grid;grid-template-columns:repeat(auto-fill,minmax(min(100%,260px),1fr));gap:12px}
.mini{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:10px;align-items:center;background:#fff;border:1px solid var(--blush-2);border-radius:var(--r);padding:10px 12px}
.mini__link{display:grid;grid-template-columns:52px minmax(0,1fr);gap:12px;align-items:center}
.mini .thumb{width:52px;height:52px;border-radius:8px;background:var(--bg);display:grid;place-items:center}
.mini .thumb svg{width:48%;color:var(--tone)}
.mini small{display:block;font-size:11.5px;letter-spacing:.06em;text-transform:uppercase;color:var(--ink-3);font-weight:700}
.mini b{display:block;font-size:14px;line-height:1.3}
.mini .p{font-size:14px;font-weight:700;font-variant-numeric:tabular-nums}
.mini .btn{padding:9px 14px;font-size:13px}
/* Generic page head + prose */
.phead{background:var(--blush);padding-block:8px clamp(26px,4vw,44px)}
.phead h1{font-size:clamp(30px,4.2vw,48px);margin-block:4px 10px}
.prose{max-width:760px;display:grid;gap:14px;font-size:16.5px;color:var(--ink-2)}
.prose h2{font-size:clamp(22px,2.6vw,28px);color:var(--ink);margin-top:18px}
.prose h3{font-size:19px;color:var(--ink);margin-top:8px}
.prose ul,.prose ol{margin:0;padding-left:22px;display:grid;gap:6px}
.prose a{color:var(--berry);text-decoration:underline}
.prose b,.prose strong{color:var(--ink)}
.prose table{width:100%;border-collapse:collapse;font-size:15px}
.prose ul,.box ul{list-style:disc}
.prose ol{list-style:decimal}
.vs__p .card__media{aspect-ratio:16/10}
.note{background:var(--gold-lt);border-radius:10px;padding:12px 14px;font-size:14px;color:var(--ink)}
.layout2{display:grid;grid-template-columns:220px minmax(0,1fr);gap:48px;align-items:start}
.toc{position:sticky;top:calc(env(safe-area-inset-top,0px) + 96px);display:grid;gap:4px;font-size:14px}
.toc p{font-weight:700;margin-bottom:6px;color:var(--ink)}
.toc a{color:var(--ink-2);padding:4px 0;border-left:2px solid var(--line);padding-left:12px}
.toc a:hover{color:var(--berry);border-color:var(--berry)}
@media (max-width:900px){.layout2{grid-template-columns:minmax(0,1fr);gap:20px}.toc{position:static;background:var(--blush);border-radius:var(--r);padding:14px}}
.tbl{overflow-x:auto;border:1px solid var(--line);border-radius:var(--r);background:#fff}
.tbl table{width:100%;border-collapse:collapse;font-size:15px;min-width:520px}
.tbl th{text-align:left;font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:var(--ink-2);background:var(--blush);padding:11px 14px;border-bottom:1px solid var(--line)}
.tbl td{padding:12px 14px;border-bottom:1px solid var(--line);vertical-align:top;color:var(--ink)}
.tbl tr:last-child td{border-bottom:0}
.tbl td:first-child{font-weight:700}
.cards3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
@media (max-width:900px){.cards3{grid-template-columns:minmax(0,1fr)}}
.cards2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
@media (max-width:640px){.cards2{grid-template-columns:minmax(0,1fr)}}
.wrap>*,.cartwrap>*,.layout2>*{min-width:0}
.box{background:#fff;border:1px solid var(--blush-2);border-radius:var(--r);padding:20px;display:grid;gap:8px;align-content:start}
.box h3{font-size:18px}
.box p,.box li{font-size:15px;color:var(--ink-2)}
.box ul{margin:0;padding-left:18px;display:grid;gap:5px}
/* Forms */
.form{display:grid;gap:14px}
.form .row2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
@media (max-width:560px){.form .row2{grid-template-columns:minmax(0,1fr)}}
.field{display:grid;gap:6px;font-size:14px;font-weight:700}
.field input,.field select,.field textarea{border:1.5px solid var(--line);border-radius:10px;padding:12px 13px;font-weight:400;font-size:15px;background:#fff;width:100%}
.field input:focus,.field select:focus,.field textarea:focus{border-color:var(--berry);outline:none}
.field [aria-invalid="true"]{border-color:#b3261e}
.field small{font-weight:400;color:var(--ink-3);font-size:12.5px}
.field .err{color:#b3261e;font-weight:700;font-size:13px}
.field .err:empty{display:none}
.pay-opts{display:grid;gap:10px}
.pay-opt{display:grid;grid-template-columns:20px minmax(0,1fr);gap:12px;align-items:start;border:1.5px solid var(--line);border-radius:10px;padding:14px;cursor:pointer;font-weight:400}
.pay-opt:has(input:checked){border-color:var(--ink);box-shadow:inset 0 0 0 1px var(--ink)}
.pay-opt input{width:18px;height:18px;accent-color:var(--berry);margin:2px 0 0}
.pay-opt b{display:block;font-size:15px}
.pay-opt span{font-size:13.5px;color:var(--ink-2)}
.formmsg{font-size:14px;min-height:1.4em}
.formmsg.ok{color:var(--sage);font-weight:700}.formmsg.err{color:#b3261e;font-weight:700}
/* Cart + checkout */
.cartwrap{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(0,1fr);gap:clamp(20px,4vw,48px);align-items:start;padding-block:24px 64px}
@media (max-width:900px){.cartwrap{grid-template-columns:minmax(0,1fr)}}
.cline{display:grid;grid-template-columns:72px minmax(0,1fr) auto;gap:14px;align-items:center;padding:16px 0;border-bottom:1px solid var(--line)}
.cline .thumb{width:72px;height:72px;border-radius:10px;background:var(--bg);display:grid;place-items:center}
.cline .thumb svg{width:46%;color:var(--tone)}
.cline b{display:block;font-size:15px;line-height:1.3}
.cline small{font-size:13px;color:var(--ink-3)}
.cline .qty{margin-top:8px}
.cline .rm{background:none;border:0;padding:0;margin-left:10px;font-size:13px;text-decoration:underline;color:var(--ink-2)}
.cline .p{font-weight:700;font-variant-numeric:tabular-nums;white-space:nowrap}
.summary{background:var(--blush);border-radius:var(--r);padding:22px;display:grid;gap:10px;position:sticky;top:calc(env(safe-area-inset-top,0px) + 90px)}
@media (max-width:900px){.summary{position:static}}
.summary h2{font-size:20px}
.srow{display:flex;justify-content:space-between;gap:12px;font-size:15px;font-variant-numeric:tabular-nums}
.srow.disc{color:var(--berry);font-weight:700}
.srow.tot{border-top:1px solid var(--blush-2);padding-top:10px;font-size:17px;font-weight:700}
.srow.tot b{font-family:var(--f-display);font-size:26px}
.promo{display:flex;gap:8px}
.promo input{flex:1;min-width:0;border:1.5px solid var(--line);border-radius:999px;padding:10px 14px;font-size:14px;text-transform:uppercase;background:#fff}
.promo .btn{padding:10px 16px;font-size:14px}
.co-item{display:grid;grid-template-columns:52px minmax(0,1fr) auto;gap:12px;align-items:center;font-size:14px}
.co-item .thumb{width:52px;height:52px;border-radius:8px;background:var(--bg);display:grid;place-items:center;position:relative}
.co-item .thumb svg{width:46%;color:var(--tone)}
.co-item .thumb i{position:absolute;top:-6px;right:-6px;min-width:20px;height:20px;border-radius:999px;background:var(--ink);color:#fff;font-size:11px;font-style:normal;font-weight:700;display:grid;place-items:center;padding-inline:5px}
.co-steps{display:flex;gap:8px;flex-wrap:wrap;font-size:13px;font-weight:700;color:var(--ink-3);margin-bottom:6px}
.co-steps span[aria-current]{color:var(--berry)}
.secure{display:flex;gap:8px;align-items:center;font-size:13px;color:var(--ink-2)}
.secure svg{width:18px;height:18px;fill:none;stroke:var(--sage);stroke-width:2}
/* Order timeline (confirmation, tracking) */
.track{display:grid;gap:0;margin-top:8px}
.track li{display:grid;grid-template-columns:28px minmax(0,1fr);gap:12px;padding-bottom:18px;position:relative;list-style:none}
.track li::before{content:"";position:absolute;left:13px;top:26px;bottom:0;width:2px;background:var(--line)}
.track li:last-child::before{display:none}
.track .dot{width:28px;height:28px;border-radius:50%;border:2px solid var(--line);background:#fff;display:grid;place-items:center;font-size:13px;font-weight:700;color:var(--ink-3)}
.track li.done .dot{background:var(--sage);border-color:var(--sage);color:#fff}
.track li.now .dot{border-color:var(--berry);color:var(--berry)}
.track b{display:block;font-size:15px}
.track span{font-size:14px;color:var(--ink-2)}
.track ol{margin:0;padding:0}
/* Account */
.acct{max-width:480px}
.tabbar{display:flex;gap:8px;margin-bottom:18px}
/* Blog */
.blog-feat{display:grid;grid-template-columns:minmax(0,1.2fr) minmax(0,1fr);gap:28px;align-items:center;background:#fff;border:1px solid var(--blush-2);border-radius:var(--r);padding:clamp(16px,3vw,24px);margin-bottom:28px}
@media (max-width:800px){.blog-feat{grid-template-columns:minmax(0,1fr)}}
.blog-feat h2{font-size:clamp(24px,3vw,34px)}
.post .post__art{position:relative}
.post a.stretch::after{content:"";position:absolute;inset:0}
.post{position:relative}
.byline{display:flex;flex-wrap:wrap;gap:6px 14px;font-size:14px;color:var(--ink-3)}
.share{display:flex;gap:8px;flex-wrap:wrap;align-items:center;font-size:14px}
.share a,.share button{border:1.5px solid var(--line);background:#fff;border-radius:999px;padding:6px 12px;font-size:13px;font-weight:700}
.pcall{display:grid;grid-template-columns:72px minmax(0,1fr) auto;gap:14px;align-items:center;background:#fff;border:1px solid var(--blush-2);border-radius:var(--r);padding:14px;margin-block:6px}
.pcall .thumb{width:72px;height:72px;border-radius:10px;background:var(--bg);display:grid;place-items:center}
.pcall .thumb svg{width:44%;color:var(--tone)}
.pcall b{color:var(--ink);display:block}
.pcall small{font-size:13px;color:var(--ink-3)}
@media (max-width:520px){.pcall{grid-template-columns:56px minmax(0,1fr)}.pcall .btn{grid-column:1/-1}}
/* VS pages */
.vs{display:grid;grid-template-columns:minmax(0,1fr) auto minmax(0,1fr);gap:16px;align-items:stretch}
.vs__p{background:#fff;border:1px solid var(--blush-2);border-radius:var(--r);padding:20px;display:grid;gap:8px;justify-items:center;text-align:center;align-content:start}
.vs__p .card__media{width:100%;aspect-ratio:4/3}
.vs__p .card__media svg{width:26%}
.vs__p h2{font-size:20px}
.vs__x{align-self:center;font-family:var(--f-display);font-weight:800;font-size:22px;color:var(--berry)}
@media (max-width:640px){.vs{grid-template-columns:minmax(0,1fr)}.vs__x{justify-self:center}}
.win{display:inline-block;background:var(--sage-lt);color:var(--sage);border-radius:999px;padding:2px 9px;font-size:12px;font-weight:700;margin-left:6px}
/* Brands A–Z */
.az-letters{display:flex;flex-wrap:wrap;gap:6px;margin-block:16px}
.az-letters a{min-width:34px;height:34px;display:grid;place-items:center;border-radius:8px;border:1px solid var(--line);font-weight:700;font-size:14px;background:#fff}
.az-letters a:hover{border-color:var(--berry)}
.az-group{display:grid;gap:12px;margin-top:24px;scroll-margin-top:120px}
.az-group h2{font-size:24px;color:var(--berry)}
/* 404 */
.nf{text-align:center;display:grid;gap:14px;justify-items:center;padding-block:64px 40px}
.nf b{font-family:var(--f-display);font-size:clamp(64px,12vw,120px);line-height:1;color:var(--berry)}
.sitemap{columns:3 220px;column-gap:32px}
.sitemap section{break-inside:avoid;margin-bottom:22px}
.sitemap h2{font-size:17px;margin-bottom:8px}
.sitemap a{display:block;padding:3px 0;color:var(--ink-2)}
.sitemap a:hover{color:var(--berry)}
"""
