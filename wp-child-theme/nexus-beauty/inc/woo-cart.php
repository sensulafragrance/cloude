<?php
/**
 * Side cart (drawer), live quantity changes, free delivery progress and bundle discounts.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

/**
 * Free delivery progress bar.
 *
 * @return string
 */
function nexus_free_ship_bar() {
	$goal = (float) nexus_opt( 'free_shipping' );
	if ( $goal <= 0 || ! WC()->cart ) {
		return '';
	}
	$total = (float) WC()->cart->get_displayed_subtotal();
	$left  = max( 0, $goal - $total );
	$pct   = min( 100, $goal ? ( $total / $goal ) * 100 : 0 );
	$text  = $left > 0
		/* translators: %s: amount left */
		? sprintf( __( 'You\'re %s away from free delivery', 'nexus-beauty' ), '<b>' . wp_strip_all_tags( wc_price( $left ) ) . '</b>' )
		: '<b>' . esc_html__( 'You\'ve unlocked free delivery.', 'nexus-beauty' ) . '</b>';
	return '<div class="nx-ship"><p>' . wp_kses( $text, array( 'b' => array() ) ) . '</p><div class="nx-ship__track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . esc_attr( round( $pct ) ) . '" aria-label="' . esc_attr__( 'Progress to free delivery', 'nexus-beauty' ) . '"><span style="width:' . esc_attr( round( $pct, 1 ) ) . '%"></span></div></div>';
}

/**
 * Drawer body: free delivery bar + WooCommerce mini cart. Also used as a cart fragment.
 */
function nexus_drawer_body() {
	echo '<div class="nx-drawer-body">';
	if ( WC()->cart ) {
		echo nexus_free_ship_bar(); // phpcs:ignore WordPress.Security.EscapeOutput
		echo '<div class="widget_shopping_cart_content">';
		woocommerce_mini_cart();
		echo '</div>';
	}
	echo '</div>';
}

/**
 * Header count badge. Also used as a cart fragment.
 *
 * @return string
 */
function nexus_count_html() {
	$n = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	return '<span class="nx-count nx-cart-count"' . ( $n ? '' : ' hidden' ) . '>' . esc_html( $n ) . '</span>';
}

add_filter(
	'woocommerce_add_to_cart_fragments',
	function ( $fragments ) {
		ob_start();
		nexus_drawer_body();
		$fragments['div.nx-drawer-body']  = ob_get_clean();
		$fragments['span.nx-cart-count'] = nexus_count_html();
		return $fragments;
	}
);

// Quantity buttons inside the mini cart.
add_filter(
	'woocommerce_widget_cart_item_quantity',
	function ( $html, $cart_item, $cart_item_key ) {
		$product = $cart_item['data'];
		$max     = $product->get_max_purchase_quantity();
		$price   = WC()->cart->get_product_price( $product );
		return sprintf(
			'<span class="nx-mq" data-key="%1$s"><button type="button" data-nx-qty="-1" aria-label="%2$s">−</button><span class="nx-mq__n">%3$d</span><button type="button" data-nx-qty="1" aria-label="%4$s"%5$s>+</button><span class="nx-mq__price">%6$s</span></span>',
			esc_attr( $cart_item_key ),
			esc_attr__( 'One less', 'nexus-beauty' ),
			(int) $cart_item['quantity'],
			esc_attr__( 'One more', 'nexus-beauty' ),
			( $max > 0 && $cart_item['quantity'] >= $max ) ? ' disabled' : '',
			wp_kses_post( $price )
		);
	},
	10,
	3
);

/**
 * AJAX: change a cart line quantity, then return fresh fragments.
 */
function nexus_ajax_update_qty() {
	check_ajax_referer( 'nexus', 'nonce' );
	$key = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$qty = isset( $_POST['qty'] ) ? max( 0, (int) $_POST['qty'] ) : 0;
	if ( $key && WC()->cart->get_cart_item( $key ) ) {
		if ( 0 === $qty ) {
			WC()->cart->remove_cart_item( $key );
		} else {
			WC()->cart->set_quantity( $key, $qty, true );
		}
	}
	WC_AJAX::get_refreshed_fragments();
}
add_action( 'wp_ajax_nexus_update_qty', 'nexus_ajax_update_qty' );
add_action( 'wp_ajax_nopriv_nexus_update_qty', 'nexus_ajax_update_qty' );

/**
 * AJAX: add several products at once as a bundle (frequently bought together, kits).
 */
function nexus_ajax_add_bundle() {
	check_ajax_referer( 'nexus', 'nonce' );
	$ids  = isset( $_POST['ids'] ) ? array_slice( array_filter( array_map( 'absint', (array) wp_unslash( $_POST['ids'] ) ) ), 0, 12 ) : array();
	$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	if ( ! $ids ) {
		wp_send_json_error( array( 'message' => __( 'Choose at least one product.', 'nexus-beauty' ) ), 400 );
	}
	$bundle = count( $ids ) >= max( 2, (int) nexus_opt( 'bundle_min' ) ) ? wp_generate_password( 8, false ) : '';
	$added  = 0;
	foreach ( $ids as $id ) {
		$product = wc_get_product( $id );
		if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() || ! $product->is_type( 'simple' ) ) {
			continue;
		}
		$data = $bundle ? array( 'nx_bundle' => $bundle, 'nx_bundle_name' => $name ) : array();
		if ( WC()->cart->add_to_cart( $id, 1, 0, array(), $data ) ) {
			$added++;
		}
	}
	if ( ! $added ) {
		wc_clear_notices();
		wp_send_json_error( array( 'message' => __( 'These products are not available right now.', 'nexus-beauty' ) ), 400 );
	}
	wc_clear_notices();
	WC_AJAX::get_refreshed_fragments();
}
add_action( 'wp_ajax_nexus_add_bundle', 'nexus_ajax_add_bundle' );
add_action( 'wp_ajax_nopriv_nexus_add_bundle', 'nexus_ajax_add_bundle' );

// Keep bundle data in the session.
add_filter(
	'woocommerce_get_cart_item_from_session',
	function ( $item, $values ) {
		foreach ( array( 'nx_bundle', 'nx_bundle_name' ) as $k ) {
			if ( isset( $values[ $k ] ) ) {
				$item[ $k ] = $values[ $k ];
			}
		}
		return $item;
	},
	10,
	2
);

// Show the kit name under each item.
add_filter(
	'woocommerce_get_item_data',
	function ( $data, $item ) {
		if ( ! empty( $item['nx_bundle_name'] ) ) {
			$data[] = array( 'key' => __( 'Kit', 'nexus-beauty' ), 'value' => $item['nx_bundle_name'] );
		}
		return $data;
	},
	10,
	2
);

// Bundle discount: X% off every bundle that still has at least the minimum number of products.
add_action(
	'woocommerce_cart_calculate_fees',
	function ( WC_Cart $cart ) {
		$pct = (int) nexus_opt( 'bundle_percent' );
		$min = max( 2, (int) nexus_opt( 'bundle_min' ) );
		if ( $pct <= 0 ) {
			return;
		}
		$groups = array();
		foreach ( $cart->get_cart() as $item ) {
			if ( empty( $item['nx_bundle'] ) ) {
				continue;
			}
			$g = $item['nx_bundle'];
			if ( ! isset( $groups[ $g ] ) ) {
				$groups[ $g ] = array( 'n' => 0, 'sub' => 0.0, 'name' => $item['nx_bundle_name'] );
			}
			$groups[ $g ]['n']++;
			// Discount one of each product in the bundle, not extra quantities added later.
			$groups[ $g ]['sub'] += (float) $item['line_subtotal'] / max( 1, (int) $item['quantity'] );
		}
		foreach ( $groups as $g ) {
			if ( $g['n'] >= $min && $g['sub'] > 0 ) {
				$label = $g['name']
					/* translators: %s: kit name */
					? sprintf( __( 'Bundle discount: %s', 'nexus-beauty' ), $g['name'] )
					: __( 'Bundle discount', 'nexus-beauty' );
				$cart->add_fee( $label, -round( $g['sub'] * $pct / 100, wc_get_price_decimals() ), false );
			}
		}
	}
);

// Free delivery bar on the cart page.
add_action(
	'woocommerce_before_cart',
	function () {
		echo nexus_free_ship_bar(); // phpcs:ignore WordPress.Security.EscapeOutput
	}
);

// Recently viewed on the cart page.
add_action(
	'woocommerce_after_cart',
	function () {
		if ( function_exists( 'nexus_recent_block' ) ) {
			echo nexus_recent_block( 8 ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}
);
