<?php
/**
 * Page not found.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main">
	<?php nexus_page_head( __( 'Page not found', 'nexus-beauty' ), esc_html__( 'The page you were looking for has moved or no longer exists. Try a search, or start from a category.', 'nexus-beauty' ) ); ?>
	<div class="wrap page-body nx-404">
		<div class="finder">
			<h2 class="h2"><?php esc_html_e( 'Search the store', 'nexus-beauty' ); ?></h2>
			<?php nexus_search_form( 'q-404' ); ?>
			<?php nexus_popular_searches(); ?>
		</div>
		<?php
		if ( function_exists( 'nexus_home_cats' ) && class_exists( 'WooCommerce' ) ) {
			echo nexus_cat_tiles( nexus_home_cats( 8 ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		?>
		<p><a class="btn btn--dark" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the home page', 'nexus-beauty' ); ?></a></p>
	</div>
</main>
<?php
get_footer();
