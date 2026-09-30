<?php
/**
 * Checkout: Pakistan-friendly fields, phone check, delivery estimate,
 * batch and expiry on order lines, and a helpful thank-you page.
 *
 * These hooks apply to the classic checkout ([woocommerce_checkout] shortcode).
 * See Appearance > Nexus setup if your checkout page uses the Checkout block.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

/**
 * Normalise a Pakistani mobile number to 03XXXXXXXXX. Returns '' if it isn't one.
 *
 * @param string $phone Raw phone.
 * @return string
 */
function nexus_pk_mobile( $phone ) {
	$d = preg_replace( '/\D/', '', (string) $phone );
	if ( 0 === strpos( $d, '0092' ) ) {
		$d = '0' . substr( $d, 4 );
	} elseif ( 0 === strpos( $d, '92' ) && 12 === strlen( $d ) ) {
		$d = '0' . substr( $d, 2 );
	}
	return preg_match( '/^03\d{9}$/', $d ) ? $d : '';
}

// Simpler fields: no company, no second address line; clearer labels and hints.
add_filter(
	'woocommerce_checkout_fields',
	function ( $fields ) {
		foreach ( array( 'billing', 'shipping' ) as $type ) {
			unset( $fields[ $type ][ $type . '_company' ], $fields[ $type ][ $type . '_address_2' ] );
			if ( isset( $fields[ $type ][ $type . '_address_1' ] ) ) {
				$fields[ $type ][ $type . '_address_1' ]['label']       = __( 'Full address', 'nexus-beauty' );
				$fields[ $type ][ $type . '_address_1' ]['placeholder'] = __( 'House number, street, area', 'nexus-beauty' );
			}
			if ( isset( $fields[ $type ][ $type . '_city' ] ) ) {
				$fields[ $type ][ $type . '_city' ]['custom_attributes'] = array( 'list' => 'nx-cities', 'autocomplete' => 'address-level2' );
			}
		}
		if ( isset( $fields['billing']['billing_phone'] ) ) {
			$fields['billing']['billing_phone']['label']       = __( 'Mobile number', 'nexus-beauty' );
			$fields['billing']['billing_phone']['placeholder'] = '0300 1234567';
			$fields['billing']['billing_phone']['required']    = true;
			$fields['billing']['billing_phone']['priority']    = 25;
		}
		if ( nexus_opt( 'email_optional' ) && isset( $fields['billing']['billing_email'] ) ) {
			$fields['billing']['billing_email']['required'] = false;
			$fields['billing']['billing_email']['label']    = __( 'Email (optional, for your receipt)', 'nexus-beauty' );
		}
		if ( isset( $fields['order']['order_comments'] ) ) {
			$fields['order']['order_comments']['placeholder'] = __( 'e.g. please call before arriving', 'nexus-beauty' );
		}
		return $fields;
	},
	20
);

// Pakistan doesn't use postcodes for delivery.
add_filter(
	'woocommerce_get_country_locale',
	function ( $locale ) {
		$locale['PK']['postcode'] = array( 'required' => false, 'hidden' => true );
		return $locale;
	}
);

// City suggestions for the city fields.
add_action(
	'woocommerce_after_checkout_billing_form',
	function () {
		echo '<datalist id="nx-cities">';
		foreach ( array_keys( nexus_cities() ) as $city ) {
			echo '<option value="' . esc_attr( $city ) . '"></option>';
		}
		echo '</datalist>';
	}
);

// Check Pakistani mobile numbers.
add_action(
	'woocommerce_after_checkout_validation',
	function ( $data, $errors ) {
		if ( ! nexus_opt( 'pk_phone_check' ) || empty( $data['billing_country'] ) || 'PK' !== $data['billing_country'] ) {
			return;
		}
		if ( ! empty( $data['billing_phone'] ) && ! nexus_pk_mobile( $data['billing_phone'] ) ) {
			$errors->add( 'billing_phone', __( 'Please enter a Pakistani mobile number, like 0300 1234567.', 'nexus-beauty' ), array( 'id' => 'billing_phone' ) );
		}
	},
	10,
	2
);

// Save the phone in one consistent format.
add_filter(
	'woocommerce_process_checkout_field_billing_phone',
	function ( $phone ) {
		$pk = nexus_pk_mobile( $phone );
		return $pk ? $pk : $phone;
	}
);

// Delivery estimate above the payment methods (updated by JavaScript as the city changes).
add_action(
	'woocommerce_review_order_before_payment',
	function () {
		echo '<p class="nx-co-eta" data-nx-checkout-eta></p>';
	}
);

// Copy batch, expiry and kit name to each order line (shows in admin, emails and invoices).
add_action(
	'woocommerce_checkout_create_order_line_item',
	function ( $item, $cart_item_key, $values ) {
		$product = isset( $values['data'] ) ? $values['data'] : null;
		if ( $product instanceof WC_Product ) {
			$batch = nexus_meta( $product, '_nx_batch' );
			$exp   = nexus_meta( $product, '_nx_expiry' );
			if ( $batch ) {
				$item->add_meta_data( __( 'Batch', 'nexus-beauty' ), $batch, true );
			}
			if ( $exp ) {
				$item->add_meta_data( __( 'Expiry', 'nexus-beauty' ), $exp, true );
			}
		}
		if ( ! empty( $values['nx_bundle_name'] ) ) {
			$item->add_meta_data( __( 'Kit', 'nexus-beauty' ), $values['nx_bundle_name'], true );
		}
	},
	10,
	3
);

// Thank-you page: delivery estimate and what happens next.
add_action(
	'woocommerce_thankyou',
	function ( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}
		$city = $order->get_shipping_city() ? $order->get_shipping_city() : $order->get_billing_city();
		$track = (int) nexus_opt( 'track_page' );
		?>
		<section class="nx-thanks">
			<h2 class="nx-h2"><?php esc_html_e( 'What happens next', 'nexus-beauty' ); ?></h2>
			<ol class="nx-track">
				<li class="is-done"><span class="nx-track__dot">✓</span><span><b><?php esc_html_e( 'Order confirmed', 'nexus-beauty' ); ?></b><?php esc_html_e( 'We\'ll message you on WhatsApp to confirm.', 'nexus-beauty' ); ?></span></li>
				<li class="is-now"><span class="nx-track__dot">2</span><span><b><?php esc_html_e( 'Checked and packed', 'nexus-beauty' ); ?></b><?php esc_html_e( 'Every item\'s batch and expiry goes on your parcel sticker.', 'nexus-beauty' ); ?></span></li>
				<li><span class="nx-track__dot">3</span><span><b><?php esc_html_e( 'On its way', 'nexus-beauty' ); ?></b><?php /* translators: %s: date range */ printf( esc_html__( 'Expected %s.', 'nexus-beauty' ), '<b>' . esc_html( nexus_eta_text( $city ) ) . '</b>' ); ?></span></li>
				<li><span class="nx-track__dot">4</span><span><b><?php esc_html_e( 'Delivered', 'nexus-beauty' ); ?></b><?php esc_html_e( 'Check the batch and expiry against the sticker before you pay the rider.', 'nexus-beauty' ); ?></span></li>
			</ol>
			<p class="nx-thanks__links">
				<?php if ( $track ) : ?><a class="nx-btn nx-btn--primary" href="<?php echo esc_url( get_permalink( $track ) ); ?>"><?php esc_html_e( 'Track this order', 'nexus-beauty' ); ?></a><?php endif; ?>
				<?php if ( nexus_opt( 'whatsapp' ) ) : ?><a class="nx-btn nx-btn--ghost" href="<?php echo esc_url( nexus_whatsapp_url( sprintf( /* translators: %s: order number */ __( 'Hi, I have a question about order #%s', 'nexus-beauty' ), $order->get_order_number() ) ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Questions? WhatsApp us', 'nexus-beauty' ); ?></a><?php endif; ?>
			</p>
		</section>
		<?php
	},
	5
);
