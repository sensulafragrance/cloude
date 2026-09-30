<?php
/**
 * Styles, scripts, fonts and speed tweaks.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

/**
 * Asset URL, using the minified file unless SCRIPT_DEBUG is on.
 *
 * @param string $path e.g. 'css/main.css'.
 * @return string
 */
function nexus_asset( $path ) {
	$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? $path : preg_replace( '/\.(css|js)$/', '.min.$1', $path );
	$use = file_exists( NEXUS_DIR . '/assets/' . $min ) ? $min : $path;
	return NEXUS_URI . '/assets/' . $use;
}

add_action(
	'wp_enqueue_scripts',
	function () {
		$ver = NEXUS_VERSION;

		if ( nexus_opt( 'load_fonts' ) ) {
			wp_enqueue_style( 'nexus-fonts', 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Figtree:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap', array(), null );
		}

		// Load after the parent theme so our styles win without !important.
		$deps = wp_style_is( 'generate-style', 'registered' ) ? array( 'generate-style' ) : array();
		wp_enqueue_style( 'nexus-main', nexus_asset( 'css/main.css' ), $deps, $ver );

		$css = sprintf(
			':root{--nx-accent:%1$s;--nx-ink:%2$s}',
			sanitize_hex_color( nexus_opt( 'accent' ) ) ?: '#8c2350',
			sanitize_hex_color( nexus_opt( 'ink' ) ) ?: '#241826'
		);
		wp_add_inline_style( 'nexus-main', $css );

		$deps = array();
		if ( class_exists( 'WooCommerce' ) ) {
			// Cart fragments keep the bag count and side cart correct on cached pages.
			wp_enqueue_script( 'wc-cart-fragments' );
			$deps[] = 'jquery';
		}
		wp_enqueue_script( 'nexus-main', nexus_asset( 'js/main.js' ), $deps, $ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );

		$data = array(
			'ajax'        => admin_url( 'admin-ajax.php' ),
			'nonce'       => wp_create_nonce( 'nexus' ),
			'loggedIn'    => is_user_logged_in(),
			'storeApi'    => esc_url_raw( rest_url( 'wc/store/v1/products' ) ),
			'shopUrl'     => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ),
			'home'        => home_url( '/' ),
			'cities'      => nexus_cities(),
			'defaultDays' => nexus_opt( 'default_days' ),
			'cutoff'      => (int) nexus_opt( 'cutoff_hour' ),
			'sundayOff'   => (bool) nexus_opt( 'sunday_off' ),
			'tzOffset'    => (float) get_option( 'gmt_offset', 5 ),
			'freeShip'    => (float) nexus_opt( 'free_shipping' ),
			'currency'    => function_exists( 'get_woocommerce_currency_symbol' ) ? html_entity_decode( get_woocommerce_currency_symbol() ) : 'Rs',
			'bundlePct'   => (int) nexus_opt( 'bundle_percent' ),
			'bundleMin'   => (int) nexus_opt( 'bundle_min' ),
			'i18n'        => array(
				'added'      => __( 'Added to your bag', 'nexus-beauty' ),
				'saved'      => __( 'Saved to your wishlist', 'nexus-beauty' ),
				'removed'    => __( 'Removed from your wishlist', 'nexus-beauty' ),
				'noResults'  => __( 'No products found. Try another word.', 'nexus-beauty' ),
				'viewAll'    => __( 'See all results', 'nexus-beauty' ),
				'arrives'    => __( 'Arrives', 'nexus-beauty' ),
				'orderIn'    => __( 'Order in the next %s for same-day dispatch.', 'nexus-beauty' ),
				'leavesOn'   => __( 'Orders placed now leave our warehouse on %s.', 'nexus-beauty' ),
				'chooseMore' => __( 'Choose %d more to get %d%% off.', 'nexus-beauty' ),
				'applied'    => __( '%d%% bundle discount applied.', 'nexus-beauty' ),
				'addN'       => __( 'Add %d to bag', 'nexus-beauty' ),
				'error'      => __( 'Something went wrong. Please try again.', 'nexus-beauty' ),
			),
		);
		wp_add_inline_script( 'nexus-main', 'var NEXUS = ' . wp_json_encode( $data ) . ';', 'before' );
	},
	20
);

// Preconnect to Google Fonts.
add_filter(
	'wp_resource_hints',
	function ( $urls, $type ) {
		if ( 'preconnect' === $type && nexus_opt( 'load_fonts' ) ) {
			$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
		}
		return $urls;
	},
	10,
	2
);

// Block editor gets the same look.
add_action(
	'enqueue_block_editor_assets',
	function () {
		wp_enqueue_style( 'nexus-editor', nexus_asset( 'css/main.css' ), array(), NEXUS_VERSION );
	}
);

/* ---------- Speed tweaks (all safe for WooCommerce) ---------- */

// Emoji scripts are not needed: every modern device renders emoji natively.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

// Head clean-up.
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

// Dashicons only for logged-in users (the admin bar needs them).
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( ! is_user_logged_in() ) {
			wp_dequeue_style( 'dashicons' );
		}
	},
	100
);

// Lazy-load every product image except the first row on archives, and prioritise the main product image.
add_filter(
	'wp_get_attachment_image_attributes',
	function ( $attr ) {
		if ( isset( $attr['class'] ) && false !== strpos( $attr['class'], 'wp-post-image' ) && function_exists( 'is_product' ) && is_product() && ! did_action( 'woocommerce_after_single_product_summary' ) ) {
			$attr['fetchpriority'] = 'high';
			$attr['loading']       = 'eager';
		}
		if ( empty( $attr['decoding'] ) ) {
			$attr['decoding'] = 'async';
		}
		return $attr;
	}
);
