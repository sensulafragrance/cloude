<?php
/**
 * Product card in any WooCommerce loop (shortcodes, related products, wishlist).
 * Markup: the design's article.card, built by nexus_card() in inc/woo-loop.php.
 *
 * @package NexusBeauty
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

echo nexus_card( $product ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped inside nexus_card().
