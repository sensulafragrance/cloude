<?php
/**
 * Theme setup: supports, menus and widget areas.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_child_theme_textdomain( 'nexus-beauty', NEXUS_DIR . '/languages' );

		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 260, 'flex-width' => true, 'flex-height' => true ) );

		register_nav_menus(
			array(
				'primary'       => __( 'Main menu (header and mobile)', 'nexus-beauty' ),
				'nexus-footer-1' => __( 'Footer: Shop', 'nexus-beauty' ),
				'nexus-footer-2' => __( 'Footer: Help', 'nexus-beauty' ),
				'nexus-footer-3' => __( 'Footer: Company', 'nexus-beauty' ),
			)
		);
	},
	20
);

add_action(
	'widgets_init',
	function () {
		register_sidebar(
			array(
				'name'          => __( 'Shop filters (extra)', 'nexus-beauty' ),
				'id'            => 'nexus-shop-filters',
				'description'   => __( 'Optional extra filters shown under the built-in shop filters, e.g. "Filter products by attribute".', 'nexus-beauty' ),
				'before_widget' => '<div id="%1$s" class="nx-fgroup widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="nx-fgroup__title">',
				'after_title'   => '</h3>',
			)
		);
	}
);

/**
 * Full-width layout for shop, product, cart, checkout and account pages.
 * The shop has its own filter column, so the GeneratePress sidebar is not needed there.
 */
add_filter(
	'generate_sidebar_layout',
	function ( $layout ) {
		if ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
			return 'no-sidebar';
		}
		if ( is_page_template( 'page-templates/template-full-width.php' ) || is_front_page() ) {
			return 'no-sidebar';
		}
		return $layout;
	}
);

/**
 * Hide the page title on the front page (the hero has its own heading).
 */
add_filter(
	'generate_show_title',
	function ( $show ) {
		return is_front_page() ? false : $show;
	}
);
