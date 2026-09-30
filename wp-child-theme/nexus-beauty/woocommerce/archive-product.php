<?php
/**
 * Shop, categories (collections), brands, tags and product search: the design's shop page.
 *
 * @package NexusBeauty
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

global $wp_query;
$nexus_total   = (int) $wp_query->found_posts;
$nexus_term    = is_product_taxonomy() ? get_queried_object() : null;
$nexus_is_shop = is_shop() && ! is_search();
$nexus_paged   = max( 1, (int) get_query_var( 'paged' ) );
$nexus_filters = nexus_filters_active();

// Title and intro.
if ( is_search() ) {
	/* translators: %s: search phrase */
	$nexus_title = sprintf( __( 'Results for “%s”', 'nexus-beauty' ), get_search_query() );
	/* translators: %d: number of products */
	$nexus_lede = sprintf( _n( '%d product matches your search.', '%d products match your search.', $nexus_total, 'nexus-beauty' ), $nexus_total );
} elseif ( $nexus_is_shop ) {
	$nexus_title  = nexus_opt( 'shop_title' ) ? nexus_opt( 'shop_title' ) : woocommerce_page_title( false );
	$nexus_counts = wp_count_posts( 'product' );
	$nexus_brands = taxonomy_exists( 'product_brand' ) ? (int) wp_count_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => true ) ) : 0;
	$nexus_lede   = strtr(
		nexus_text( 'shop_intro' ),
		array(
			'{count}'  => number_format_i18n( isset( $nexus_counts->publish ) ? $nexus_counts->publish : $nexus_total ),
			'{brands}' => number_format_i18n( $nexus_brands ),
		)
	);
	if ( ! $nexus_brands ) {
		$nexus_lede = preg_replace( '/ from 0 [^.]*\./', '.', $nexus_lede );
	}
} else {
	$nexus_title = woocommerce_page_title( false );
	$nexus_lede  = $nexus_term && $nexus_term->description ? wpautop( $nexus_term->description ) : '';
	if ( ! $nexus_lede && $nexus_term ) {
		$nexus_lede = is_tax( 'product_brand' )
			/* translators: %s: brand */
			? sprintf( __( 'Every %s product we stock, bought from the brand or its authorised distributor, with the batch and expiry on every order.', 'nexus-beauty' ), $nexus_term->name )
			/* translators: 1: count, 2: category */
			: sprintf( _n( '%1$d original %2$s product, checked before it ships.', '%1$d original %2$s products, checked before they ship.', $nexus_total, 'nexus-beauty' ), $nexus_total, strtolower( $nexus_term->name ) );
	}
}

// Chips under the intro: sub-collections on a category, otherwise popular concerns.
$nexus_chips = '';
if ( is_product_category() ) {
	$nexus_children = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => $nexus_term->term_id, 'hide_empty' => true, 'orderby' => 'menu_order' ) );
	foreach ( is_wp_error( $nexus_children ) ? array() : $nexus_children as $nexus_c ) {
		$nexus_chips .= '<a href="' . esc_url( get_term_link( $nexus_c ) ) . '">' . esc_html( $nexus_c->name ) . '</a>';
	}
}
if ( ! $nexus_chips && ! is_search() ) {
	$nexus_state = nexus_filter_state();
	foreach ( nexus_concern_tags( 6 ) as $nexus_t ) {
		$nexus_on     = in_array( $nexus_t->slug, $nexus_state['tag'], true );
		$nexus_chips .= '<a href="' . esc_url( add_query_arg( 'nx_tag', array( $nexus_t->slug ), nexus_filter_url( array( 'nx_tag' ) ) ) ) . '"' . ( $nexus_on ? ' aria-current="page"' : '' ) . '>' . esc_html( $nexus_t->name ) . '</a>';
	}
}
$nexus_after = $nexus_chips ? '<div class="concerns" aria-label="' . esc_attr__( 'Popular searches', 'nexus-beauty' ) . '">' . $nexus_chips . '</div>' : '';
?>
<main id="main">
	<?php nexus_page_head( $nexus_title, $nexus_lede, $nexus_after ); ?>
	<div class="wrap shop">
		<?php nexus_filter_panel(); ?>
		<section aria-label="<?php esc_attr_e( 'Products', 'nexus-beauty' ); ?>">
			<div class="toolbar">
				<div class="toolbar__left">
					<button class="btn btn--ghost filter-toggle" type="button" data-open-filters style="padding:10px 16px"><?php echo nexus_icon( 'filter' ); // phpcs:ignore ?> <?php esc_html_e( 'Filter', 'nexus-beauty' ); ?></button>
					<?php /* translators: %s: number of products */ ?>
					<p><?php echo wp_kses( sprintf( _n( '<b>%s</b> product', '<b>%s</b> products', $nexus_total, 'nexus-beauty' ), number_format_i18n( $nexus_total ) ), array( 'b' => array() ) ); ?></p>
				</div>
				<?php
				if ( $nexus_total > 1 ) {
					woocommerce_catalog_ordering();
				}
				?>
			</div>
			<?php
			nexus_active_filters();
			woocommerce_output_all_notices();

			if ( have_posts() ) {
				do_action( 'woocommerce_before_shop_loop' );
				$nexus_banner = $nexus_is_shop && 1 === $nexus_paged && (int) nexus_opt( 'bundle_percent' ) > 0 && nexus_page_url( 'routines' );
				$nexus_i      = 0;
				echo '<div class="grid grid--3" data-product-grid>';
				while ( have_posts() ) {
					the_post();
					do_action( 'woocommerce_shop_loop' );
					$nexus_product = wc_get_product( get_the_ID() );
					if ( $nexus_product && $nexus_product->is_visible() ) {
						echo nexus_card( $nexus_product ); // phpcs:ignore WordPress.Security.EscapeOutput
						$nexus_i++;
					}
					if ( $nexus_banner && 6 === $nexus_i && $nexus_total > 6 ) {
						?>
						<aside class="inline-banner">
							<?php /* translators: %d: percent */ ?>
							<div><b><?php echo esc_html( sprintf( __( 'Build a routine, save %d%%', 'nexus-beauty' ), (int) nexus_opt( 'bundle_percent' ) ) ); ?></b><p style="font-size:14px;color:var(--ink-2)"><?php esc_html_e( 'Any cleanser + serum + SPF. Discount applies in your bag.', 'nexus-beauty' ); ?></p></div>
							<a class="btn btn--dark" href="<?php echo esc_url( nexus_page_url( 'routines' ) ); ?>"><?php esc_html_e( 'Build my routine', 'nexus-beauty' ); ?></a>
						</aside>
						<?php
					}
				}
				echo '</div>';
				do_action( 'woocommerce_after_shop_loop' );
				nexus_shop_pager();
			} else {
				do_action( 'woocommerce_no_products_found' );
				echo '<p class="empty">';
				if ( $nexus_filters ) {
					echo esc_html__( 'No products match these filters.', 'nexus-beauty' ) . ' <a class="linkish" href="' . esc_url( nexus_filter_url( array( 'nx_cat', 'nx_origin', 'nx_brand', 'nx_tag', 'nx_sale', 'nx_stock', 'min_price', 'max_price' ) ) ) . '">' . esc_html__( 'Clear filters', 'nexus-beauty' ) . '</a>';
				} elseif ( is_search() ) {
					echo esc_html__( 'No products found. Try another word, or browse the categories.', 'nexus-beauty' );
				} else {
					echo esc_html__( 'No products here yet.', 'nexus-beauty' ) . ' <a class="linkish" href="' . esc_url( nexus_shop_url() ) . '">' . esc_html__( 'See all products', 'nexus-beauty' ) . '</a>';
				}
				echo '</p>';
			}
			?>
		</section>
	</div>
	<?php if ( $nexus_is_shop && 1 === $nexus_paged && ! $nexus_filters ) : ?>
		<?php nexus_shop_guide(); ?>
	<?php endif; ?>
</main>
<?php
get_footer( 'shop' );
