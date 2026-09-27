# Sensula storefront (HTML template)

A static, multi-brand beauty & personal care store design: home, shop (category) and product pages.

| File | What it is |
| --- | --- |
| `index.html` | Home: hero, categories, bestsellers, offers, shop-by-concern, local/international brands, reviews, FAQ, newsletter |
| `shop.html` | Category/listing page: filters (category, brand origin, brand, concern, price), sort, product grid, buying guide, FAQ |
| `product.html` | Product page: gallery, size variants with price per ml, sticky add-to-bag, delivery promise, quick facts, bundle, reviews, Q&A, related products |
| `assets/css/styles.css` | All styles. Brand colours and fonts are tokens at the top of the file |
| `assets/js/main.js` | Cart drawer, free-delivery bar, filters, sorting, gallery, variants. Set `CURRENCY` and `FREE_SHIP` here |
| `build.py` | Generates the three pages plus `sitemap.xml`, `robots.txt`, `llms.txt` from one product list |
| `robots.txt`, `sitemap.xml`, `llms.txt` | Search engine and AI crawler files |

## Editing

1. Change products, brands, prices, currency and your domain at the top of `build.py`.
2. Run `python3 build.py` to regenerate the pages.
3. Replace the illustrated product shapes with real photos (`<img>` with `alt`, `width`, `height`, `loading="lazy"`).

## What's built in

**SEO:** one `<h1>` per page, keyword-led titles and meta descriptions, canonical URLs, Open Graph tags, breadcrumbs, semantic HTML, and JSON-LD for Organization, WebSite search, BreadcrumbList, CollectionPage/ItemList, Product (offers per size, shipping, returns, ratings, reviews) and FAQPage.

**AI / answer engines:** a "Quick facts" block on product pages, question-style FAQs with direct answers, category buying guides, and `llms.txt` summarising the store.

**Conversion:** free-delivery progress bar, trust strip (authentic, COD, returns), stock and dispatch cut-off messaging, price-per-ml sizes, sale badges with % saved, local vs imported labels, bundle discount, reviews with breakdown, sticky add-to-bag on mobile, one-tap "Buy now, pay on delivery", first-order code and newsletter capture.

Checkout and forms are front-end only. Connect them to your platform (Shopify, WooCommerce, or a custom backend).
