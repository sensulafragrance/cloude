=== Nexus Beauty ===
Parent theme: GeneratePress (free version is enough)
Requires: WordPress 6.4+, PHP 7.4+, WooCommerce 8.5+ (9.6+ for Brands)
Version: 2.0.0
License: GPLv2 or later

The Sensula storefront design as a WooCommerce child theme for GeneratePress (Pakistan).
Every page of the store uses the design: header, search panel, mobile menu, side bag, home page,
shop, collections (categories), brand pages, product pages, info pages, blog and footer.
No page builder and no extra plugins needed.

== Installation ==

1. Appearance > Themes > Add New: install "GeneratePress" (you don't need to activate it; it only has to be installed).
2. Install and activate WooCommerce.
3. Appearance > Themes > Add New > Upload Theme: upload nexus-beauty.zip and activate "Nexus Beauty".

That's it. On activation the theme sets up the store by itself (see below) and shows a summary.
You can see the log and run it again any time at Appearance > Nexus setup.

== What happens automatically on activation ==

Pages (only the ones that don't exist yet; nothing is overwritten):
Home, Routines (problem kits), All collections, Brands A–Z, Offers, About us, Contact us,
How we check authenticity, Delivery information, Returns & refunds, FAQs, Terms of service,
Privacy policy, Wishlist, Track your order, Journal (blog).

Site:
- The home page becomes the front page and Journal the blog page (your previous front page is
  remembered and restored if you switch theme).
- Menus are built like the design: main menu = your product categories + Brands + Offers;
  footer columns Shop, Brands, Help, Company. Menu locations that already have your own menu are left alone.

Store (runs as soon as WooCommerce is active, even if you activate WooCommerce later):
- Every product, existing and new, uses the design automatically.
- 34 starter products are imported only if your store has no products yet. They carry the design's
  look (illustration and colours until you add photos), top picks (featured), launch dates for
  "Just arrived", linked sizes and scents, fragrance notes, quick facts and questions. Categories get
  the design's order, colours and tab notes; brands get origin (Pakistani / International) and notes.
  To add them to a store that already has products: Appearance > Nexus setup > "Import starter
  products" (products whose SKU already exists are skipped). Prices for St. Ives, Dove Body Polish,
  Cetaphil and Rederm Clariderm are estimates.
- New store only: currency set to Pakistani rupee and store country to Pakistan.
- Cart and Checkout switched to the classic version (needed for the Pakistan checkout features);
  the previous content is saved and restored if you switch theme.
- If you have no shipping zones: a Pakistan zone with a Rs 250 flat rate and free delivery over the
  threshold (default Rs 5,000). If no payment method is enabled: Cash on delivery.

Updating from version 1: upload the new zip (Appearance > Themes > Add New > Upload, "Replace current").
The setup runs once more on your next admin page view: it adds what's new and rebuilds only the main
menu the theme made itself.

== How the design fills itself ==

Home page (all automatic):
- Category tiles and tabs: your top-level product categories, in their order (Products > Categories, drag to reorder).
- Top picks: featured products (the star in Products > All products).
- Just arrived: the four newest products.
- Own-label spotlight: products tagged "our-label". Minis offer: products tagged "mini".
- Dermatologist brands: brands marked as derm (Products > Brands > edit).
- Shop by concern: product tags that aren't badges (e.g. Hair fall, Dark spots).
- Brands: Products > Brands, with Pakistani / International tabs.
- Texts, hero photo and colours: Appearance > Customize > Nexus Beauty > Home page.

Cards: brand, name, tagline, price, size and badges. Badge tags: our-label, mini, derm, bestseller,
korea, imported, pakistani (plus automatic New, sale %, Sold out, Only X left).
Photos: white-background packshots sit on the design's tinted cards; set "Photos fill the card"
(Customize > Nexus Beauty > Shop & products) if your photos are lifestyle shots. Products without a
photo show the design's illustration in the category colour.

Product page: gallery (your product images), brand, title, subtitle, price with "Save x%", scent
swatches and size buttons (linked products; see "Nexus details"), variation options shown as buttons,
fragrance notes, stock, quantity, Add to bag (no reload), Buy now, free delivery bar, batch card,
delivery promises with an arrival date, payment badges, quick facts, accordions (description, is it
right for me, key ingredients, how to use, ingredients, video, delivery & returns), frequently bought
together (cross-sells), reviews, questions, related products, recently viewed and a sticky bar.

== After activation (optional) ==

- Appearance > Customize > Nexus Beauty: home page texts, WhatsApp number, phone, popular searches,
  free delivery threshold, delivery cities and days, dispatch cut-off, bundle discount, colours.
- Add product photos, and fill "Nexus details" on your own products (tagline, size, batch and expiry,
  facts, how to use, ingredients, questions).
- Kits on the Routines page pick products by SKU, falling back to best-sellers with the matching tag.

== Import the sample products manually (alternative) ==

WooCommerce > Products > Import > choose sample-data/nexus-products.csv > "Run the importer".
Keep the "Meta: _nx_..." columns mapped to "Import as meta data".

== Features ==

Store
* The design's header: logo (or your custom logo), category menu row, search panel with live results,
  account, wishlist and bag. Mobile menu and side bag slide in.
* Side bag with +/− quantities, remove, bundle discounts and a free delivery progress bar.
* Wishlist for guests (cookie) that syncs to the customer's account when logged in. Shortcode: [nexus_wishlist].
* Recently viewed products (stored in the browser, works with page caching).
* Optional announcement bar, floating WhatsApp button and cookie notice.

Shop, collections and brand pages
* The design's header band with breadcrumbs, title, intro and concern chips.
* Filters without plugins: category, brand origin, brand, concern, on offer, in stock, price slider.
  Sidebar on desktop, slide-in panel on phones. Filtered URLs are set to noindex.
* Sorting, result count, active filter chips, pagination, "Build a routine" banner, buying guide and FAQ.

Checkout (classic checkout)
* No company or second address line; postcode hidden for Pakistan; city suggestions.
* Checks Pakistani mobile numbers (03XX XXXXXXX) and saves them in one format.
* Optional email (Customizer).
* Delivery estimate above payment methods.
* Batch, expiry and kit name are saved on every order line (visible in admin, emails and invoices).
* Thank-you page with "what happens next", delivery estimate, tracking and WhatsApp links.

Product details (Products > Edit > "Nexus details" tab)
* Tagline, subtitle, size, amount + unit (for price per ml), batch, manufactured, expiry, good for, not for,
  key ingredients (Name | Role | What it does), full ingredients, how to use, questions (Question | Answer),
  YouTube URL, quick facts, fragrance notes, wear-time bars, linked sizes group and scents group,
  frequently-bought-together heading, and the illustration and colours used when there's no photo.

Page building
* Block patterns for info pages: hero, trust strip, categories, product tabs, kit, how we check, brands, FAQ, WhatsApp banner.
* Shortcodes:
  [nexus_product_tabs tabs="best,new,sale" limit="8" columns="4"]
  [nexus_categories limit="12"]   [nexus_brands limit="24"]   [nexus_trust]
  [nexus_kit skus="SKU1,SKU2,SKU3" tag="hair-fall" title="Hair Fall Kit" who="..." roles="A|B|C" how="A|B|C" tips="A|B" avoid="A|B"]
  [nexus_delivery_table]   [nexus_contact_form]
  [nexus_videos urls="https://youtu.be/...,https://youtu.be/..." titles="One|Two"]
  [nexus_whatsapp text="Chat with us"]   [nexus_wishlist]   [nexus_recently_viewed limit="8"]

Speed
* One stylesheet (~15 KB gzipped) and one script (~6 KB gzipped), minified; set SCRIPT_DEBUG to load the readable ones.
* Deferred JavaScript, no jQuery of our own (only WooCommerce's), no icon fonts (inline SVG).
* Emoji scripts removed, dashicons only for logged-in users, head clean-up, image decoding async,
  main product image loaded with high priority, the rest lazy-loaded.
* Google Fonts can be switched off (Customize > Nexus Beauty > Colours & fonts) if you host fonts locally.
* Works with page caching (LiteSpeed Cache, WP Rocket, etc.). The wishlist page is excluded from caching automatically.

== Notes ==

* The Pakistan checkout features use the classic checkout. The block checkout ignores these PHP hooks;
  the setup page switches it for you.
* The shop, product card and product page use their own templates (woocommerce/ folder) so they match
  the design exactly. They still use WooCommerce's add-to-cart form, variations, reviews, structured data
  and hooks, so payment, add-on and review plugins keep working.
* The stylesheet (assets/css/main.css) is the Sensula design stylesheet, plus a second part for
  WooCommerce screens (cart, checkout, account). GeneratePress and WooCommerce default styles are not loaded.
* Using Astra instead of GeneratePress needs a different header/footer module (Astra uses different hooks).
* Translation-ready (text domain: nexus-beauty).
