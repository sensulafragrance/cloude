<?php
/**
 * Blog (Journal), post archives and site search, as a grid of the design's cards.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_search() ) {
	/* translators: %s: search phrase */
	$nexus_title = sprintf( __( 'Results for “%s”', 'nexus-beauty' ), get_search_query() );
	$nexus_lede  = '';
} elseif ( is_home() ) {
	$nexus_posts = (int) get_option( 'page_for_posts' );
	$nexus_title = $nexus_posts ? get_the_title( $nexus_posts ) : __( 'Journal', 'nexus-beauty' );
	$nexus_lede  = $nexus_posts && has_excerpt( $nexus_posts ) ? esc_html( get_the_excerpt( $nexus_posts ) ) : esc_html__( 'Skincare routines, ingredient guides and honest product advice for Pakistan\'s weather.', 'nexus-beauty' );
} else {
	$nexus_title = wp_strip_all_tags( get_the_archive_title() );
	$nexus_lede  = get_the_archive_description();
}
?>
<main id="main">
	<?php nexus_page_head( $nexus_title, $nexus_lede ); ?>
	<div class="wrap page-body">
		<?php if ( have_posts() ) : ?>
			<div class="grid grid--3">
				<?php
				while ( have_posts() ) :
					the_post();
					nexus_post_card();
				endwhile;
				?>
			</div>
			<?php
			$nexus_links = paginate_links( array( 'type' => 'array', 'prev_text' => '‹', 'next_text' => '›', 'mid_size' => 1 ) );
			if ( $nexus_links ) {
				echo '<nav class="pager" aria-label="' . esc_attr__( 'Pagination', 'nexus-beauty' ) . '">' . implode( '', $nexus_links ) . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput
			}
			?>
		<?php else : ?>
			<div class="empty"><p><?php esc_html_e( 'Nothing here yet.', 'nexus-beauty' ); ?></p></div>
			<div class="finder" style="margin-top:24px"><?php nexus_search_form( 'q-empty' ); ?></div>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
