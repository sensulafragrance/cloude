<?php
/**
 * Recently viewed products.
 *
 * The list is stored in the visitor's browser and drawn by JavaScript from the
 * WooCommerce Store API, so it stays correct even when pages are cached.
 *
 * Shortcode: [nexus_recently_viewed limit="8"]
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

add_shortcode(
	'nexus_recently_viewed',
	function ( $atts ) {
		$atts = shortcode_atts( array( 'limit' => 8 ), $atts, 'nexus_recently_viewed' );
		return function_exists( 'nexus_recent_block' ) ? nexus_recent_block( (int) $atts['limit'] ) : '';
	}
);

// Tell the page which product is being viewed (read by JavaScript).
add_filter(
	'body_class',
	function ( $classes ) {
		if ( function_exists( 'is_product' ) && is_product() ) {
			$classes[] = 'nx-product-' . (int) get_queried_object_id();
		}
		return $classes;
	}
);
