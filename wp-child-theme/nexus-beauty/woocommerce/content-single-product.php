<?php
/**
 * Product page content in the design: breadcrumbs, gallery + buy box, then the sections below.
 *
 * @package NexusBeauty
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Notices (e.g. "added to your bag") and anything plugins print first.
echo '<div class="wrap notices-wrap">';
do_action( 'woocommerce_before_single_product' );
echo '</div>';

if ( post_password_required() ) {
	echo '<div class="wrap page-body">' . get_the_password_form() . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
	<?php nexus_crumbs( 'wrap' ); ?>
	<div class="wrap pdp">
		<?php nexus_gallery( $product ); ?>
		<?php nexus_buybox( $product ); ?>
	</div>
	<?php
	nexus_fbt_section( $product );
	nexus_reviews_section( $product );
	nexus_qa_section( $product );
	nexus_related_section( $product );
	echo nexus_recent_block( 4 ); // phpcs:ignore WordPress.Security.EscapeOutput
	nexus_sticky_bar( $product );
	?>
</div>
<?php
do_action( 'woocommerce_after_single_product' );
