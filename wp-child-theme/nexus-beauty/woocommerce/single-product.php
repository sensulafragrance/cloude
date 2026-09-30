<?php
/**
 * Single product page wrapper (no sidebar; the design is full width).
 *
 * @package NexusBeauty
 * @version 1.6.4
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
?>
<main id="main">
	<?php
	while ( have_posts() ) :
		the_post();
		wc_get_template_part( 'content', 'single-product' );
	endwhile;
	?>
</main>
<?php
get_footer( 'shop' );
