<?php
/**
 * Home page: the design's sections, filled automatically from the store (see inc/home.php).
 * Appearance > Customize > Nexus Beauty > Home page can switch to showing the page's own content instead.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main">
	<?php
	if ( 'content' === nexus_opt( 'home_mode' ) && is_page() ) {
		while ( have_posts() ) {
			the_post();
			echo '<div class="nx-full-content">';
			the_content();
			echo '</div>';
		}
	} else {
		$nexus_woo  = class_exists( 'WooCommerce' );
		$nexus_cats = $nexus_woo ? nexus_home_cats( 8 ) : array();
		nexus_home_hero();
		nexus_trust_strip();
		if ( $nexus_woo ) {
			nexus_home_categories( $nexus_cats );
			nexus_home_picks( $nexus_cats );
		}
		nexus_home_finder();
		if ( $nexus_woo ) {
			nexus_home_own();
			nexus_home_derm();
			nexus_home_new();
			nexus_home_promo();
			nexus_home_concerns();
			nexus_brand_tiles( 12 );
		}
		nexus_home_why();
		nexus_home_faq();
		nexus_home_news();
		nexus_home_seo();
	}
	?>
</main>
<?php
get_footer();
