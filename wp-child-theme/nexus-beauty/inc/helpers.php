<?php
/**
 * Helpers: settings, icons and small utilities used across the theme.
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
		'announcements'   => "Free delivery on orders over Rs 5,000\nCash on delivery anywhere in Pakistan\nBatch and expiry shown on every order",
		'whatsapp'        => '923000000000',
		'support_email'   => 'care@nexusbeauty.pk',
		'support_hours'   => '10am–10pm, every day',
		'free_shipping'   => 5000,
		'cities'          => "Karachi|1-2\nLahore|1-2\nIslamabad|2-3\nRawalpindi|2-3\nFaisalabad|3-5\nMultan|3-5\nPeshawar|3-5\nHyderabad|3-5\nGujranwala|3-5\nSialkot|3-5\nQuetta|3-5",
		'default_days'    => '3-5',
		'cutoff_hour'     => 15,
		'sunday_off'      => true,
		'bundle_percent'  => 15,
		'bundle_min'      => 3,
		'new_days'        => 30,
		'low_stock'       => 5,
		'cookie_notice'   => true,
		'cookie_text'     => 'We use a few cookies to keep your bag and wishlist saved and to understand what shoppers like.',
		'footer_about'    => "Pakistan's checked beauty store. Original skincare, hair care, fragrance and makeup, with the batch and expiry on every order.",
		'payment_badges'  => 'COD, VISA, MASTERCARD, JAZZCASH, EASYPAISA',
		'wishlist_page'   => 0,
		'track_page'      => 0,
		'accent'          => '#8c2350',
		'ink'             => '#241826',
		'load_fonts'      => true,
		'email_optional'  => false,
		'pk_phone_check'  => true,
		'show_batch'      => true,
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
 * Inline SVG icons. All are decorative (aria-hidden).
 *
 * @param string $name Icon name.
 * @return string
 */
function nexus_icon( $name ) {
	$paths = array(
		'search'  => '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
		'user'    => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/>',
		'heart'   => '<path d="M12 20s-7.5-4.6-9-9.3C2 7.4 4.2 4.5 7.4 4.5c2 0 3.4 1.1 4.6 2.6 1.2-1.5 2.6-2.6 4.6-2.6 3.2 0 5.4 2.9 4.4 6.2-1.5 4.7-9 9.3-9 9.3z"/>',
		'bag'     => '<path d="M5 8h14l-1 12H6z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/>',
		'menu'    => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'   => '<path d="M6 6l12 12M18 6 6 18"/>',
		'check'   => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
		'shield'  => '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="m8.5 12 2.5 2.5 4.5-5"/>',
		'cash'    => '<rect x="2.5" y="6" width="19" height="12" rx="2"/><circle cx="12" cy="12" r="2.6"/>',
		'truck'   => '<path d="M2 6h12v10H2zM14 10h4l3 3v3h-7z"/><circle cx="6" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
		'return'  => '<path d="M4 9h11a5 5 0 0 1 0 10H9"/><path d="M8 5 4 9l4 4"/>',
		'chat'    => '<path d="M4 5h16v11H9l-5 4z"/>',
		'filter'  => '<path d="M4 6h16M7 12h10M10 18h4"/>',
		'up'      => '<path d="m6 15 6-6 6 6"/>',
		'play'    => '<path d="M8 5v14l11-7z" fill="currentColor" stroke="none"/>',
		'whatsapp'=> '<path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.6-1.2A9 9 0 1 0 12 3Z"/><path d="M8.8 8.6c.2-.5.4-.5.7-.5h.5c.2 0 .4 0 .5.4l.7 1.7c.1.2 0 .4-.1.5l-.5.6c-.1.1-.2.3 0 .5.4.8 1.6 2 2.5 2.4.2.1.4.1.5 0l.6-.7c.1-.2.3-.2.5-.1l1.6.8c.2.1.3.2.3.4 0 .6-.3 1.4-1 1.7-.6.3-1.4.4-3.2-.4-2-1-3.4-3-3.6-3.5-.3-.5-.7-1.5-.3-2.3Z"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return '<svg class="nx-icon nx-icon--' . esc_attr( $name ) . '" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}

/**
 * The Nexus mark (two rings with a filled lens).
 *
 * @return string
 */
function nexus_mark() {
	return '<svg class="nx-mark" viewBox="0 0 120 80" aria-hidden="true" focusable="false"><circle cx="44" cy="40" r="30" fill="none" stroke="currentColor" stroke-width="6"/><circle cx="76" cy="40" r="30" fill="none" stroke="currentColor" stroke-width="6"/><path d="M60 14.62A30 30 0 0 1 60 65.38 30 30 0 0 1 60 14.62Z" class="nx-mark__lens"/></svg>';
}

/**
 * Format a price with the store currency, without HTML.
 *
 * @param float $amount Amount.
 * @return string
 */
function nexus_price_text( $amount ) {
	if ( function_exists( 'wc_price' ) ) {
		return wp_strip_all_tags( wc_price( $amount ) );
	}
	return 'Rs ' . number_format_i18n( $amount );
}
