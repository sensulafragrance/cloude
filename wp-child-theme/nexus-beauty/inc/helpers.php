<?php
/**
 * Helpers: settings, icons, illustrations and small utilities used across the theme.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

/**
 * Default values for every Customizer setting.
 *
 * @return array
 */
function nexus_defaults() {
	return array(
		// Store & contact.
		'announcements'   => '',
		'whatsapp'        => '923000000000',
		'phone'           => '+92 21 0000 0000',
		'support_email'   => 'care@nexusbeauty.pk',
		'support_hours'   => 'WhatsApp advice 10am to 10pm, every day',
		'footer_about'    => 'A Pakistani multi-brand beauty store. Local derm brands, Korean favourites and our own Koh-e-Noor fragrances, with the batch and expiry shown on every order.',
		'payment_badges'  => 'COD, VISA, MASTERCARD, JAZZCASH, EASYPAISA',
		'popular'         => 'Sunscreen | Hair fall shampoo | Axis-Y | PDRN | Body mist | Rosemary | Minis under Rs 1,000 | Niacinamide',
		'cookie_notice'   => true,
		'cookie_text'     => 'We use a few cookies to keep your bag and wishlist saved and to understand what shoppers like.',
		'whatsapp_button' => true,
		// Delivery.
		'free_shipping'   => 5000,
		'cities'          => "Karachi|1-2\nLahore|1-2\nIslamabad|1-2\nRawalpindi|2-3\nFaisalabad|3-5\nMultan|3-5\nPeshawar|3-5\nHyderabad|3-5\nGujranwala|3-5\nSialkot|3-5\nQuetta|3-5",
		'default_days'    => '3-5',
		'cutoff_hour'     => 15,
		'sunday_off'      => true,
		'returns_days'    => 14,
		'flat_rate'       => 250,
		// Home page.
		'home_mode'       => 'design',
		'hero_eyebrow'    => 'Pakistan\'s checked beauty store',
		'hero_title'      => 'Original beauty, *checked* before it ships.',
		'hero_text'       => 'Skincare, sun care, hair, body and fragrance from the brands Pakistan trusts: local derm names, Korean favourites and our own Koh-e-Noor mists. Every order shows its batch number and expiry date.',
		'hero_checks'     => "Cash on delivery\nFree delivery over {free}\nMinis from Rs 799",
		'hero_image'      => 0,
		'hero_batch'      => 'AX2609K|03/2026|02/2029',
		'hero_tag_title'  => 'New: Koh-e-Noor',
		'hero_tag_text'   => 'Hair & body mists, Rs 1,290',
		'own_title'       => 'Koh-e-Noor hair & body mists',
		'own_text'        => 'Body sprays cost Rs 550 and fade fast. Perfume mists cost Rs 2,000 or more. Koh-e-Noor sits in between: 100 ml of long-lasting scent for your hair, skin and clothes, made in Pakistan.',
		'derm_title'      => 'The brands dermatologists recommend, in one place',
		'derm_text'       => 'Rederm, Estelin, Neophar and Jenpharm lead reviews for hair fall and sunscreen, but they are usually sold in pharmacies. Find the whole range here, sorted by concern.',
		'new_eyebrow'     => 'Trending in the US and Korea',
		'new_title'       => 'Just arrived in Pakistan',
		'new_text'        => 'PDRN, azelaic acid and new sunscreen formats. Few stores in Pakistan carry them yet.',
		'newsletter_form' => '',
		'seo_title'       => 'Original beauty products online in Pakistan',
		// Shop.
		'shop_title'      => 'Beauty & personal care',
		'shop_intro'      => 'Shop {count} original products from {brands} local and international brands. Every item is sourced from the brand or its authorised distributor.',
		'bundle_percent'  => 15,
		'bundle_min'      => 3,
		'new_days'        => 30,
		'low_stock'       => 5,
		'image_style'     => 'packshot',
		'show_batch'      => true,
		'wishlist_page'   => 0,
		'track_page'      => 0,
		// Checkout.
		'email_optional'  => false,
		'pk_phone_check'  => true,
		// Look.
		'accent'          => '#8c2350',
		'ink'             => '#241826',
		'load_fonts'      => true,
	);
}

/**
 * Read one theme setting, falling back to the default.
 *
 * @param string $key Setting name.
 * @return mixed
 */
function nexus_opt( $key ) {
	$defaults = nexus_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	return get_theme_mod( 'nexus_' . $key, $default );
}

/**
 * A text setting with {free}, {cutoff}, {returns} and {store} replaced.
 *
 * @param string $key Setting name.
 * @return string
 */
function nexus_text( $key ) {
	return nexus_fill( (string) nexus_opt( $key ) );
}

/**
 * Replace placeholders in a string.
 *
 * @param string $s Text.
 * @return string
 */
function nexus_fill( $s ) {
	return strtr(
		(string) $s,
		array(
			'{free}'    => nexus_price_text( nexus_opt( 'free_shipping' ) ),
			'{cutoff}'  => nexus_cutoff_label(),
			'{returns}' => (int) nexus_opt( 'returns_days' ),
			'{store}'   => get_bloginfo( 'name' ),
		)
	);
}

/**
 * Cut-off hour as "3pm".
 *
 * @return string
 */
function nexus_cutoff_label() {
	$h = (int) nexus_opt( 'cutoff_hour' );
	if ( 0 === $h ) {
		return '12am';
	}
	return ( $h > 12 ? $h - 12 : $h ) . ( $h >= 12 ? 'pm' : 'am' );
}

/**
 * Lines of a textarea setting.
 *
 * @param string $key Setting name.
 * @return array
 */
function nexus_opt_lines( $key ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', nexus_text( $key ) ) ), 'strlen' ) );
}

/**
 * Delivery cities as [ 'Karachi' => '1-2', ... ].
 *
 * @return array
 */
function nexus_cities() {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) nexus_opt( 'cities' ) ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line ) );
		if ( ! empty( $parts[0] ) ) {
			$out[ $parts[0] ] = ( isset( $parts[1] ) && preg_match( '/^\d+-\d+$/', $parts[1] ) ) ? $parts[1] : nexus_opt( 'default_days' );
		}
	}
	return $out;
}

/**
 * "1–2 days to Karachi, Lahore and Islamabad, 3–5 days elsewhere".
 *
 * @return string
 */
function nexus_delivery_summary() {
	$cities = nexus_cities();
	$fast   = array();
	$min    = null;
	foreach ( $cities as $city => $days ) {
		$first = (int) $days;
		if ( null === $min || $first < $min ) {
			$min = $first;
		}
	}
	$fast_days = '';
	foreach ( $cities as $city => $days ) {
		if ( (int) $days === $min ) {
			$fast[]    = $city;
			$fast_days = $days;
		}
	}
	$other = str_replace( '-', '–', (string) nexus_opt( 'default_days' ) );
	if ( ! $fast ) {
		/* translators: %s: days */
		return sprintf( __( '%s days anywhere in Pakistan', 'nexus-beauty' ), $other );
	}
	$last = array_pop( $fast );
	$list = $fast ? implode( ', ', $fast ) . ' ' . __( 'and', 'nexus-beauty' ) . ' ' . $last : $last;
	/* translators: 1: days, 2: cities, 3: days elsewhere */
	return sprintf( __( '%1$s days to %2$s, %3$s days elsewhere', 'nexus-beauty' ), str_replace( '-', '–', $fast_days ), $list, $other );
}

/**
 * Estimated delivery window for a city, using the store's timezone,
 * the dispatch cut-off hour and (optionally) Sundays off.
 *
 * @param string $city City name.
 * @return string e.g. "Thu 2 Oct – Fri 3 Oct"
 */
function nexus_eta_text( $city ) {
	$cities = nexus_cities();
	$range  = isset( $cities[ $city ] ) ? $cities[ $city ] : nexus_opt( 'default_days' );
	list( $min, $max ) = array_map( 'intval', explode( '-', $range . '-' ) + array( 3, 5 ) );
	$max        = max( $min, $max );
	$now        = new DateTimeImmutable( 'now', wp_timezone() );
	$sunday_off = (bool) nexus_opt( 'sunday_off' );
	$is_workday = function ( DateTimeImmutable $d ) use ( $sunday_off ) {
		return ! ( $sunday_off && '0' === $d->format( 'w' ) );
	};
	$add_workdays = function ( DateTimeImmutable $d, $n ) use ( $is_workday ) {
		while ( $n > 0 ) {
			$d = $d->modify( '+1 day' );
			if ( $is_workday( $d ) ) {
				$n--;
			}
		}
		return $d;
	};
	$dispatch = ( $is_workday( $now ) && (int) $now->format( 'G' ) < (int) nexus_opt( 'cutoff_hour' ) ) ? $now : $add_workdays( $now, 1 );
	$from     = $add_workdays( $dispatch, $min );
	$to       = $add_workdays( $dispatch, $max );
	return wp_date( 'D j M', $from->getTimestamp() ) . ' – ' . wp_date( 'D j M', $to->getTimestamp() );
}

/**
 * WhatsApp link for the store number.
 *
 * @param string $text Optional pre-filled message.
 * @return string
 */
function nexus_whatsapp_url( $text = '' ) {
	$number = preg_replace( '/\D/', '', (string) nexus_opt( 'whatsapp' ) );
	$url    = 'https://wa.me/' . $number;
	return $text ? add_query_arg( 'text', rawurlencode( $text ), $url ) : $url;
}

/**
 * Human-friendly WhatsApp number: 923001234567 -> +92 300 1234567.
 *
 * @return string
 */
function nexus_whatsapp_display() {
	$n = preg_replace( '/\D/', '', (string) nexus_opt( 'whatsapp' ) );
	if ( 12 === strlen( $n ) && 0 === strpos( $n, '92' ) ) {
		return '+92 ' . substr( $n, 2, 3 ) . ' ' . substr( $n, 5 );
	}
	return '+' . $n;
}

/**
 * Parse a list of product IDs from a cookie.
 *
 * @param string $name Cookie name.
 * @param int    $max  Maximum IDs to keep.
 * @return int[]
 */
function nexus_cookie_ids( $name, $max = 50 ) {
	if ( empty( $_COOKIE[ $name ] ) ) {
		return array();
	}
	$raw = sanitize_text_field( wp_unslash( $_COOKIE[ $name ] ) );
	$ids = array_filter( array_map( 'absint', explode( ',', $raw ) ) );
	return array_slice( array_values( array_unique( $ids ) ), 0, $max );
}

/**
 * Interface icon from the sprite. Decorative (aria-hidden).
 *
 * @param string $name search, user, heart, bag, menu, close, truck, shield, return, cash, leaf, chat, clock, filter, check, whatsapp, play, up.
 * @return string
 */
function nexus_icon( $name ) {
	return '<svg aria-hidden="true"><use href="#ic-' . esc_attr( $name ) . '"/></svg>';
}

/**
 * Product illustration from the sprite (dropper, perfume, bottle, pump, lipstick, jar, tube).
 *
 * @param string $shape Shape name.
 * @return string
 */
function nexus_shape_svg( $shape ) {
	$shape = in_array( $shape, nexus_shapes(), true ) ? $shape : 'bottle';
	return '<svg aria-hidden="true" viewBox="0 0 100 160"><use href="#i-' . esc_attr( $shape ) . '"/></svg>';
}

/**
 * Available illustration shapes.
 *
 * @return array
 */
function nexus_shapes() {
	return array( 'dropper', 'perfume', 'bottle', 'pump', 'lipstick', 'jar', 'tube' );
}

/**
 * Print the SVG sprite once.
 */
function nexus_sprite() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;
	echo include NEXUS_DIR . '/inc/sprite.php'; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG.
}

/**
 * Colour and illustration for a category, from its settings or its name.
 * Category settings: Products > Categories > edit (Nexus look).
 *
 * @param WP_Term|null $term Category.
 * @return array [ shape, tone, tile ]
 */
function nexus_cat_look( $term ) {
	$map = array(
		'sun'       => array( 'tube', '#f0c24b', '#fdf5dc' ),
		'spf'       => array( 'tube', '#f0c24b', '#fdf5dc' ),
		'hair'      => array( 'bottle', '#c68a3a', '#f8eedf' ),
		'body'      => array( 'pump', '#d9b48f', '#f7efe6' ),
		'bath'      => array( 'pump', '#d9b48f', '#f7efe6' ),
		'fragrance' => array( 'perfume', '#b04a6b', '#f7e5eb' ),
		'perfume'   => array( 'perfume', '#b04a6b', '#f7e5eb' ),
		'mist'      => array( 'perfume', '#b04a6b', '#f7e5eb' ),
		'makeup'    => array( 'lipstick', '#9e2f4a', '#f6e2e7' ),
		'lip'       => array( 'lipstick', '#9e2f4a', '#f6e2e7' ),
		'men'       => array( 'tube', '#3c3f44', '#e8e9eb' ),
		'groom'     => array( 'tube', '#3c3f44', '#e8e9eb' ),
		'personal'  => array( 'jar', '#9bb79e', '#eaf2eb' ),
		'hygiene'   => array( 'jar', '#9bb79e', '#eaf2eb' ),
		'skin'      => array( 'dropper', '#e2a33b', '#fbf1df' ),
		'serum'     => array( 'dropper', '#e2a33b', '#fbf1df' ),
		'face'      => array( 'tube', '#b04a6b', '#f7e5eb' ),
	);
	$look = array( 'bottle', '#b04a6b', '#f7e5eb' );
	if ( $term instanceof WP_Term ) {
		foreach ( $map as $key => $l ) {
			if ( false !== strpos( $term->slug, $key ) ) {
				$look = $l;
				break;
			}
		}
		$shape = get_term_meta( $term->term_id, 'nx_shape', true );
		$tone  = get_term_meta( $term->term_id, 'nx_tone', true );
		$tile  = get_term_meta( $term->term_id, 'nx_tile', true );
		$look  = array(
			in_array( $shape, nexus_shapes(), true ) ? $shape : $look[0],
			sanitize_hex_color( $tone ) ? $tone : $look[1],
			sanitize_hex_color( $tile ) ? $tile : $look[2],
		);
	}
	return $look;
}

/**
 * Top-level product categories in the store's order (WooCommerce's "order" field).
 *
 * @param int $limit Limit (0 = all).
 * @return WP_Term[]
 */
function nexus_sorted_cats( $limit = 0 ) {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return array();
	}
	$cats = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => 0,
			'hide_empty' => true,
			'orderby'    => 'menu_order',
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
		)
	);
	if ( is_wp_error( $cats ) ) {
		return array();
	}
	usort(
		$cats,
		function ( $a, $b ) {
			return (int) get_term_meta( $a->term_id, 'order', true ) <=> (int) get_term_meta( $b->term_id, 'order', true );
		}
	);
	return $limit ? array_slice( $cats, 0, $limit ) : $cats;
}

/**
 * Hero title with *word* turned into the accent <em>.
 *
 * @param string $text Title.
 * @return string Safe HTML.
 */
function nexus_em( $text ) {
	return preg_replace( '/\*(.+?)\*/', '<em>$1</em>', esc_html( $text ) );
}

/**
 * Site logo: the custom logo if set, otherwise the site name with the accent dot, as in the design.
 *
 * @param string $tag Wrapper tag: a (linked) or span.
 */
function nexus_logo( $tag = 'a' ) {
	if ( has_custom_logo() && 'a' === $tag ) {
		the_custom_logo();
		return;
	}
	$name = get_bloginfo( 'name' );
	if ( 'a' === $tag ) {
		/* translators: %s: site name */
		printf( '<a class="logo" href="%1$s" rel="home" aria-label="%2$s">%3$s<span>.</span></a>', esc_url( home_url( '/' ) ), esc_attr( sprintf( __( '%s home', 'nexus-beauty' ), $name ) ), esc_html( $name ) );
	} else {
		printf( '<span class="logo">%s<span>.</span></span>', esc_html( $name ) );
	}
}

/**
 * Format a price with the store currency, without HTML.
 *
 * @param float $amount Amount.
 * @return string
 */
function nexus_price_text( $amount ) {
	if ( function_exists( 'wc_price' ) ) {
		return html_entity_decode( wp_strip_all_tags( wc_price( $amount ) ), ENT_QUOTES, 'UTF-8' );
	}
	return 'Rs ' . number_format_i18n( (float) $amount );
}

/**
 * Money without HTML.
 *
 * @param float $n Amount.
 * @return string
 */
function nexus_money( $n ) {
	return nexus_price_text( $n );
}

/**
 * Wishlist page URL.
 *
 * @return string
 */
function nexus_wishlist_url() {
	$id = (int) nexus_opt( 'wishlist_page' );
	if ( $id && get_post_status( $id ) ) {
		return get_permalink( $id );
	}
	$page = get_page_by_path( 'wishlist' );
	return $page ? get_permalink( $page ) : '';
}

/**
 * URL of a page created by the setup, by slug.
 *
 * @param string $slug Page slug.
 * @return string
 */
function nexus_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return ( $page && 'publish' === $page->post_status ) ? get_permalink( $page ) : '';
}

/**
 * Shop URL, or home if WooCommerce isn't active.
 *
 * @return string
 */
function nexus_shop_url() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

/**
 * Product search URL for a phrase.
 *
 * @param string $q Search phrase.
 * @return string
 */
function nexus_search_url( $q ) {
	$args = array( 's' => $q );
	if ( class_exists( 'WooCommerce' ) ) {
		$args['post_type'] = 'product';
	}
	return add_query_arg( $args, home_url( '/' ) );
}

/**
 * Breadcrumbs in the design's style. Uses WooCommerce's breadcrumb trail when available
 * (so Google also gets breadcrumb structured data), otherwise a simple trail.
 *
 * @param string $extra_class Extra class on the nav.
 */
function nexus_crumbs( $extra_class = '' ) {
	$trail = array();
	if ( class_exists( 'WC_Breadcrumb' ) ) {
		$bc = new WC_Breadcrumb();
		$bc->add_crumb( _x( 'Home', 'breadcrumb', 'nexus-beauty' ), home_url( '/' ) );
		$trail = $bc->generate();
		if ( function_exists( 'WC' ) && WC()->structured_data ) {
			WC()->structured_data->generate_breadcrumblist_data( $bc );
		}
	} else {
		$trail[] = array( _x( 'Home', 'breadcrumb', 'nexus-beauty' ), home_url( '/' ) );
		if ( is_singular() ) {
			foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $a ) {
				$trail[] = array( get_the_title( $a ), get_permalink( $a ) );
			}
			$trail[] = array( get_the_title(), '' );
		} elseif ( is_archive() ) {
			$trail[] = array( wp_strip_all_tags( get_the_archive_title() ), '' );
		} elseif ( is_search() ) {
			/* translators: %s: search phrase */
			$trail[] = array( sprintf( __( 'Search: %s', 'nexus-beauty' ), get_search_query() ), '' );
		}
	}
	$last = count( $trail ) - 1;
	echo '<nav class="crumbs ' . esc_attr( $extra_class ) . '" aria-label="' . esc_attr__( 'Breadcrumb', 'nexus-beauty' ) . '"><ol>';
	foreach ( $trail as $i => $c ) {
		if ( $i < $last && ! empty( $c[1] ) ) {
			printf( '<li><a href="%s">%s</a></li>', esc_url( $c[1] ), esc_html( wp_strip_all_tags( $c[0] ) ) );
		} else {
			printf( '<li aria-current="page">%s</li>', esc_html( wp_strip_all_tags( $c[0] ) ) );
		}
	}
	echo '</ol></nav>';
}

/**
 * Page header band used on pages, the shop, archives and the blog (the design's .shop-head).
 *
 * @param string $title   Title (plain text).
 * @param string $lede    Intro (plain text or safe HTML).
 * @param string $after   Extra safe HTML under the intro (chips).
 */
function nexus_page_head( $title, $lede = '', $after = '' ) {
	echo '<div class="shop-head">';
	nexus_crumbs( 'wrap' );
	echo '<div class="wrap"><h1>' . esc_html( $title ) . '</h1>';
	if ( $lede ) {
		echo '<div class="lede">' . wp_kses_post( $lede ) . '</div>';
	}
	echo $after; // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts by callers.
	echo '</div></div>';
}

/**
 * Split "a|b|c".
 *
 * @param string $s Text.
 * @return array
 */
function nexus_pipe( $s ) {
	return array_values( array_filter( array_map( 'trim', explode( '|', (string) $s ) ), 'strlen' ) );
}

/**
 * The FAQ list in the design's style.
 *
 * @param array $qa    [ [question, answer], ... ].
 * @param bool  $first_open Open the first item.
 * @return string
 */
function nexus_faq_html( $qa, $first_open = true ) {
	$out = '<div class="faq">';
	foreach ( array_values( $qa ) as $i => $q ) {
		$out .= '<details' . ( $first_open && 0 === $i ? ' open' : '' ) . '><summary>' . esc_html( $q[0] ) . '</summary><div><p>' . esc_html( $q[1] ) . '</p></div></details>';
	}
	return $out . '</div>';
}

/**
 * FAQPage structured data (skipped when an SEO plugin handles schema).
 *
 * @param array $qa [ [question, answer], ... ].
 */
function nexus_faq_schema( $qa ) {
	if ( ! $qa || defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) ) {
		return;
	}
	$items = array();
	foreach ( $qa as $q ) {
		$items[] = array(
			'@type'          => 'Question',
			'name'           => $q[0],
			'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $q[1] ),
		);
	}
	echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
}

/**
 * Store-wide FAQ used on the home page.
 *
 * @return array
 */
function nexus_store_faq() {
	$store = get_bloginfo( 'name' );
	return apply_filters(
		'nexus_store_faq',
		array(
			/* translators: %s: store name */
			array( sprintf( __( 'Are the products on %s original?', 'nexus-beauty' ), $store ), __( 'Yes. We buy from the brand or its authorised distributor only. Every order shows the batch number and expiry date, and you can ask us for the source of any product before you buy. If anything looks wrong, we refund you in full.', 'nexus-beauty' ) ),
			array( __( 'Can I pay cash on delivery?', 'nexus-beauty' ), __( 'Yes, cash on delivery works on every order anywhere in Pakistan. You can also pay by card, JazzCash or Easypaisa.', 'nexus-beauty' ) ),
			/* translators: 1: cut-off time, 2: delivery summary, 3: free delivery amount */
			array( __( 'How long does delivery take?', 'nexus-beauty' ), sprintf( __( 'Orders placed before %1$s leave our warehouse the same day. Delivery takes %2$s. Delivery is free over %3$s.', 'nexus-beauty' ), nexus_cutoff_label(), nexus_delivery_summary(), nexus_price_text( nexus_opt( 'free_shipping' ) ) ) ),
			array( __( 'Why do you sell minis?', 'nexus-beauty' ), __( 'Many buyers want to test a product before paying for the full size. Minis let you try a serum or sunscreen for under Rs 1,000.', 'nexus-beauty' ) ),
			/* translators: %d: days */
			array( __( 'What is your return policy?', 'nexus-beauty' ), sprintf( __( 'Unopened products can be returned within %d days. Damaged or wrong items are replaced free of charge.', 'nexus-beauty' ), (int) nexus_opt( 'returns_days' ) ) ),
		)
	);
}

/**
 * Reading time for the current post.
 *
 * @return string
 */
function nexus_read_time() {
	$words = str_word_count( wp_strip_all_tags( (string) get_post_field( 'post_content', get_the_ID() ) ) );
	/* translators: %d: minutes */
	return sprintf( __( '%d min read', 'nexus-beauty' ), max( 1, (int) round( $words / 220 ) ) );
}

/**
 * Blog post card in the design's card style.
 */
function nexus_post_card() {
	$cats = get_the_category();
	?>
	<article <?php post_class( 'card card--post' ); ?>>
		<div class="card__media" style="--tone:var(--berry);--bg:var(--blush)">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy', 'alt' => '' ) );
			} else {
				echo nexus_shape_svg( 'dropper' ); // phpcs:ignore WordPress.Security.EscapeOutput
			}
			?>
		</div>
		<?php if ( $cats ) : ?><p class="card__brand"><?php echo esc_html( $cats[0]->name ); ?></p><?php endif; ?>
		<h2 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="card__why"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
		<div class="card__meta"><span class="card__size"><?php echo esc_html( get_the_date() . ' · ' . nexus_read_time() ); ?></span></div>
	</article>
	<?php
}
