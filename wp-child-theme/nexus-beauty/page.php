<?php
/**
 * Pages: the design's header band (breadcrumbs, title, intro) and the page content.
 * WooCommerce's cart, checkout and account pages use this template too.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main">
	<?php
	while ( have_posts() ) :
		the_post();
		$nexus_full = is_page_template( 'page-templates/template-full-width.php' );
		$nexus_woo  = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );
		if ( ! $nexus_full ) {
			nexus_page_head( get_the_title(), has_excerpt() ? esc_html( get_the_excerpt() ) : '' );
		}
		?>
		<div class="<?php echo $nexus_full ? 'nx-full-content' : 'wrap page-body'; ?>">
			<div class="<?php echo $nexus_woo ? 'woo-page' : 'prose'; ?>">
				<?php
				the_content();
				wp_link_pages( array( 'before' => '<nav class="page-links">', 'after' => '</nav>' ) );
				?>
			</div>
			<?php
			if ( ! $nexus_woo && ( comments_open() || get_comments_number() ) ) {
				comments_template();
			}
			?>
		</div>
	<?php endwhile; ?>
</main>
<?php
get_footer();
