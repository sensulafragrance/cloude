=== Nexus Beauty ===
Parent theme: GeneratePress (free version is enough)
Requires: WordPress 6.4+, PHP 7.4+, WooCommerce 8.5+ (9.6+ for Brands)
Version: 1.0.0
License: GPLv2 or later

A fast WooCommerce child theme for GeneratePress, built for Nexus Beauty (Pakistan).
No page builder and no extra plugins needed. About 8.5 KB of CSS and 5 KB of JavaScript (gzipped).

== Installation ==

1. Appearance > Themes > Add New: install and activate "GeneratePress" once, so it's available as the parent.
2. Appearance > Themes > Add New > Upload Theme: upload nexus-beauty.zip and activate "Nexus Beauty".
3. Install and activate WooCommerce (Settings > General: currency Pakistani rupee; Store country Pakistan).
4. Appearance > Nexus setup: follow the checklist. It has one-click buttons to
   - create the Wishlist page,
   - create the Order tracking page,
   - switch Cart and Checkout to the classic version (needed for the Pakistan checkout features).
5. Appearance > Menus:
   - "Main menu (header and mobile)": your categories. Add the CSS class "mega" to a top-level item
     (Screen Options > CSS Classes) to turn its sub-menu into a full-width mega menu.
   - "Footer: Shop", "Footer: Help", "Footer: Company": the menu name is used as the column title.
6. Appearance > Customize > Nexus Beauty: WhatsApp number, announcement messages, free delivery
   threshold, delivery cities and days, dispatch cut-off, bundle discount, colours.
7. WooCommerce > Settings > Shipping: add "Free shipping" with the same minimum amount as the
   free delivery bar (default Rs 5,000), and a flat rate (e.g. Rs 250) for other orders.
8. Home page: Pages > Add New > "+ > Patterns > Nexus Beauty > Full home page". Set the page
   template to "Nexus full width", then Settings > Reading > "A static page" > choose it.
   Replace the batch-stamp art in the hero with a product photo (delete the HTML block, add an Image block).
   In the kit section, put real product IDs in [nexus_kit ids="..."].

Recommended GeneratePress settings (Customize > Layout > Container): "One container", content width 1240px.

== Import the sample products ==

WooCommerce > Products > Import > choose sample-data/nexus-products.csv > "Run the importer".
Keep the "Meta: _nx_..." columns mapped to "Import as meta data". Add product images afterwards.
Prices for St. Ives, Dove Body Polish, Cetaphil and Rederm Clariderm are estimates: please check them.

== Features ==

Store
* Custom header: logo (or the Nexus mark), mega menu, live product search, account, wishlist and bag.
* Rotating announcement bar, floating WhatsApp button, back-to-top button, cookie notice.
* Side cart (drawer) opens after adding to bag, with +/− quantity buttons and a free delivery progress bar.
* Wishlist for guests (cookie) that syncs to the customer's account when logged in. Shortcode: [nexus_wishlist].
* Recently viewed products (stored in the browser, works with page caching).

Shop
* Product cards with brand, tagline, size, sale %, New, low-stock and tag badges.
* Built-in filters without plugins: price, on offer, in stock, concern/type (product tags), brand.
  Filters are a sidebar on desktop and a slide-in panel on phones. Filtered URLs are set to noindex.
* Category chips above the grid, sorting and pagination styled.
* Tag slugs that show badges: bestseller, mini, derm, our-label, korea, pakistani.

Product page
* Brand link, tagline, price per ml/g, batch and expiry stamp (also per variation).
* "Buy now, pay on delivery" button that goes straight to checkout.
* Delivery estimate by city (store timezone, cut-off hour, Sundays off), reassurance grid, WhatsApp help.
* "Is it right for me?" section, key ingredients, and extra tabs: How to use, Ingredients, Questions, Video.
* Frequently bought together from Cross-sells, with a bundle discount (default 15% for 3+ products).
* Sticky add-to-bag bar, quantity steppers, lightweight YouTube embeds.

Checkout (classic checkout)
* No company or second address line; postcode hidden for Pakistan; city suggestions.
* Checks Pakistani mobile numbers (03XX XXXXXXX) and saves them in one format.
* Optional email (Customizer).
* Delivery estimate above payment methods.
* Batch, expiry and kit name are saved on every order line (visible in admin, emails and invoices).
* Thank-you page with "what happens next", delivery estimate, tracking and WhatsApp links.

Product details (Products > Edit > "Nexus details" tab)
* Tagline, size, amount + unit (for price per ml), batch, manufactured, expiry, good for, not for,
  key ingredients (Name | Role | What it does), full ingredients, how to use, questions (Question | Answer), YouTube URL.

Page building
* Block patterns: full home page, hero, trust strip, categories, product tabs, kit, how we check, brands, FAQ, WhatsApp banner.
* Shortcodes:
  [nexus_product_tabs tabs="best,new,sale" limit="8" columns="4"]
  [nexus_categories limit="12"]   [nexus_brands limit="24"]   [nexus_trust]
  [nexus_kit ids="12,34,56" title="Hair Fall Kit" who="..." roles="A|B|C" how="A|B|C" tips="A|B" avoid="A|B"]
  [nexus_videos urls="https://youtu.be/...,https://youtu.be/..." titles="One|Two"]
  [nexus_whatsapp text="Chat with us"]   [nexus_wishlist]   [nexus_recently_viewed limit="8"]

Speed
* Minified CSS/JS (the .min files load automatically; set SCRIPT_DEBUG to load the readable ones).
* Deferred JavaScript, no jQuery of our own (only WooCommerce's), no icon fonts (inline SVG).
* Emoji scripts removed, dashicons only for logged-in users, head clean-up, image decoding async,
  main product image loaded with high priority.
* Google Fonts can be switched off (Customize > Nexus Beauty > Colours & fonts) if you host fonts locally.
* Works with page caching (LiteSpeed Cache, WP Rocket, etc.). The wishlist page is excluded from caching automatically.

== Notes ==

* The Pakistan checkout features use the classic checkout. The block checkout ignores these PHP hooks;
  the setup page switches it for you.
* Product pages are built with WooCommerce hooks, not template copies, so WooCommerce updates won't break the theme.
* Using Astra instead of GeneratePress needs a different header/footer module (Astra uses different hooks).
* Translation-ready (text domain: nexus-beauty).
