<?php
/**
 * Extra product fields: Products > Edit > "Nexus details" tab, plus batch/expiry/volume per variation.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

/**
 * All extra fields: key => [ label, type, description ].
 *
 * @return array
 */
function nexus_product_fields() {
	return array(
		'_nx_tagline'     => array( __( 'Tagline (one line under the product name)', 'nexus-beauty' ), 'text', __( 'e.g. "Fades dark spots and acne marks in 4–8 weeks"', 'nexus-beauty' ) ),
		'_nx_size'        => array( __( 'Size shown on cards', 'nexus-beauty' ), 'text', __( 'e.g. 50 ml', 'nexus-beauty' ) ),
		'_nx_volume'      => array( __( 'Amount for unit price (number only)', 'nexus-beauty' ), 'number', __( 'e.g. 50. Shows "Rs 81 per ml" next to the price.', 'nexus-beauty' ) ),
		'_nx_unit'        => array( __( 'Unit', 'nexus-beauty' ), 'select', '' ),
		'_nx_batch'       => array( __( 'Batch number', 'nexus-beauty' ), 'text', __( 'Printed on the box. Copied to every order line.', 'nexus-beauty' ) ),
		'_nx_mfg'         => array( __( 'Manufactured (MM/YYYY)', 'nexus-beauty' ), 'text', '' ),
		'_nx_expiry'      => array( __( 'Expiry (MM/YYYY)', 'nexus-beauty' ), 'text', '' ),
		'_nx_good_for'    => array( __( 'Good for (one per line)', 'nexus-beauty' ), 'textarea', '' ),
		'_nx_not_for'     => array( __( 'Choose something else if (one per line)', 'nexus-beauty' ), 'textarea', '' ),
		'_nx_ingredients' => array( __( 'Key ingredients (one per line: Name | Role | What it does)', 'nexus-beauty' ), 'textarea', __( 'e.g. Niacinamide 5% | Main active | Fades dark spots and controls oil', 'nexus-beauty' ) ),
		'_nx_inci'        => array( __( 'Full ingredient list', 'nexus-beauty' ), 'textarea', '' ),
		'_nx_howto'       => array( __( 'How to use (one step per line)', 'nexus-beauty' ), 'textarea', '' ),
		'_nx_faq'         => array( __( 'Questions (one per line: Question | Answer)', 'nexus-beauty' ), 'textarea', '' ),
		'_nx_video'       => array( __( 'YouTube video URL (review or how-to)', 'nexus-beauty' ), 'url', '' ),
	);
}

add_filter(
	'woocommerce_product_data_tabs',
	function ( $tabs ) {
		$tabs['nexus'] = array(
			'label'    => __( 'Nexus details', 'nexus-beauty' ),
			'target'   => 'nexus_product_data',
			'class'    => array(),
			'priority' => 65,
		);
		return $tabs;
	}
);

add_action(
	'woocommerce_product_data_panels',
	function () {
		global $post;
		echo '<div id="nexus_product_data" class="panel woocommerce_options_panel hidden"><div class="options_group">';
		foreach ( nexus_product_fields() as $key => $f ) {
			$args = array(
				'id'          => $key,
				'label'       => $f[0],
				'description' => $f[2],
				'desc_tip'    => (bool) $f[2],
				'value'       => get_post_meta( $post->ID, $key, true ),
			);
			switch ( $f[1] ) {
				case 'textarea':
					$args['rows'] = 4;
					woocommerce_wp_textarea_input( $args );
					break;
				case 'select':
					$args['options'] = array( 'ml' => 'ml', 'g' => 'g', 'pcs' => __( 'piece', 'nexus-beauty' ) );
					woocommerce_wp_select( $args );
					break;
				case 'number':
					$args['type']              = 'number';
					$args['custom_attributes'] = array( 'step' => 'any', 'min' => '0' );
					woocommerce_wp_text_input( $args );
					break;
				default:
					$args['type'] = 'url' === $f[1] ? 'url' : 'text';
					woocommerce_wp_text_input( $args );
			}
		}
		echo '</div></div>';
	}
);

add_action(
	'woocommerce_admin_process_product_object',
	function ( $product ) {
		// Nonce is checked by WooCommerce before this hook runs.
		foreach ( nexus_product_fields() as $key => $f ) {
			if ( ! isset( $_POST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				continue;
			}
			$raw = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			switch ( $f[1] ) {
				case 'textarea':
					$val = sanitize_textarea_field( $raw );
					break;
				case 'number':
					$val = '' === $raw ? '' : (string) max( 0, (float) $raw );
					break;
				case 'url':
					$val = esc_url_raw( $raw );
					break;
				default:
					$val = sanitize_text_field( $raw );
			}
			$product->update_meta_data( $key, $val );
		}
	}
);

/* ---------- Variation fields: batch, expiry, size and amount ---------- */

add_action(
	'woocommerce_product_after_variable_attributes',
	function ( $loop, $variation_data, $variation ) {
		$fields = array(
			'_nx_batch'  => __( 'Batch number', 'nexus-beauty' ),
			'_nx_expiry' => __( 'Expiry (MM/YYYY)', 'nexus-beauty' ),
			'_nx_size'   => __( 'Size shown to shoppers', 'nexus-beauty' ),
			'_nx_volume' => __( 'Amount for unit price', 'nexus-beauty' ),
		);
		foreach ( $fields as $key => $label ) {
			woocommerce_wp_text_input(
				array(
					'id'            => $key . '[' . $loop . ']',
					'name'          => $key . '[' . $loop . ']',
					'label'         => $label,
					'value'         => get_post_meta( $variation->ID, $key, true ),
					'wrapper_class' => 'form-row form-row-first',
				)
			);
		}
	},
	10,
	3
);

add_action(
	'woocommerce_save_product_variation',
	function ( $variation_id, $i ) {
		// WooCommerce verifies the nonce before saving variations (both the AJAX and the full product save).
		foreach ( array( '_nx_batch', '_nx_expiry', '_nx_size', '_nx_volume' ) as $key ) {
			if ( isset( $_POST[ $key ][ $i ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$val = sanitize_text_field( wp_unslash( $_POST[ $key ][ $i ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
				if ( '_nx_volume' === $key && '' !== $val ) {
					$val = (string) max( 0, (float) $val );
				}
				update_post_meta( $variation_id, $key, $val );
			}
		}
	},
	10,
	2
);

/**
 * Read a Nexus field from a product or variation, falling back to the parent product.
 *
 * @param WC_Product|int $product Product or ID.
 * @param string         $key     Meta key.
 * @return string
 */
function nexus_meta( $product, $key ) {
	$product = is_numeric( $product ) ? wc_get_product( $product ) : $product;
	if ( ! $product ) {
		return '';
	}
	$val = $product->get_meta( $key );
	if ( '' === $val && $product->get_parent_id() ) {
		$val = get_post_meta( $product->get_parent_id(), $key, true );
	}
	return is_string( $val ) ? trim( $val ) : (string) $val;
}

/**
 * Split a textarea into lines, and optionally each line into "|" parts.
 *
 * @param string $text  Text.
 * @param int    $parts Number of parts per line (0 = plain lines).
 * @return array
 */
function nexus_lines( $text, $parts = 0 ) {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$out[] = $parts ? array_pad( array_map( 'trim', explode( '|', $line, $parts ) ), $parts, '' ) : $line;
	}
	return $out;
}

/**
 * Brand name for a product: WooCommerce Brands taxonomy, then a "brand" attribute.
 *
 * @param WC_Product $product Product.
 * @return array [ name, url ]
 */
function nexus_brand( $product ) {
	$id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
	if ( taxonomy_exists( 'product_brand' ) ) {
		$terms = get_the_terms( $id, 'product_brand' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$link = get_term_link( $terms[0] );
			return array( $terms[0]->name, is_wp_error( $link ) ? '' : $link );
		}
	}
	$attr = $product->get_attribute( 'pa_brand' );
	if ( ! $attr ) {
		$attr = $product->get_attribute( 'brand' );
	}
	if ( ! $attr ) {
		$attr = (string) get_post_meta( $id, '_nx_brand', true );
	}
	return array( $attr, '' );
}
