<?php
/**
 * Block patterns: insert them from the block editor (+ > Patterns > Nexus Beauty).
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

// Full-width sections on the front page and the "Nexus full width" template.
add_filter(
	'body_class',
	function ( $classes ) {
		if ( is_front_page() || is_page_template( 'page-templates/template-full-width.php' ) ) {
			$classes[] = 'full-width-content';
			$classes[] = 'nx-full';
		}
		return $classes;
	}
);

/**
 * Pattern markup helpers.
 */
function nexus_p( $text, $class = '' ) {
	return $class
		? '<!-- wp:paragraph {"className":"' . $class . '"} --><p class="' . $class . '">' . $text . '</p><!-- /wp:paragraph -->'
		: '<!-- wp:paragraph --><p>' . $text . '</p><!-- /wp:paragraph -->';
}
function nexus_h( $text, $level = 2, $class = '' ) {
	$attrs = array();
	if ( 2 !== $level ) {
		$attrs['level'] = $level;
	}
	if ( $class ) {
		$attrs['className'] = $class;
	}
	$json = $attrs ? ' ' . wp_json_encode( $attrs ) : '';
	return '<!-- wp:heading' . $json . ' --><h' . $level . ' class="wp-block-heading' . ( $class ? ' ' . $class : '' ) . '">' . $text . '</h' . $level . '><!-- /wp:heading -->';
}
function nexus_sc( $code ) {
	return '<!-- wp:shortcode -->' . $code . '<!-- /wp:shortcode -->';
}
function nexus_group( $inner, $class ) {
	return '<!-- wp:group {"className":"' . $class . '","layout":{"type":"default"}} --><div class="wp-block-group ' . $class . '">' . $inner . '</div><!-- /wp:group -->';
}
function nexus_btn( $text, $url, $outline = false ) {
	return $outline
		? '<!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="' . $url . '">' . $text . '</a></div><!-- /wp:button -->'
		: '<!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . $url . '">' . $text . '</a></div><!-- /wp:button -->';
}
function nexus_sec_head( $eyebrow, $title, $lede = '' ) {
	return nexus_p( $eyebrow, 'nx-eyebrow' ) . nexus_h( $title, 2, 'nx-h2' ) . ( $lede ? nexus_p( $lede, 'nx-lede' ) : '' );
}

add_action(
	'init',
	function () {
		if ( ! function_exists( 'register_block_pattern' ) ) {
			return;
		}
		register_block_pattern_category( 'nexus-beauty', array( 'label' => __( 'Nexus Beauty', 'nexus-beauty' ) ) );
		$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

		$hero = nexus_group(
			'<!-- wp:columns {"verticalAlignment":"center","className":"nx-hero__cols"} --><div class="wp-block-columns are-vertically-aligned-center nx-hero__cols"><!-- wp:column {"verticalAlignment":"center"} --><div class="wp-block-column is-vertically-aligned-center">'
			. nexus_p( 'Beauty, checked.', 'nx-eyebrow' )
			. nexus_h( 'Good skin. <em>Good sense.</em>', 1, 'nx-hero__title' )
			. nexus_p( 'Original Pakistani derm heroes, Korean icons and the next wave of beauty, chosen for your routine, your budget and our climate. Every order shows the batch number and expiry date.', 'nx-lede' )
			. '<!-- wp:buttons --><div class="wp-block-buttons">' . nexus_btn( 'Shop best sellers', esc_url( $shop ) ) . nexus_btn( 'Find my routine', esc_url( home_url( '/routines/' ) ), true ) . '</div><!-- /wp:buttons -->'
			. nexus_p( '✓ Cash on delivery &nbsp; ✓ Delivered across Pakistan &nbsp; ✓ Minis from Rs 490', 'nx-hero__checks' )
			. '</div><!-- /wp:column --><!-- wp:column {"verticalAlignment":"center"} --><div class="wp-block-column is-vertically-aligned-center">'
			. '<!-- wp:html --><div class="nx-hero__art" aria-hidden="true"><div class="nx-hero__disc"></div><div class="nx-batch nx-batch--tilt"><strong>✓ Verified original</strong><span>BATCH <b>AX2609K</b></span><span>MFG <b>03/2026</b></span><span>EXP <b>02/2029</b></span></div></div><!-- /wp:html -->'
			. '</div><!-- /wp:column --></div><!-- /wp:columns -->',
			'nx-section nx-hero'
		);
		$trust = nexus_group( nexus_sc( '[nexus_trust]' ), 'nx-section nx-section--tight nx-trust-wrap' );
		$cats  = nexus_group( nexus_sec_head( 'Start here', 'Shop by category' ) . nexus_sc( '[nexus_categories limit="12"]' ), 'nx-section' );
		$tabs  = nexus_group( nexus_sec_head( 'Proven in Pakistan', 'Best sellers & new arrivals' ) . nexus_sc( '[nexus_product_tabs tabs="best,new,sale" limit="8" columns="4"]' ), 'nx-section nx-section--tint' );
		$kit   = nexus_group(
			nexus_sec_head( 'Shop by problem', 'Complete kits, with a guide to get results', 'Every product you need for one problem, in the order you use them. 15% off when you choose 3 or more.' )
			. nexus_sc( '[nexus_kit skus="NX-LOREAL-GLYCOLIC,NX-GARNIER-VITC,NX-AXIS-Y-5,NX-PONDS-GEL,NX-ESTELIN-70" tag="dark-spots,dullness" title="Complete Brightening Kit" who="For dull skin, dark spots and uneven tone." roles="Cleanse|Vitamin C serum|Dark spot serum|Moisturise|Sunscreen" how="Morning and night, 30 seconds|3–4 drops in the morning|2–3 drops at night|A pea-sized amount|Two finger-lengths, reapply outdoors" tips="Wear sunscreen every morning|Expect results in 4–8 weeks|Exfoliate at most twice a week|Patch test new products" avoid="Unlabelled whitening creams|Lemon or toothpaste on skin"]' ),
			'nx-section'
		);
		$steps = nexus_group(
			nexus_sec_head( 'Proof over promises', 'How we check every order' )
			. '<!-- wp:columns {"className":"nx-steps-cols"} --><div class="wp-block-columns nx-steps-cols">'
			. implode(
				'',
				array_map(
					function ( $s ) {
						return '<!-- wp:column --><div class="wp-block-column">' . nexus_p( $s[0], 'nx-step-n' ) . nexus_h( $s[1], 3 ) . nexus_p( $s[2] ) . '</div><!-- /wp:column -->';
					},
					array(
						array( 'Step 1', 'We buy from the source', 'Only from the brand or its authorised distributor, with an invoice for every batch.' ),
						array( 'Step 2', 'We check every batch', 'We record the batch number and expiry date before a product goes on sale.' ),
						array( 'Step 3', 'We print it on your order', 'Your parcel sticker lists the batch and expiry of every item.' ),
						array( 'Step 4', 'You pay when it arrives', 'Cash on delivery anywhere in Pakistan. Anything wrong? Full refund.' ),
					)
				)
			)
			. '</div><!-- /wp:columns -->',
			'nx-section nx-section--tint'
		);
		$brands = nexus_group( nexus_sec_head( 'Know what\'s inside', 'Pakistani favourites & global icons' ) . nexus_sc( '[nexus_brands limit="20"]' ), 'nx-section' );
		$faq_q  = array(
			array( 'Are your products original?', 'Yes. We buy only from brands and their authorised distributors, and every order shows the batch number and expiry date of each product.' ),
			array( 'Can I pay cash on delivery?', 'Yes, everywhere in Pakistan. You can also pay by card, JazzCash or Easypaisa.' ),
			array( 'How long does delivery take?', 'Orders placed before 3pm leave the same day. Karachi and Lahore: 1–2 working days. Other cities: 3–5 working days.' ),
			array( 'What is your return policy?', 'Unopened products can be returned within 7 days. Damaged or wrong items are always replaced or refunded.' ),
		);
		$faq    = nexus_group(
			nexus_sec_head( 'The reassuring bits', 'Questions, answered' )
			. implode(
				'',
				array_map(
					function ( $q ) {
						return '<!-- wp:details {"className":"nx-details"} --><details class="wp-block-details nx-details"><summary>' . $q[0] . '</summary>' . nexus_p( $q[1] ) . '</details><!-- /wp:details -->';
					},
					$faq_q
				)
			),
			'nx-section'
		);
		$cta = nexus_group(
			nexus_h( 'Not sure what suits your skin?', 2, 'nx-h2' )
			. nexus_p( 'Send us a message and a beauty advisor will suggest a routine. Free, 10am to 10pm, every day.' )
			. nexus_sc( '[nexus_whatsapp text="Chat with us on WhatsApp"]' ),
			'nx-section nx-cta'
		);

		$patterns = array(
			'home'      => array( __( 'Full home page', 'nexus-beauty' ), $hero . $trust . $cats . $tabs . $kit . $steps . $brands . $faq . $cta ),
			'hero'      => array( __( 'Hero with batch stamp', 'nexus-beauty' ), $hero ),
			'trust'     => array( __( 'Trust strip', 'nexus-beauty' ), $trust ),
			'categories'=> array( __( 'Shop by category', 'nexus-beauty' ), $cats ),
			'tabs'      => array( __( 'Product tabs', 'nexus-beauty' ), $tabs ),
			'kit'       => array( __( 'Problem kit with guide', 'nexus-beauty' ), $kit ),
			'steps'     => array( __( 'How we check every order', 'nexus-beauty' ), $steps ),
			'brands'    => array( __( 'Brands grid', 'nexus-beauty' ), $brands ),
			'faq'       => array( __( 'FAQ', 'nexus-beauty' ), $faq ),
			'cta'       => array( __( 'WhatsApp advice banner', 'nexus-beauty' ), $cta ),
		);
		foreach ( $patterns as $slug => $p ) {
			register_block_pattern(
				'nexus-beauty/' . $slug,
				array(
					'title'      => $p[0],
					'categories' => array( 'nexus-beauty' ),
					'content'    => $p[1],
				)
			);
		}
	}
);
