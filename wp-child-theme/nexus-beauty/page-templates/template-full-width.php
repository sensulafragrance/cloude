<?php
/**
 * Template Name: Nexus full width
 * Description: Full-width page without sidebar or title. Use it with the Nexus block patterns.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'generate_show_title', '__return_false' );
get_header();
?>
<div id="primary" class="content-area nx-fullwidth">
	<main id="main" class="site-main">
		<?php
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</main>
</div>
<?php
get_footer();
