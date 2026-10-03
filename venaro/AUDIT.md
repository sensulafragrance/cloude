# Venaro site: audit and rebuild

`index.html` replaces `Venaro-Final.html`. Open it directly in a browser, or serve the `venaro/` folder from any static host. Images are in `img/`.

## What was wrong in the mockup

| Area | Problem | Fix |
| --- | --- | --- |
| Weight | 3.4MB single file. All 30 images were base64 inside the script, so the browser downloaded every image before showing anything. `loading="lazy"` could not help. | Images extracted to `img/` and recompressed (1.7MB total, 440px and 800px versions). Only the hero loads up front; everything else loads as you scroll. The page itself (HTML, CSS and JS) is 77KB, about 20KB gzipped. |
| Code | Five stacked `<style>` blocks and four `<script>` blocks, each overriding the last with `!important`. Font sizes were set up to four times for the same element. | One stylesheet, one script, one set of design tokens. |
| Type on mobile | 13 different text sizes, many from 6px to 11px: promise strip 8px, "View all" 8px, scent family captions 7px, discovery fact labels 7px, footer links 11px, PDP labels 9–10px, filters line 9px. | A fixed scale. Body text is 16px. No reading text is under 13px; secondary text is 14px. Headings scale smoothly with `clamp()`. |
| Contrast | Gold `#8a6c46` used for text was close to the 4.5:1 limit. Light-grey captions on the dark sections and footer were hard to read. | Muted text `#5D5950` (6.6:1), bronze `#7A5A33` (5.9:1), light text on dark `#C2BBAD` (8.4:1). All pass WCAG AA. |
| Homepage length | 11 product carousels (perfumes, men, women, signature, new, candles, diffusers, body, hair, linen, gifts). The homepage was 18,000px tall on a phone. | 3 carousels with tabs (Perfumes: Bestsellers/For him/For her/Signature/New; Home fragrance: Candles/Diffusers/Linen; Mists: Body/Hair). The homepage is now about 9,300px on a phone. |
| Product cards | A three-way size selector on every card, squeezed into 165px on phones ("5ml / Tester" wrapped). | Clean cards: image, family, name, notes, price. Sizes are chosen on the product page. A round + button adds the default size. |
| Gift cards | Names broke mid-word ("Impression / s"). | Fixed. |
| Header | Search was a "⌕" text glyph, the bag count was a tiny circle and the brand overflowed at 320px. | SVG icons, a 44px tap target for every control, a count badge, and a brand size that fits at 320px. |
| Touch targets | Footer links and many text links were under 32px tall. | 40–56px everywhere. |
| Mobile commerce | No sticky purchase bar on the product page. | A sticky "Add to bag" bar appears once the main button scrolls away. The discovery and gift builders have a bottom summary bar. |

## Pages

| Route | Page |
| --- | --- |
| `#/` | Home: hero, categories, perfume carousel, discovery set, scent families, home ritual, home fragrance, mists, gifting, FAQ |
| `#/shop/<all,perfumes,home,candles,diffusers,mists,linen>` | Collection grid with collection, scent family and sort filters |
| `#/shop/all/<fresh,floral,woody,sweet,oud,musk>` | Scent family page |
| `#/product/<id>` | Product page: gallery, size choice with price per ml, quantity, notes, how to use, related products |
| `#/discovery` | Discovery set builder (5 × 5ml) |
| `#/gifts`, `#/gifts/build` | Gift sets with budget filter; gift box builder (box, up to 3 items, message) |

Bag, search and menu are drawers. The bag is saved in the browser.

## Checked

- Widths 320, 375, 768, 1024 and 1440px on all routes: no horizontal scrolling, no text under 13px except the "PERFUMES" line in the logo (11px).
- Add to bag from cards, product page, discovery set, gift sets and gift box; quantities and subtotal add up.
- Filters, search, keyboard focus, `prefers-reduced-motion`.

## Removed on purpose

- Product comparison tray, the scent quiz modal and the placeholder video reviews. They added weight and screens without real content. They are easy to bring back once there are real reviews.
- The journal teaser, which linked to placeholder text.

## Before launch

Prices, names and stock are sample data. Checkout, newsletter and policies are not connected. Remove `<meta name="robots" content="noindex,nofollow">` and the preview notice when the store goes live.
