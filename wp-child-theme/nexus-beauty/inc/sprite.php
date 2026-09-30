<?php
/**
 * SVG sprite: interface icons (ic-*) and the product illustrations (i-*) from the Sensula design.
 * Printed once after <body> so every <use href="#..."> on the page works.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

return <<<'SVG'
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
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
 <symbol id="ic-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"/></symbol>

 <symbol id="ic-whatsapp" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"><path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.6-1.2A9 9 0 1 0 12 3Z"/><path d="M8.8 8.6c.2-.5.4-.5.7-.5h.5c.2 0 .4 0 .5.4l.7 1.7c.1.2 0 .4-.1.5l-.5.6c-.1.1-.2.3 0 .5.4.8 1.6 2 2.5 2.4.2.1.4.1.5 0l.6-.7c.1-.2.3-.2.5-.1l1.6.8c.2.1.3.2.3.4 0 .6-.3 1.4-1 1.7-.6.3-1.4.4-3.2-.4-2-1-3.4-3-3.6-3.5-.3-.5-.7-1.5-.3-2.3Z"/></symbol>
 <symbol id="ic-play" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" fill="currentColor"/></symbol>
 <symbol id="ic-up" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 15 6-6 6 6"/></symbol>
</defs></svg>
SVG;
