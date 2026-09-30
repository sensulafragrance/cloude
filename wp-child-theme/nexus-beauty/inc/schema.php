<?php
/**
 * Organization and site search markup for Google.
 * Skipped automatically when an SEO plugin (Yoast, Rank Math, SEOPress, AIOSEO, The SEO Framework) is active,
 * because those plugins output the same markup. WooCommerce adds product markup itself.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_head',
	function () {
		if ( ! is_front_page() ) {
			return;
		}
		if ( defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' ) ) {
			return;
		}
		$logo_id = get_theme_mod( 'custom_logo' );
		$graph   = array(
			array(
				'@type'        => class_exists( 'WooCommerce' ) ? 'OnlineStore' : 'Organization',
				'@id'          => home_url( '/#organization' ),
				'name'         => get_bloginfo( 'name' ),
				'url'          => home_url( '/' ),
				'logo'         => $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : null,
				'contactPoint' => array(
					'@type'             => 'ContactPoint',
					'telephone'         => '+' . preg_replace( '/\D/', '', (string) nexus_opt( 'whatsapp' ) ),
					'email'             => nexus_opt( 'support_email' ),
					'contactType'       => 'customer service',
					'availableLanguage' => array( 'en', 'ur' ),
				),
			),
			array(
				'@type'           => 'WebSite',
				'@id'             => home_url( '/#website' ),
				'name'            => get_bloginfo( 'name' ),
				'url'             => home_url( '/' ),
				'publisher'       => array( '@id' => home_url( '/#organization' ) ),
				'potentialAction' => array(
					'@type'       => 'SearchAction',
					'target'      => add_query_arg( array( 's' => '{search_term_string}', 'post_type' => 'product' ), home_url( '/' ) ),
					'query-input' => 'required name=search_term_string',
				),
			),
		);
		$graph[0] = array_filter( $graph[0] );
		echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $graph ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
);
