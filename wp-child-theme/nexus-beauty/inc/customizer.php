<?php
/**
 * Customizer: Appearance > Customize > Nexus Beauty.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'customize_register',
	function ( WP_Customize_Manager $wp ) {
		$d = nexus_defaults();

		$wp->add_panel( 'nexus', array( 'title' => __( 'Nexus Beauty', 'nexus-beauty' ), 'priority' => 25 ) );
		$sections = array(
			'nexus_store'    => __( 'Store & contact', 'nexus-beauty' ),
			'nexus_delivery' => __( 'Delivery & free shipping', 'nexus-beauty' ),
			'nexus_shop'     => __( 'Shop & products', 'nexus-beauty' ),
			'nexus_checkout' => __( 'Checkout', 'nexus-beauty' ),
			'nexus_look'     => __( 'Colours & fonts', 'nexus-beauty' ),
		);
		foreach ( $sections as $id => $title ) {
			$wp->add_section( $id, array( 'title' => $title, 'panel' => 'nexus' ) );
		}

		$add = function ( $key, $section, $label, $type = 'text', $sanitize = 'sanitize_text_field', $extra = array() ) use ( $wp, $d ) {
			$wp->add_setting(
				'nexus_' . $key,
				array(
					'default'           => $d[ $key ],
					'sanitize_callback' => $sanitize,
					'transport'         => 'refresh',
				)
			);
			if ( 'color' === $type ) {
				$wp->add_control( new WP_Customize_Color_Control( $wp, 'nexus_' . $key, array( 'label' => $label, 'section' => $section ) ) );
				return;
			}
			$wp->add_control( 'nexus_' . $key, array_merge( array( 'label' => $label, 'section' => $section, 'type' => $type ), $extra ) );
		};
		$bool = function ( $v ) {
			return (bool) $v;
		};

		// Store & contact.
		$add( 'announcements', 'nexus_store', __( 'Announcement bar (one message per line)', 'nexus-beauty' ), 'textarea', 'sanitize_textarea_field' );
		$add( 'whatsapp', 'nexus_store', __( 'WhatsApp number (international format, e.g. 923001234567)', 'nexus-beauty' ) );
		$add( 'support_email', 'nexus_store', __( 'Support email', 'nexus-beauty' ), 'email', 'sanitize_email' );
		$add( 'support_hours', 'nexus_store', __( 'Support hours', 'nexus-beauty' ) );
		$add( 'footer_about', 'nexus_store', __( 'Footer about text', 'nexus-beauty' ), 'textarea', 'sanitize_textarea_field' );
		$add( 'payment_badges', 'nexus_store', __( 'Payment badges (comma separated)', 'nexus-beauty' ) );
		$add( 'cookie_notice', 'nexus_store', __( 'Show cookie notice', 'nexus-beauty' ), 'checkbox', $bool );
		$add( 'cookie_text', 'nexus_store', __( 'Cookie notice text', 'nexus-beauty' ), 'textarea', 'sanitize_textarea_field' );

		// Delivery.
		$add( 'free_shipping', 'nexus_delivery', __( 'Free delivery threshold (store currency). Set the same amount in WooCommerce > Shipping > Free shipping.', 'nexus-beauty' ), 'number', 'absint' );
		$add( 'cities', 'nexus_delivery', __( 'Cities and delivery days, one per line: City|min-max', 'nexus-beauty' ), 'textarea', 'sanitize_textarea_field' );
		$add( 'default_days', 'nexus_delivery', __( 'Delivery days for other cities (min-max)', 'nexus-beauty' ) );
		$add( 'cutoff_hour', 'nexus_delivery', __( 'Same-day dispatch cut-off hour (0–23, store timezone)', 'nexus-beauty' ), 'number', 'absint', array( 'input_attrs' => array( 'min' => 0, 'max' => 23 ) ) );
		$add( 'sunday_off', 'nexus_delivery', __( 'No dispatch or delivery on Sundays', 'nexus-beauty' ), 'checkbox', $bool );

		// Shop.
		$add( 'bundle_percent', 'nexus_shop', __( 'Bundle and kit discount (%)', 'nexus-beauty' ), 'number', 'absint' );
		$add( 'bundle_min', 'nexus_shop', __( 'Products needed in a bundle for the discount', 'nexus-beauty' ), 'number', 'absint' );
		$add( 'new_days', 'nexus_shop', __( 'Show "New" badge for products added in the last X days', 'nexus-beauty' ), 'number', 'absint' );
		$add( 'low_stock', 'nexus_shop', __( 'Show "Only X left" when stock is at or below', 'nexus-beauty' ), 'number', 'absint' );
		$add( 'show_batch', 'nexus_shop', __( 'Show batch and expiry on product pages', 'nexus-beauty' ), 'checkbox', $bool );
		$add( 'wishlist_page', 'nexus_shop', __( 'Wishlist page (add the [nexus_wishlist] shortcode to it)', 'nexus-beauty' ), 'dropdown-pages', 'absint' );
		$add( 'track_page', 'nexus_shop', __( 'Order tracking page (add [woocommerce_order_tracking] to it)', 'nexus-beauty' ), 'dropdown-pages', 'absint' );

		// Checkout.
		$add( 'pk_phone_check', 'nexus_checkout', __( 'Check that phone numbers are Pakistani mobile numbers (for Pakistan addresses)', 'nexus-beauty' ), 'checkbox', $bool );
		$add( 'email_optional', 'nexus_checkout', __( 'Make email optional at checkout', 'nexus-beauty' ), 'checkbox', $bool );

		// Look.
		$add( 'accent', 'nexus_look', __( 'Accent colour (buttons, links)', 'nexus-beauty' ), 'color', 'sanitize_hex_color' );
		$add( 'ink', 'nexus_look', __( 'Text and dark colour', 'nexus-beauty' ), 'color', 'sanitize_hex_color' );
		$add( 'load_fonts', 'nexus_look', __( 'Load brand fonts from Google Fonts (turn off if you host fonts locally)', 'nexus-beauty' ), 'checkbox', $bool );
	}
);
