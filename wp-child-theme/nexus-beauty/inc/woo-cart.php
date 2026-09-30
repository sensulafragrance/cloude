<?php
/**
 * Side bag (the design's .drawer), live quantities, add to bag without reloading,
 * free delivery progress and bundle discounts.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

/**
 * Free delivery progress bar (design: .ship-bar). Refreshed as a cart fragment.
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
	$pct   = min( 100, ( $total / $goal ) * 100 );
	if ( $total <= 0 ) {
		/* translators: %s: amount */
		$text = sprintf( esc_html__( 'Free delivery over %s', 'nexus-beauty' ), esc_html( nexus_money( $goal ) ) );
	} elseif ( $left > 0 ) {
		/* translators: %s: amount left */
		$text = sprintf( __( 'You\'re %s away from <b>free delivery</b>', 'nexus-beauty' ), '<b>' . esc_html( nexus_money( $left ) ) . '</b>' );
	} else {
		$text = '<b>' . esc_html__( 'You\'ve unlocked free delivery.', 'nexus-beauty' ) . '</b>';
	}
	return '<div class="ship-bar" data-nx-ship><span data-ship-text>' . wp_kses( $text, array( 'b' => array() ) ) . '</span><div class="ship-bar__track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' . esc_attr( round( $pct ) ) . '" aria-label="' . esc_attr__( 'Progress to free delivery', 'nexus-beauty' ) . '"><div class="ship-bar__fill" style="width:' . esc_attr( round( $pct, 1 ) ) . '%"></div></div></div>';
}

/**
 * Drawer contents: delivery bar, lines and footer. Also used as a cart fragment.
 */
function nexus_drawer_body() {
	$cart = WC()->cart;
	echo '<div class="drawer__body">';
	$bar = $cart ? nexus_free_ship_bar() : '';
	echo $bar ? $bar : '<div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in nexus_free_ship_bar().
	echo '<div class="drawer__items">';
	if ( ! $cart || $cart->is_empty() ) {
		echo '<div class="drawer__empty"><p>' . esc_html__( 'Your bag is empty.', 'nexus-beauty' ) . '</p><a class="btn btn--dark" href="' . esc_url( nexus_shop_url() ) . '">' . esc_html__( 'Shop bestsellers', 'nexus-beauty' ) . '</a></div>';
	} else {
		foreach ( $cart->get_cart() as $key => $item ) {
			$product = apply_filters( 'woocommerce_cart_item_product', $item['data'], $item, $key );
			if ( ! $product || ! $product->exists() || $item['quantity'] <= 0 || ! apply_filters( 'woocommerce_widget_cart_item_visible', true, $item, $key ) ) {
				continue;
			}
			$look  = nexus_product_look( $product );
			$size  = nexus_meta( $product, '_nx_size' );
			$max   = $product->get_max_purchase_quantity();
			$link  = $product->is_visible() ? $product->get_permalink( $item ) : '';
			$data  = wc_get_formatted_cart_item_data( $item, true );
			$name  = apply_filters( 'woocommerce_cart_item_name', $product->get_name(), $item, $key );
			?>
			<div class="line" data-key="<?php echo esc_attr( $key ); ?>">
				<div class="line__img" style="<?php echo esc_attr( nexus_look_style( $look ) ); ?>"><?php echo nexus_product_visual( $product, 'woocommerce_gallery_thumbnail', array( 'loading' => 'lazy' ) ); // phpcs:ignore ?></div>
				<div>
					<b><?php echo $link ? '<a href="' . esc_url( $link ) . '">' . wp_kses_post( $name ) . '</a>' : wp_kses_post( $name ); ?></b>
					<?php if ( $size && ! $product->is_type( 'variation' ) ) : ?><small><?php echo esc_html( $size ); ?></small><?php endif; ?>
					<?php if ( $data ) : ?><small class="nx-meta"><?php echo wp_kses_post( str_replace( "\n", ' · ', trim( $data ) ) ); ?></small><?php endif; ?>
					<div class="line__qty">
						<button type="button" data-nx-qty="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'nexus-beauty' ); ?>">−</button><span><?php echo (int) $item['quantity']; ?></span><button type="button" data-nx-qty="1" aria-label="<?php esc_attr_e( 'Increase quantity', 'nexus-beauty' ); ?>"<?php disabled( $max > 0 && $item['quantity'] >= $max ); ?>>+</button>
						<a class="line__remove" href="<?php echo esc_url( wc_get_cart_remove_url( $key ) ); ?>" data-nx-remove><?php esc_html_e( 'Remove', 'nexus-beauty' ); ?></a>
					</div>
				</div>
				<span class="line__price"><?php echo wp_kses_post( $cart->get_product_subtotal( $product, $item['quantity'] ) ); ?></span>
			</div>
			<?php
		}
	}
	echo '</div>';
	if ( $cart && ! $cart->is_empty() ) {
		$total = (float) $cart->get_displayed_subtotal();
		echo '<div class="drawer__foot">';
		foreach ( $cart->get_fees() as $fee ) {
			$total += (float) $fee->total;
			echo '<div class="drawer__fee"><span>' . esc_html( $fee->name ) . '</span><span>' . esc_html( nexus_money( $fee->total ) ) . '</span></div>';
		}
		echo '<div class="drawer__total"><span>' . esc_html__( 'Subtotal', 'nexus-beauty' ) . '</span><span data-cart-total>' . esc_html( nexus_money( $total ) ) . '</span></div>';
		echo '<a class="btn btn--primary btn--lg btn--block" href="' . esc_url( wc_get_checkout_url() ) . '">' . esc_html__( 'Checkout securely', 'nexus-beauty' ) . '</a>';
		echo '<small>' . esc_html__( 'Cash on delivery, cards, JazzCash and Easypaisa accepted.', 'nexus-beauty' ) . ' <a href="' . esc_url( wc_get_cart_url() ) . '" style="text-decoration:underline">' . esc_html__( 'View bag', 'nexus-beauty' ) . '</a></small>';
		echo '</div>';
	} else {
		echo '<div></div>';
	}
	echo '</div>';
}

add_filter(
	'woocommerce_add_to_cart_fragments',
	function ( $fragments ) {
		ob_start();
		nexus_drawer_body();
		$fragments['div.drawer__body']                  = ob_get_clean();
		$fragments['span.badge-count[data-cart-count]'] = nexus_count_badge( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 );
		$bar                                            = nexus_free_ship_bar();
		if ( $bar ) {
			$fragments['.buybox div.ship-bar[data-nx-ship], .page-body div.ship-bar[data-nx-ship]'] = $bar;
		}
		return $fragments;
	}
);

/**
 * AJAX: change a line's quantity, then return fresh fragments.
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
 * AJAX: add to bag from the product page form (simple and variable products), without reloading.
 */
function nexus_ajax_add_to_cart() {
	check_ajax_referer( 'nexus', 'nonce' );
	// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$product_id   = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
	$quantity     = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : 1;
	$variation_id = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
	$variation    = array();
	foreach ( $_POST as $k => $v ) {
		if ( 0 === strpos( (string) $k, 'attribute_' ) ) {
			$variation[ sanitize_title( wp_unslash( $k ) ) ] = wc_clean( wp_unslash( $v ) );
		}
	}
	// phpcs:enable
	$product = wc_get_product( $variation_id ? $variation_id : $product_id );
	if ( ! $product || $quantity <= 0 ) {
		wp_send_json_error( array( 'message' => __( 'Please choose an option first.', 'nexus-beauty' ) ), 400 );
	}
	$passed = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation );
	if ( $passed && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) ) {
		do_action( 'woocommerce_ajax_added_to_cart', $product_id );
		wc_clear_notices();
		WC_AJAX::get_refreshed_fragments();
	}
	$errors = wc_get_notices( 'error' );
	wc_clear_notices();
	$message = $errors ? wp_strip_all_tags( is_array( $errors[0] ) ? $errors[0]['notice'] : $errors[0] ) : __( 'This product could not be added. Please try again.', 'nexus-beauty' );
	wp_send_json_error( array( 'message' => $message ), 400 );
}
add_action( 'wp_ajax_nexus_add_to_cart', 'nexus_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_nexus_add_to_cart', 'nexus_ajax_add_to_cart' );

/**
 * AJAX: add several products at once as a bundle (frequently bought together, kits).
 */
function nexus_ajax_add_bundle() {
	check_ajax_referer( 'nexus', 'nonce' );
	$ids  = isset( $_POST['ids'] ) ? array_slice( array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_POST['ids'] ) ) ) ) ), 0, 12 ) : array();
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
	wc_clear_notices();
	if ( ! $added ) {
		wp_send_json_error( array( 'message' => __( 'These products are not available right now.', 'nexus-beauty' ) ), 400 );
	}
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
			echo nexus_recent_block( 4 ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
	}
);
