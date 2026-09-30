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
		'_nx_subtitle'    => array( __( 'Line under the title on the product page (optional)', 'nexus-beauty' ), 'text', __( 'Defaults to the tagline.', 'nexus-beauty' ) ),
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
		'_nx_facts'       => array( __( 'Quick facts (one per line: Label | Value)', 'nexus-beauty' ), 'textarea', __( 'e.g. Wear time | 3–4 hours on skin. Shown in the "Quick facts" box.', 'nexus-beauty' ) ),
		'_nx_notes'       => array( __( 'Fragrance notes (one per line: Top | Pistachio, pear)', 'nexus-beauty' ), 'textarea', '' ),
		'_nx_wear'        => array( __( 'Wear time bars (one per line: Skin | 45 | 3–4 h)', 'nexus-beauty' ), 'textarea', '' ),
		'_nx_group'       => array( __( 'Sizes group', 'nexus-beauty' ), 'text', __( 'Give sizes of the same product the same group name (e.g. axis-y-serum). They show as size buttons on each other\'s page.', 'nexus-beauty' ) ),
		'_nx_choice'      => array( __( 'Size button label', 'nexus-beauty' ), 'text', __( 'e.g. 100 ml. Defaults to the size shown on cards.', 'nexus-beauty' ) ),
		'_nx_choice_tag'  => array( __( 'Small tag on the size button', 'nexus-beauty' ), 'text', __( 'e.g. Most popular, Best value, Travel size', 'nexus-beauty' ) ),
		'_nx_scent_group' => array( __( 'Scents / shades group', 'nexus-beauty' ), 'text', __( 'Give scents or shades of one line the same group name (e.g. koh-e-noor-mists). They show as colour dots on each other\'s page.', 'nexus-beauty' ) ),
		'_nx_scent'       => array( __( 'Scent or shade name', 'nexus-beauty' ), 'text', __( 'e.g. Vanilla Pistachio', 'nexus-beauty' ) ),
		'_nx_fbt_title'   => array( __( 'Frequently bought together: heading', 'nexus-beauty' ), 'text', __( 'Default: Complete the routine', 'nexus-beauty' ) ),
		'_nx_fbt_text'    => array( __( 'Frequently bought together: intro', 'nexus-beauty' ), 'text', '' ),
		'_nx_shape'       => array( __( 'Illustration when there is no photo', 'nexus-beauty' ), 'shape', '' ),
		'_nx_tone'        => array( __( 'Illustration colour (hex)', 'nexus-beauty' ), 'text', __( 'e.g. #e2a33b. Leave empty to use the category colour.', 'nexus-beauty' ) ),
		'_nx_bg'          => array( __( 'Card background colour (hex)', 'nexus-beauty' ), 'text', __( 'e.g. #fbf1df', 'nexus-beauty' ) ),
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
				case 'shape':
					$args['options'] = array( '' => __( 'From the category', 'nexus-beauty' ) ) + array_combine( nexus_shapes(), array_map( 'ucfirst', nexus_shapes() ) );
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

/* ---------- Category and brand settings ---------- */

/**
 * Extra fields on product categories and brands.
 *
 * @param string $taxonomy Taxonomy.
 * @return array key => [ label, type, options ]
 */
function nexus_term_fields( $taxonomy ) {
	if ( 'product_cat' === $taxonomy ) {
		return array(
			'nx_shape' => array( __( 'Illustration', 'nexus-beauty' ), 'select', array( '' => __( 'Automatic', 'nexus-beauty' ) ) + array_combine( nexus_shapes(), array_map( 'ucfirst', nexus_shapes() ) ) ),
			'nx_tone'  => array( __( 'Illustration colour (hex)', 'nexus-beauty' ), 'text', array() ),
			'nx_tile'  => array( __( 'Tile background (hex)', 'nexus-beauty' ), 'text', array() ),
			'nx_short' => array( __( 'Short line under the name on the home page', 'nexus-beauty' ), 'text', array() ),
			'nx_note'  => array( __( 'Note shown on the home page tab', 'nexus-beauty' ), 'text', array() ),
		);
	}
	if ( 'product_brand' === $taxonomy ) {
		return array(
			'nx_origin' => array( __( 'Origin', 'nexus-beauty' ), 'select', array( '' => '—', 'local' => __( 'Pakistani', 'nexus-beauty' ), 'intl' => __( 'International', 'nexus-beauty' ) ) ),
			'nx_note'   => array( __( 'Short line on brand tiles (e.g. Pakistan · Derm)', 'nexus-beauty' ), 'text', array() ),
			'nx_derm'   => array( __( 'Show in the dermatologist brands section', 'nexus-beauty' ), 'select', array( '' => __( 'No', 'nexus-beauty' ), '1' => __( 'Yes', 'nexus-beauty' ) ) ),
		);
	}
	return array();
}

foreach ( array( 'product_cat', 'product_brand' ) as $nexus_tax ) {
	add_action(
		$nexus_tax . '_edit_form_fields',
		function ( $term, $taxonomy ) {
			foreach ( nexus_term_fields( $taxonomy ) as $key => $f ) {
				$val = get_term_meta( $term->term_id, $key, true );
				echo '<tr class="form-field"><th scope="row"><label for="' . esc_attr( $key ) . '">' . esc_html( $f[0] ) . '</label></th><td>';
				if ( 'select' === $f[1] ) {
					echo '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '">';
					foreach ( $f[2] as $v => $l ) {
						echo '<option value="' . esc_attr( $v ) . '"' . selected( (string) $val, (string) $v, false ) . '>' . esc_html( $l ) . '</option>';
					}
					echo '</select>';
				} else {
					echo '<input type="text" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '">';
				}
				echo '</td></tr>';
			}
			wp_nonce_field( 'nexus_term', 'nexus_term_nonce' );
		},
		20,
		2
	);
	add_action(
		'edited_' . $nexus_tax,
		function ( $term_id ) use ( $nexus_tax ) {
			if ( ! isset( $_POST['nexus_term_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nexus_term_nonce'] ) ), 'nexus_term' ) ) {
				return;
			}
			foreach ( array_keys( nexus_term_fields( $nexus_tax ) ) as $key ) {
				if ( isset( $_POST[ $key ] ) ) {
					update_term_meta( $term_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
				}
			}
		}
	);
}
