<?php
/**
 * Wishlist: saved in a cookie for everyone (works with page caching),
 * and synced to the customer's account when they are logged in.
 *
 * Shortcode: [nexus_wishlist]
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

/**
 * Heart button for a product.
 *
 * @param int $id Product ID.
 */
function nexus_wish_button( $id ) {
	printf(
		'<button class="nx-wish" type="button" data-nx-wish="%1$d" aria-pressed="false" aria-label="%2$s">%3$s</button>',
		(int) $id,
		/* translators: %s: product name */
		esc_attr( sprintf( __( 'Save %s to wishlist', 'nexus-beauty' ), get_the_title( $id ) ) ),
		nexus_icon( 'heart' ) // phpcs:ignore WordPress.Security.EscapeOutput
	);
}

// Heart on the product page, next to the title.
add_action(
	'woocommerce_single_product_summary',
	function () {
		global $product;
		nexus_wish_button( $product->get_id() );
	},
	4
);

/**
 * Wishlist IDs for the current visitor.
 *
 * @return int[]
 */
function nexus_wishlist_ids() {
	$ids = nexus_cookie_ids( 'nx_wishlist' );
	if ( is_user_logged_in() ) {
		$saved = get_user_meta( get_current_user_id(), '_nx_wishlist', true );
		$ids   = array_unique( array_merge( $ids, is_array( $saved ) ? array_map( 'absint', $saved ) : array() ) );
	}
	return array_values( array_filter( $ids ) );
}

/**
 * AJAX: save the wishlist to the logged-in customer's account and return the merged list.
 */
function nexus_ajax_wishlist_sync() {
	check_ajax_referer( 'nexus', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( null, 403 );
	}
	$uid     = get_current_user_id();
	$ids     = isset( $_POST['ids'] ) ? array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['ids'] ) ) ) ) ) : array();
	$replace = ! empty( $_POST['replace'] );
	$saved   = get_user_meta( $uid, '_nx_wishlist', true );
	$saved   = is_array( $saved ) ? array_map( 'absint', $saved ) : array();
	$merged  = $replace ? $ids : array_unique( array_merge( $saved, $ids ) );
	$merged  = array_slice( array_values( array_filter( $merged, 'wc_get_product' ) ), 0, 100 );
	update_user_meta( $uid, '_nx_wishlist', $merged );
	wp_send_json_success( array( 'ids' => $merged ) );
}
add_action( 'wp_ajax_nexus_wishlist_sync', 'nexus_ajax_wishlist_sync' );

// The wishlist page shows personal data, so it must never be cached.
add_action(
	'template_redirect',
	function () {
		$post = get_post();
		if ( is_singular() && $post && has_shortcode( $post->post_content, 'nexus_wishlist' ) ) {
			nocache_headers();
			if ( ! defined( 'DONOTCACHEPAGE' ) ) {
				define( 'DONOTCACHEPAGE', true );
			}
		}
	}
);

add_shortcode(
	'nexus_wishlist',
	function () {
		$ids = nexus_wishlist_ids();
		if ( ! $ids ) {
			return '<div class="nx-empty" data-nx-wish-empty><p><b>' . esc_html__( 'Your wishlist is empty.', 'nexus-beauty' ) . '</b></p><p>' . esc_html__( 'Tap the heart on any product to save it here.', 'nexus-beauty' ) . '</p><a class="nx-btn nx-btn--primary" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html__( 'Browse products', 'nexus-beauty' ) . '</a></div>';
		}
		return '<div class="nx-wishlist" data-nx-wishlist>' . do_shortcode( '[products ids="' . esc_attr( implode( ',', $ids ) ) . '" orderby="post__in" limit="100" columns="4" cache="false"]' ) . '</div>';
	}
);
