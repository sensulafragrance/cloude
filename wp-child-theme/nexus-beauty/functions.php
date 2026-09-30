<?php
/**
 * Nexus Beauty child theme for GeneratePress.
 *
 * Every feature lives in its own file in /inc so it's easy to find, change or switch off.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

define( 'NEXUS_VERSION', '1.0.0' );
define( 'NEXUS_DIR', get_stylesheet_directory() );
define( 'NEXUS_URI', get_stylesheet_directory_uri() );

$nexus_modules = array(
	'helpers',          // Options, icons, small utilities.
	'setup',            // Theme supports, menus, widget areas.
	'customizer',       // Appearance > Customize > Nexus Beauty.
	'enqueue',          // CSS, JS, fonts and speed tweaks.
	'layout',           // Header, footer, announcement bar, floating buttons.
	'schema',           // Organization and search markup when no SEO plugin is active.
	'patterns',         // Block patterns for the home page and info pages.
	'shortcodes',       // [nexus_product_tabs], [nexus_kit] and more.
	'admin',            // Setup checklist and notices.
);

// WooCommerce features load only when WooCommerce is active.
$nexus_woo_modules = array(
	'product-fields',   // Batch, expiry, size, tagline and extra product details.
	'woo-loop',         // Product cards, badges and shop filters.
	'woo-single',       // Product page additions.
	'woo-cart',         // Side cart, free delivery bar, bundle discounts.
	'woo-checkout',     // Pakistan-friendly checkout and order details.
	'wishlist',         // Wishlist with cookie + account sync.
	'recently-viewed',  // Recently viewed products.
);

foreach ( $nexus_modules as $nexus_module ) {
	require_once NEXUS_DIR . '/inc/' . $nexus_module . '.php';
}

add_action(
	'after_setup_theme',
	function () use ( $nexus_woo_modules ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		foreach ( $nexus_woo_modules as $module ) {
			require_once NEXUS_DIR . '/inc/' . $module . '.php';
		}
	},
	5
);
