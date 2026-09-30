<?php
/**
 * Content for the pages created by the automatic setup. Written in block markup so every page
 * is fully editable in the block editor afterwards.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

/**
 * List block (markup that is valid on every WordPress version from 6.4).
 *
 * @param array $items Items.
 * @param bool  $ordered Ordered list.
 * @return string
 */
function nexus_list( $items, $ordered = false ) {
	$tag  = $ordered ? 'ol' : 'ul';
	$attr = $ordered ? ' {"ordered":true}' : '';
	$li   = '';
	foreach ( $items as $i ) {
		$li .= '<!-- wp:list-item --><li>' . $i . '</li><!-- /wp:list-item -->';
	}
	return '<!-- wp:list' . $attr . ' --><' . $tag . '>' . $li . '</' . $tag . '><!-- /wp:list -->';
}

/**
 * FAQ details blocks.
 *
 * @param array $qa [ [question, answer], ... ].
 * @return string
 */
function nexus_details( $qa ) {
	$out = '';
	foreach ( $qa as $q ) {
		$out .= '<!-- wp:details {"className":"nx-details"} --><details class="wp-block-details nx-details"><summary>' . $q[0] . '</summary>' . nexus_p( $q[1] ) . '</details><!-- /wp:details -->';
	}
	return $out;
}

/**
 * A registered pattern's content.
 *
 * @param string $slug Pattern slug without prefix.
 * @return string
 */
function nexus_pattern( $slug ) {
	$p = WP_Block_Patterns_Registry::get_instance()->get_registered( 'nexus-beauty/' . $slug );
	return $p ? $p['content'] : '';
}

function nexus_content_home() {
	// The home page is drawn by front-page.php from the store (categories, products, brands).
	return '';
}

function nexus_content_routines() {
	$kits = array(
		array( 'Complete Brightening Kit', 'For dull skin, dark spots, acne marks and uneven tone.', 'NX-LOREAL-GLYCOLIC,NX-GARNIER-VITC,NX-AXIS-Y-5,NX-PONDS-GEL,NX-ESTELIN-70,NX-ST-IVES-APRICOT,NX-DOVE-BODY-POLISH,NX-VASELINE-GLUTA', 'dark-spots,dullness', 'Cleanse, morning and night|Vitamin C serum, morning|Dark spot serum, night|Moisturise, morning and night|Sunscreen, every morning|Face scrub, once or twice a week|Body polish, twice a week|Body lotion, daily', 'Massage for 30 seconds, rinse with lukewarm water|3–4 drops on dry skin before moisturiser|2–3 drops on clean skin. Start with the mini|A pea-sized amount|Two finger-lengths for face and neck. Reapply every 2–3 hours outdoors|Gently, on damp skin. Skip the night you use the serum|In the shower on elbows, knees and legs|After every shower, on slightly damp skin', 'Wear sunscreen every single morning. Without it, spots come back|Expect lighter marks in 4–8 weeks, not days|Exfoliate at most twice a week, never on the same night as your dark spot serum|Patch test each new product on your jaw for 2 days|Add one new product at a time, one week apart', 'Whitening or fairness creams without a full ingredient list. Some contain mercury or steroids|Lemon, toothpaste or baking soda on your face|Scrubbing broken or sunburnt skin' ),
		array( 'Clear Skin Kit', 'For oily skin, breakouts, bumps and the red marks acne leaves behind.', 'NX-REDERM-CLARIDERM,NX-ANUA-AZELAIC,NX-PONDS-GEL,NX-NEOBRELLA-60', 'acne', 'Cleanse, morning and night|Treat, at night|Moisturise, morning and night|Sunscreen, every morning', 'Twice a day only. Over-washing makes skin oilier|A thin layer on clean skin. Calms redness and bumps|Yes, oily skin needs moisture too|Every morning. Acne marks darken in the sun', 'Give it 6–8 weeks before judging results|Change your pillow cover twice a week|Keep hands, phone and hair oil away from your face|See a dermatologist for painful, deep or scarring acne', 'Popping or squeezing spots|Using several acne treatments at once|Heavy oils and thick creams on the face' ),
		array( 'Hair Fall Kit', 'For thinning hair, hair fall after washing and dandruff.', 'NX-REDERM-TRESSFIX,NX-JENPHARM-ANAGROW,NX-MIELLE-ROSEMARY,NX-FINO-MASK', 'hair-fall', 'Anti-dandruff shampoo, 3 times a week|Biotin shampoo, other wash days|Strengthening masque, once a week|Repair mask, once a week', 'Leave the lather on your scalp for 2 minutes, then rinse|Massage into the scalp with your fingertips|On lengths and ends for 10–15 minutes|Alternate with the masque on dry, damaged hair', 'Use it consistently for 8–12 weeks. Hair grows slowly|Wash your scalp regularly|Loosen tight ponytails and buns|If hair falls in patches or suddenly, see a doctor and check iron, vitamin D and thyroid', 'Hot water on the scalp|Brushing wet hair hard. Use a wide-tooth comb|Leaving hair oil in for days' ),
		array( 'Dry & Sensitive Skin Kit', 'For tight, flaky or easily irritated skin, especially in winter.', 'NX-CETAPHIL-GENTLE,NX-MEDICUBE-PDRN,NX-DR-ALTHEA-345,NX-BOJ-RELIEF-SUN', 'dryness,sensitive-skin', 'Gentle cleanser, morning and night|Hydrating serum, morning|Barrier cream, morning and night|Sunscreen, every morning', 'Soap-free. Pat dry, don\'t rub|On slightly damp skin to hold in water|A generous layer, especially at night|A creamy SPF 50+ that doesn\'t dry skin out', 'Apply moisturiser within a minute of washing|Use lukewarm water, never hot|Keep your routine short: 3–4 products', 'Foaming, deep cleansing face washes|Scrubs while your skin is flaking|Products with strong fragrance or alcohol' ),
	);
	$out = nexus_group( nexus_sec_head( 'Shop by problem', 'Routines that work in Pakistan', 'Pick your problem. Each kit lists every product you need in the order you use them, how to apply each one, and what to avoid. Choose 3 or more products and get 15% off.' ), 'nx-section nx-section--tint' );
	foreach ( $kits as $k ) {
		$sc   = sprintf(
			'[nexus_kit skus="%s" tag="%s" title="%s" who="%s" roles="%s" how="%s" tips="%s" avoid="%s"]',
			$k[2],
			$k[3],
			esc_attr( $k[0] ),
			esc_attr( $k[1] ),
			esc_attr( $k[4] ),
			esc_attr( $k[5] ),
			esc_attr( $k[6] ),
			esc_attr( $k[7] )
		);
		$out .= nexus_group( nexus_sc( $sc ), 'nx-section' );
	}
	return $out;
}

function nexus_content_collections() {
	return nexus_p( 'Every way to shop ' . esc_html( get_bloginfo( 'name' ) ) . ', from sunscreen to Pakistani derm brands.', 'nx-lede' ) . nexus_sc( '[nexus_categories limit="50"]' );
}

function nexus_content_brands() {
	return nexus_p( 'Every brand we stock, bought only from the brand or its authorised distributor.', 'nx-lede' ) . nexus_sc( '[nexus_brands limit="200"]' );
}

function nexus_content_offers() {
	return nexus_p( 'Real offers, no fake countdowns.', 'nx-lede' )
		. nexus_h( 'Current offers', 2 )
		. nexus_list( array( '<strong>Routine kits:</strong> 15% off when you choose 3 or more products from any kit.', '<strong>Free delivery</strong> on orders over Rs 5,000, anywhere in Pakistan.', '<strong>Cash on delivery</strong> on every order.' ) )
		. nexus_h( 'On offer now', 2 ) . nexus_sc( '[products on_sale="true" limit="12" columns="4"]' )
		. nexus_h( 'Best sellers', 2 ) . nexus_sc( '[products best_selling="true" limit="8" columns="4"]' );
}

function nexus_content_about() {
	return nexus_p( 'We started ' . esc_html( get_bloginfo( 'name' ) ) . ' because buying skincare online in Pakistan shouldn\'t feel like a gamble.', 'nx-lede' )
		. nexus_h( 'Our story', 2 )
		. nexus_p( 'Fake and expired products are the biggest worry for anyone buying beauty online in Pakistan. Big stores have huge ranges but show no proof. Small shops promise "100% original" but can\'t show where their stock came from.' )
		. nexus_p( esc_html( get_bloginfo( 'name' ) ) . ' is the meeting point of Pakistan\'s trusted dermatologist brands, the best Korean and international skincare, and our own Koh-e-Noor fragrance label. Every product is checked before it reaches you.' )
		. nexus_h( 'Our promise: Beauty, checked.', 2 )
		. nexus_list( array( 'We buy only from brands and their authorised distributors, and keep the invoice for every batch.', 'We print the batch number and expiry date of every item on your parcel sticker.', 'We never sell "first copy", grey-market or unlabelled whitening creams.', 'We only publish reviews from verified orders, and never edit or hide them.' ) )
		. nexus_pattern( 'steps' ) . nexus_pattern( 'cta' );
}

function nexus_content_contact() {
	return nexus_p( 'Real people, 10am to 10pm, every day. We usually reply within 2 hours.', 'nx-lede' )
		. '<!-- wp:columns --><div class="wp-block-columns"><!-- wp:column {"width":"60%"} --><div class="wp-block-column" style="flex-basis:60%">'
		. nexus_h( 'Send us a message', 2 ) . nexus_sc( '[nexus_contact_form]' )
		. '</div><!-- /wp:column --><!-- wp:column {"width":"40%"} --><div class="wp-block-column" style="flex-basis:40%">'
		. nexus_h( 'WhatsApp', 3 ) . nexus_p( 'Fastest for order questions and skin advice.' ) . nexus_sc( '[nexus_whatsapp text="Chat on WhatsApp"]' )
		. nexus_h( 'Email', 3 ) . nexus_p( esc_html( nexus_opt( 'support_email' ) ) )
		. nexus_h( 'Hours', 3 ) . nexus_p( 'Every day, 10am to 10pm. Orders placed before 3pm ship the same day, except Sunday.' )
		. '</div><!-- /wp:column --></div><!-- /wp:columns -->';
}

function nexus_content_authenticity() {
	return nexus_p( 'Fake skincare can contain mercury, steroids or bacteria. Here\'s exactly how we make sure yours is original and fresh.', 'nx-lede' )
		. nexus_h( 'Where we buy', 2 ) . nexus_p( 'Only from the brand itself or its authorised distributor in Pakistan. We keep the purchase invoice for every batch. We never buy from wholesale markets, surplus sellers or individuals.' )
		. nexus_h( 'What we check before a product goes on sale', 2 ) . nexus_list( array( 'The batch number and expiry date, which we record.', 'Sealed packaging, correct labels and an intact barcode.', 'At least 6 months of shelf life left.', 'Storage away from heat and sunlight.' ), true )
		. nexus_h( 'Check it yourself', 2 ) . nexus_p( 'Every parcel sticker lists each item with its batch number and expiry date. Before you pay the rider, match them with the box and check the seal is intact.' )
		. nexus_h( 'Signs of a fake product anywhere', 2 ) . nexus_list( array( 'A price far below every other store.', 'Missing batch number or expiry date, or a sticker covering them.', 'Spelling mistakes, blurry print or a different colour from the brand\'s photos.', 'Unusual smell, texture or colour.', '"Whitening" creams with no ingredient list.' ) )
		. nexus_h( 'Something looks wrong?', 2 ) . nexus_p( 'Send a photo of the product and the parcel sticker on WhatsApp. If we can\'t prove it\'s original, we refund you in full, including delivery.' );
}

function nexus_content_delivery() {
	return nexus_p( 'We deliver to every city in Pakistan. Orders placed before 3pm leave the same day.', 'nx-lede' )
		. nexus_h( 'Delivery times by city', 2 ) . nexus_sc( '[nexus_delivery_table]' )
		. nexus_h( 'How it works', 2 ) . nexus_list( array( 'You get an SMS and WhatsApp confirmation with your order number.', 'We check the batch and expiry of every item and seal your parcel.', 'Our courier partner delivers it and calls before arriving.', 'You check the parcel sticker, then pay the rider if you chose cash on delivery.' ), true )
		. nexus_h( 'Cash on delivery', 2 ) . nexus_list( array( 'Available everywhere we deliver.', 'Please keep the exact amount ready if you can.', 'You can check the outer parcel and sticker before paying.' ) );
}

function nexus_content_returns() {
	return nexus_p( '7-day returns on unopened products. Damaged, wrong or doubtful items are always on us.', 'nx-lede' )
		. nexus_h( 'The policy', 2 ) . nexus_list( array( '<strong>Change of mind:</strong> return unopened, unused products in original packaging within 7 days of delivery.', '<strong>Damaged, leaking or wrong item:</strong> tell us within 48 hours with a photo. We replace or refund it free.', '<strong>Authenticity concern:</strong> any time before the expiry date. If we can\'t prove it\'s original, you get a full refund.', '<strong>Not returnable once opened:</strong> skincare, makeup, fragrance and personal care, unless faulty.' ) )
		. nexus_h( 'How to return', 2 ) . nexus_list( array( 'WhatsApp or email us your order number and a photo of the item.', 'We confirm within 2 hours and book a pickup, or give you our return address.', 'Pack the item in its original box.' ), true )
		. nexus_h( 'Refunds', 2 ) . nexus_p( 'We refund within 2 working days of receiving your return, to your bank account, JazzCash or Easypaisa. Card payments go back to the same card.' );
}

function nexus_content_faq() {
	return nexus_h( 'Orders and payment', 2 ) . nexus_details( array( array( 'How do I place an order?', 'Add products to your bag and check out. You don\'t need an account.' ), array( 'Which payment methods do you accept?', 'Cash on delivery, plus any online payment methods shown at checkout.' ), array( 'Can I change or cancel my order?', 'Yes, until it\'s packed. WhatsApp us with your order number as soon as possible.' ) ) )
		. nexus_h( 'Delivery', 2 ) . nexus_details( array( array( 'How long does delivery take?', '1–2 working days to Karachi and Lahore, 2–3 to Islamabad and Rawalpindi, 3–5 to other cities.' ), array( 'How much is delivery?', 'Delivery is free on orders over Rs 5,000. The charge for smaller orders is shown at checkout.' ) ) )
		. nexus_h( 'Products and authenticity', 2 ) . nexus_details( array( array( 'Are your products original?', 'Yes. We buy only from brands and authorised distributors, and print each item\'s batch and expiry on your parcel sticker.' ), array( 'Do you sell whitening creams?', 'No. We sell brightening products with full ingredient lists, and never unlabelled whitening or fairness creams.' ), array( 'Can you help me choose?', 'Yes. WhatsApp us for free advice, or see our routines.' ) ) )
		. nexus_h( 'Returns', 2 ) . nexus_details( array( array( 'What is your return policy?', 'Unopened products within 7 days. Damaged, wrong or faulty items are always replaced or refunded.' ), array( 'How fast are refunds?', 'Within 2 working days of receiving your return.' ) ) );
}

function nexus_content_terms() {
	return nexus_p( 'Last updated: ' . wp_date( 'j F Y' ) . '. Please have these terms reviewed by a lawyer before relying on them.', 'nx-note' )
		. nexus_h( 'Orders', 2 ) . nexus_p( 'Your order is confirmed when you receive our confirmation. We may cancel an order if an item is out of stock, the price was shown wrongly, or we can\'t verify the delivery details. If you paid in advance, we refund you in full.' )
		. nexus_h( 'Prices and payment', 2 ) . nexus_p( 'Prices are in Pakistani rupees and include taxes. Delivery charges are shown at checkout. Discount codes can\'t be combined unless stated.' )
		. nexus_h( 'Delivery and returns', 2 ) . nexus_p( 'Delivery times are estimates. See our delivery information and returns policy pages.' )
		. nexus_h( 'Product information', 2 ) . nexus_p( 'We show product details as provided by the brand. Always read the label, patch test new products and follow the directions. Our routines and guides are general information, not medical advice.' )
		. nexus_h( 'Governing law', 2 ) . nexus_p( 'These terms are governed by the laws of Pakistan.' );
}

function nexus_content_privacy() {
	return nexus_p( 'Last updated: ' . wp_date( 'j F Y' ) . '. Please have this policy reviewed by a lawyer before relying on it.', 'nx-note' )
		. nexus_h( 'What we collect', 2 ) . nexus_list( array( 'Order details: name, mobile number, delivery address, optional email and what you bought.', 'We don\'t store card numbers. Online payments are handled by the payment provider.', 'Your wishlist and recently viewed products are saved in your browser.', 'Analytics cookies only if you choose "Accept all" in the cookie notice.' ) )
		. nexus_h( 'How we use it', 2 ) . nexus_p( 'To deliver your order, send order updates by SMS and WhatsApp, handle returns and questions, and send offers only if you sign up.' )
		. nexus_h( 'Who we share it with', 2 ) . nexus_p( 'Only the courier delivering your parcel, the payment provider you choose, and our messaging services. We never sell your data.' )
		. nexus_h( 'Your choices', 2 ) . nexus_p( 'Ask us to see, correct or delete your data at any time by emailing ' . esc_html( nexus_opt( 'support_email' ) ) . '.' );
}

function nexus_content_wishlist() {
	return nexus_sc( '[nexus_wishlist]' ) . nexus_sc( '[nexus_recently_viewed limit="8"]' );
}

function nexus_content_track() {
	return nexus_p( 'Enter your order number and the email you used at checkout.', 'nx-lede' ) . nexus_sc( '[woocommerce_order_tracking]' );
}
