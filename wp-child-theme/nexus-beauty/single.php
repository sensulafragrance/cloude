<?php
/**
 * Single blog post.
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
		$nexus_cats = get_the_category();
		?>
		<article <?php post_class(); ?>>
			<?php nexus_crumbs( 'wrap' ); ?>
			<div class="wrap" style="max-width:900px">
				<?php if ( $nexus_cats ) : ?><p class="eyebrow"><a href="<?php echo esc_url( get_category_link( $nexus_cats[0] ) ); ?>"><?php echo esc_html( $nexus_cats[0]->name ); ?></a></p><?php endif; ?>
				<h1 class="h2" style="font-size:clamp(30px,4.2vw,48px);margin-top:8px"><?php the_title(); ?></h1>
				<p class="post-meta"><span><?php echo esc_html( get_the_date() ); ?></span><span>·</span><span><?php echo esc_html( nexus_read_time() ); ?></span></p>
				<?php if ( has_post_thumbnail() ) : ?><div class="post-hero"><?php the_post_thumbnail( 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?></div><?php endif; ?>
				<div class="prose page-body"><?php the_content(); ?><?php wp_link_pages( array( 'before' => '<nav class="page-links">', 'after' => '</nav>' ) ); ?></div>
				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</div>
		</article>
		<?php
		$nexus_related = $nexus_cats ? get_posts( array( 'category__in' => array( $nexus_cats[0]->term_id ), 'post__not_in' => array( get_the_ID() ), 'posts_per_page' => 3 ) ) : array();
		if ( $nexus_related ) :
			?>
			<section class="section section--tint" aria-labelledby="more-h">
				<div class="wrap">
					<div class="sec-head"><div><p class="eyebrow"><?php esc_html_e( 'Keep reading', 'nexus-beauty' ); ?></p><h2 class="h2" id="more-h"><?php esc_html_e( 'More from the journal', 'nexus-beauty' ); ?></h2></div></div>
					<div class="grid grid--3">
						<?php
						global $post;
						foreach ( $nexus_related as $post ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride
							setup_postdata( $post );
							nexus_post_card();
						endforeach;
						wp_reset_postdata();
						?>
					</div>
				</div>
			</section>
		<?php endif; ?>
	<?php endwhile; ?>
</main>
<?php
get_footer();
