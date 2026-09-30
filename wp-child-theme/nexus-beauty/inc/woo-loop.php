<?php
/**
 * Product cards, badges, category chips and the built-in shop filters.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

/* ---------- Badges ---------- */

/**
 * Badge labels for product tags (slug => label). Add a tag with one of these slugs to show the badge.
 *
 * @return array
 */
function nexus_tag_badges() {
	return apply_filters(
		'nexus_tag_badges',
		array(
			'bestseller' => __( 'Best seller', 'nexus-beauty' ),
			'mini'       => __( 'Mini', 'nexus-beauty' ),
			'derm'       => __( 'Derm brand', 'nexus-beauty' ),
			'our-label'  => __( 'Our label', 'nexus-beauty' ),
			'korea'      => __( 'Korea', 'nexus-beauty' ),
			'pakistani'  => __( 'Pakistani', 'nexus-beauty' ),
		)
	);
}

/**
 * Sale percentage (highest across variations).
 *
 * @param WC_Product $product Product.
 * @return int
 */
function nexus_sale_percent( $product ) {
	if ( ! $product->is_on_sale() ) {
		return 0;
	}
	if ( $product->is_type( 'variable' ) ) {
		$prices = $product->get_variation_prices( true );
		$max    = 0;
		foreach ( $prices['regular_price'] as $id => $regular ) {
			$sale = isset( $prices['sale_price'][ $id ] ) ? (float) $prices['sale_price'][ $id ] : 0;
			if ( $regular > 0 && $sale > 0 && $sale < $regular ) {
				$max = max( $max, (int) round( ( 1 - $sale / $regular ) * 100 ) );
			}
		}
		return $max;
	}
	$regular = (float) $product->get_regular_price();
	$sale    = (float) $product->get_sale_price();
	return ( $regular > 0 && $sale > 0 ) ? (int) round( ( 1 - $sale / $regular ) * 100 ) : 0;
}

/**
 * Badges for a product, most important first.
 *
 * @param WC_Product $product Product.
 * @param int        $max     Maximum badges.
 * @return array [ [label, class], ... ]
 */
function nexus_badges( $product, $max = 3 ) {
	$out = array();
	if ( ! $product->is_in_stock() ) {
		$out[] = array( __( 'Sold out', 'nexus-beauty' ), 'out' );
	}
	$pct = nexus_sale_percent( $product );
	if ( $pct > 0 ) {
		/* translators: %d: discount percentage */
		$out[] = array( sprintf( __( '−%d%%', 'nexus-beauty' ), $pct ), 'sale' );
	}
	$created = $product->get_date_created();
	if ( $created && ( time() - $created->getTimestamp() ) < DAY_IN_SECONDS * (int) nexus_opt( 'new_days' ) ) {
		$out[] = array( __( 'New', 'nexus-beauty' ), 'new' );
	}
	$map = nexus_tag_badges();
	foreach ( (array) get_the_terms( $product->get_id(), 'product_tag' ) as $term ) {
		if ( $term instanceof WP_Term && isset( $map[ $term->slug ] ) ) {
			$out[] = array( $map[ $term->slug ], 'tag-' . $term->slug );
		}
	}
	if ( $product->managing_stock() && $product->is_in_stock() ) {
		$qty = (int) $product->get_stock_quantity();
		if ( $qty > 0 && $qty <= (int) nexus_opt( 'low_stock' ) ) {
			/* translators: %d: items left */
			$out[] = array( sprintf( __( 'Only %d left', 'nexus-beauty' ), $qty ), 'low' );
		}
	}
	return array_slice( $out, 0, $max );
}

/**
 * Print badges.
 *
 * @param WC_Product $product Product.
 */
function nexus_print_badges( $product ) {
	$badges = nexus_badges( $product );
	if ( ! $badges ) {
		return;
	}
	echo '<div class="nx-badges">';
	foreach ( $badges as $b ) {
		printf( '<span class="nx-pill nx-pill--%s">%s</span>', esc_attr( $b[1] ), esc_html( $b[0] ) );
	}
	echo '</div>';
}

/* ---------- Product card ---------- */

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );

add_action(
	'woocommerce_before_shop_loop_item_title',
	function () {
		global $product;
		nexus_print_badges( $product );
	},
	9
);

// Wishlist heart, placed before the product link so it isn't nested inside it.
add_action(
	'woocommerce_before_shop_loop_item',
	function () {
		global $product;
		if ( function_exists( 'nexus_wish_button' ) ) {
			nexus_wish_button( $product->get_id() );
		}
	},
	5
);

// Brand above the title.
add_action(
	'woocommerce_shop_loop_item_title',
	function () {
		global $product;
		list( $brand ) = nexus_brand( $product );
		if ( $brand ) {
			echo '<p class="nx-card__brand">' . esc_html( $brand ) . '</p>';
		}
	},
	5
);

// Tagline and size under the title.
add_action(
	'woocommerce_after_shop_loop_item_title',
	function () {
		global $product;
		$tagline = nexus_meta( $product, '_nx_tagline' );
		$size    = nexus_meta( $product, '_nx_size' );
		if ( $tagline ) {
			echo '<p class="nx-card__why">' . esc_html( $tagline ) . '</p>';
		}
		if ( $size ) {
			echo '<p class="nx-card__size">' . esc_html( $size ) . '</p>';
		}
	},
	3
);

add_filter(
	'woocommerce_loop_add_to_cart_args',
	function ( $args ) {
		$args['class'] .= ' nx-btn nx-btn--dark';
		return $args;
	}
);

// Show shop notices below the toolbar instead of inside it.
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );
add_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 36 );

/* ---------- Category chips above the products ---------- */

add_action(
	'woocommerce_before_shop_loop',
	function () {
		if ( ! ( is_shop() || is_product_category() ) ) {
			return;
		}
		$current = is_product_category() ? get_queried_object() : null;
		$parent  = $current ? ( $current->parent ? $current->parent : $current->term_id ) : 0;
		$terms   = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'parent'     => $parent,
				'hide_empty' => true,
				'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
				'number'     => 20,
			)
		);
		if ( is_wp_error( $terms ) || ! $terms ) {
			return;
		}
		$all_url  = $parent ? get_term_link( (int) $parent, 'product_cat' ) : wc_get_page_permalink( 'shop' );
		$all_name = $parent ? get_term( $parent )->name : __( 'All', 'nexus-beauty' );
		echo '<nav class="nx-chips" aria-label="' . esc_attr__( 'Categories', 'nexus-beauty' ) . '">';
		printf( '<a class="nx-chip%s" href="%s">%s</a>', ( ! $current || $current->term_id === $parent ) ? ' is-active' : '', esc_url( is_wp_error( $all_url ) ? '' : $all_url ), esc_html( $all_name ) );
		foreach ( $terms as $t ) {
			printf( '<a class="nx-chip%s" href="%s">%s</a>', ( $current && $current->term_id === $t->term_id ) ? ' is-active' : '', esc_url( get_term_link( $t ) ), esc_html( $t->name ) );
		}
		echo '</nav>';
	},
	5
);

/* ---------- Filters (no plugin needed) ---------- */

/**
 * Active filter values from the URL.
 *
 * @return array
 */
function nexus_filter_state() {
	// Read-only GET filters; no state is changed, so no nonce is needed.
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$arr = function ( $key ) {
		if ( empty( $_GET[ $key ] ) ) {
			return array();
		}
		$raw = is_array( $_GET[ $key ] ) ? wp_unslash( $_GET[ $key ] ) : explode( ',', wp_unslash( $_GET[ $key ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		return array_values( array_filter( array_map( 'sanitize_title', $raw ) ) );
	};
	$state = array(
		'brand'     => $arr( 'nx_brand' ),
		'tag'       => $arr( 'nx_tag' ),
		'sale'      => ! empty( $_GET['nx_sale'] ),
		'stock'     => ! empty( $_GET['nx_stock'] ),
		'min_price' => isset( $_GET['min_price'] ) && '' !== $_GET['min_price'] ? absint( $_GET['min_price'] ) : '',
		'max_price' => isset( $_GET['max_price'] ) && '' !== $_GET['max_price'] ? absint( $_GET['max_price'] ) : '',
	);
	// phpcs:enable
	return $state;
}

/**
 * Apply our filters to the main product query.
 */
add_action(
	'woocommerce_product_query',
	function ( $q ) {
		$s   = nexus_filter_state();
		$tax = (array) $q->get( 'tax_query' );
		if ( $s['brand'] && taxonomy_exists( 'product_brand' ) ) {
			$tax[] = array( 'taxonomy' => 'product_brand', 'field' => 'slug', 'terms' => $s['brand'], 'operator' => 'IN' );
		}
		if ( $s['tag'] ) {
			$tax[] = array( 'taxonomy' => 'product_tag', 'field' => 'slug', 'terms' => $s['tag'], 'operator' => 'IN' );
		}
		$q->set( 'tax_query', $tax );
		if ( $s['sale'] ) {
			$ids = wc_get_product_ids_on_sale();
			$in  = $q->get( 'post__in' );
			$ids = $in ? array_intersect( $in, $ids ) : $ids;
			$q->set( 'post__in', $ids ? array_values( $ids ) : array( 0 ) );
		}
		if ( $s['stock'] ) {
			$meta   = (array) $q->get( 'meta_query' );
			$meta[] = array( 'key' => '_stock_status', 'value' => 'instock' );
			$q->set( 'meta_query', $meta );
		}
	}
);

/**
 * URL of the current archive with some filter keys removed.
 *
 * @param array $remove Keys to drop.
 * @return string
 */
function nexus_filter_url( $remove = array() ) {
	return remove_query_arg( array_merge( $remove, array( 'paged', 'product-page' ) ) );
}

/**
 * The filter panel.
 */
function nexus_filter_panel() {
	$s      = nexus_filter_state();
	$brands = taxonomy_exists( 'product_brand' ) ? get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => true, 'number' => 40 ) ) : array();
	$tags   = get_terms( array( 'taxonomy' => 'product_tag', 'hide_empty' => true, 'number' => 30, 'orderby' => 'count', 'order' => 'DESC' ) );
	$action = is_shop() ? wc_get_page_permalink( 'shop' ) : get_term_link( get_queried_object() );
	?>
	<aside class="nx-filters" id="nx-filters" aria-labelledby="nx-filters-h">
		<div class="nx-filters__head">
			<h2 id="nx-filters-h"><?php esc_html_e( 'Filter', 'nexus-beauty' ); ?></h2>
			<a class="nx-link-sm" href="<?php echo esc_url( nexus_filter_url( array( 'nx_brand', 'nx_tag', 'nx_sale', 'nx_stock', 'min_price', 'max_price' ) ) ); ?>"><?php esc_html_e( 'Clear all', 'nexus-beauty' ); ?></a>
			<button class="nx-icon-btn nx-filters__close" type="button" data-nx-close aria-label="<?php esc_attr_e( 'Close filters', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'close' ); // phpcs:ignore ?></button>
		</div>
		<form class="nx-filters__form" method="get" action="<?php echo esc_url( is_wp_error( $action ) ? '' : $action ); ?>" data-nx-filter-form>
			<?php
			// Keep sorting and search when filtering.
			foreach ( array( 'orderby', 's', 'post_type' ) as $keep ) {
				if ( ! empty( $_GET[ $keep ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					printf( '<input type="hidden" name="%s" value="%s">', esc_attr( $keep ), esc_attr( sanitize_text_field( wp_unslash( $_GET[ $keep ] ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				}
			}
			?>
			<fieldset class="nx-fgroup">
				<legend class="nx-fgroup__title"><?php esc_html_e( 'Price', 'nexus-beauty' ); ?></legend>
				<div class="nx-price-row">
					<label><span class="screen-reader-text"><?php esc_html_e( 'Minimum price', 'nexus-beauty' ); ?></span><input type="number" name="min_price" min="0" inputmode="numeric" placeholder="<?php esc_attr_e( 'Min', 'nexus-beauty' ); ?>" value="<?php echo esc_attr( $s['min_price'] ); ?>"></label>
					<span aria-hidden="true">–</span>
					<label><span class="screen-reader-text"><?php esc_html_e( 'Maximum price', 'nexus-beauty' ); ?></span><input type="number" name="max_price" min="0" inputmode="numeric" placeholder="<?php esc_attr_e( 'Max', 'nexus-beauty' ); ?>" value="<?php echo esc_attr( $s['max_price'] ); ?>"></label>
				</div>
				<div class="nx-price-presets">
					<?php
					$presets = apply_filters( 'nexus_price_presets', array( array( 0, 999 ), array( 1000, 2999 ), array( 3000, 4999 ), array( 5000, '' ) ) );
					foreach ( $presets as $p ) {
						$label = '' === $p[1] ? sprintf( '%s+', nexus_price_text( $p[0] ) ) : ( 0 === $p[0] ? sprintf( /* translators: %s: price */ __( 'Under %s', 'nexus-beauty' ), nexus_price_text( $p[1] + 1 ) ) : nexus_price_text( $p[0] ) . ' – ' . nexus_price_text( $p[1] ) );
						printf( '<button type="button" class="nx-chip nx-chip--sm" data-nx-price="%s,%s">%s</button>', esc_attr( $p[0] ), esc_attr( $p[1] ), esc_html( $label ) );
					}
					?>
				</div>
			</fieldset>
			<fieldset class="nx-fgroup">
				<legend class="nx-fgroup__title"><?php esc_html_e( 'Show only', 'nexus-beauty' ); ?></legend>
				<label class="nx-check"><input type="checkbox" name="nx_sale" value="1" <?php checked( $s['sale'] ); ?>><span><?php esc_html_e( 'On offer', 'nexus-beauty' ); ?></span></label>
				<label class="nx-check"><input type="checkbox" name="nx_stock" value="1" <?php checked( $s['stock'] ); ?>><span><?php esc_html_e( 'In stock', 'nexus-beauty' ); ?></span></label>
			</fieldset>
			<?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
				<fieldset class="nx-fgroup">
					<legend class="nx-fgroup__title"><?php esc_html_e( 'Concern & type', 'nexus-beauty' ); ?></legend>
					<div class="nx-fgroup__scroll">
						<?php foreach ( $tags as $t ) : ?>
							<label class="nx-check"><input type="checkbox" name="nx_tag[]" value="<?php echo esc_attr( $t->slug ); ?>" <?php checked( in_array( $t->slug, $s['tag'], true ) ); ?>><span><?php echo esc_html( $t->name ); ?></span><span class="nx-check__n"><?php echo esc_html( $t->count ); ?></span></label>
						<?php endforeach; ?>
					</div>
				</fieldset>
			<?php endif; ?>
			<?php if ( $brands && ! is_wp_error( $brands ) ) : ?>
				<fieldset class="nx-fgroup">
					<legend class="nx-fgroup__title"><?php esc_html_e( 'Brand', 'nexus-beauty' ); ?></legend>
					<div class="nx-fgroup__scroll">
						<?php foreach ( $brands as $b ) : ?>
							<label class="nx-check"><input type="checkbox" name="nx_brand[]" value="<?php echo esc_attr( $b->slug ); ?>" <?php checked( in_array( $b->slug, $s['brand'], true ) ); ?>><span><?php echo esc_html( $b->name ); ?></span><span class="nx-check__n"><?php echo esc_html( $b->count ); ?></span></label>
						<?php endforeach; ?>
					</div>
				</fieldset>
			<?php endif; ?>
			<div class="nx-filters__apply"><button class="nx-btn nx-btn--primary nx-btn--block" type="submit"><?php esc_html_e( 'Show products', 'nexus-beauty' ); ?></button></div>
		</form>
		<?php if ( is_active_sidebar( 'nexus-shop-filters' ) ) : ?>
			<div class="nx-filters__widgets"><?php dynamic_sidebar( 'nexus-shop-filters' ); ?></div>
		<?php endif; ?>
	</aside>
	<?php
}

/**
 * Active filter chips with remove links.
 */
function nexus_active_filters() {
	$s     = nexus_filter_state();
	$chips = array();
	foreach ( $s['brand'] as $slug ) {
		$t       = get_term_by( 'slug', $slug, 'product_brand' );
		$chips[] = array( $t ? $t->name : $slug, add_query_arg( 'nx_brand', array_values( array_diff( $s['brand'], array( $slug ) ) ), nexus_filter_url( array( 'nx_brand' ) ) ) );
	}
	foreach ( $s['tag'] as $slug ) {
		$t       = get_term_by( 'slug', $slug, 'product_tag' );
		$chips[] = array( $t ? $t->name : $slug, add_query_arg( 'nx_tag', array_values( array_diff( $s['tag'], array( $slug ) ) ), nexus_filter_url( array( 'nx_tag' ) ) ) );
	}
	if ( $s['sale'] ) {
		$chips[] = array( __( 'On offer', 'nexus-beauty' ), nexus_filter_url( array( 'nx_sale' ) ) );
	}
	if ( $s['stock'] ) {
		$chips[] = array( __( 'In stock', 'nexus-beauty' ), nexus_filter_url( array( 'nx_stock' ) ) );
	}
	if ( '' !== $s['min_price'] || '' !== $s['max_price'] ) {
		$chips[] = array( trim( ( '' !== $s['min_price'] ? nexus_price_text( $s['min_price'] ) : '' ) . ' – ' . ( '' !== $s['max_price'] ? nexus_price_text( $s['max_price'] ) : '' ) ), nexus_filter_url( array( 'min_price', 'max_price' ) ) );
	}
	if ( ! $chips ) {
		return;
	}
	echo '<div class="nx-active" aria-label="' . esc_attr__( 'Active filters', 'nexus-beauty' ) . '">';
	foreach ( $chips as $c ) {
		/* translators: %s: filter name */
		printf( '<a class="nx-active__chip" href="%s" aria-label="%s">%s ×</a>', esc_url( $c[1] ), esc_attr( sprintf( __( 'Remove filter: %s', 'nexus-beauty' ), $c[0] ) ), esc_html( $c[0] ) );
	}
	echo '</div>';
}

// Two-column layout: filters on the left, products on the right.
add_action(
	'woocommerce_before_shop_loop',
	function () {
		if ( ! ( is_shop() || is_product_taxonomy() ) ) {
			return;
		}
		echo '<div class="nx-shop">';
		nexus_filter_panel();
		echo '<div class="nx-shop__main"><div class="nx-toolbar"><button class="nx-btn nx-btn--ghost nx-filter-toggle" type="button" data-nx-open="nx-filters">' . nexus_icon( 'filter' ) . esc_html__( 'Filter', 'nexus-beauty' ) . '</button>'; // phpcs:ignore
	},
	8
);
add_action(
	'woocommerce_before_shop_loop',
	function () {
		if ( ! ( is_shop() || is_product_taxonomy() ) ) {
			return;
		}
		echo '</div>';
		nexus_active_filters();
	},
	35
);
add_action(
	'woocommerce_after_shop_loop',
	function () {
		if ( is_shop() || is_product_taxonomy() ) {
			echo '</div></div>';
		}
	},
	99
);

// When filters match nothing, offer a way back.
add_action(
	'woocommerce_no_products_found',
	function () {
		$s = nexus_filter_state();
		if ( $s['brand'] || $s['tag'] || $s['sale'] || $s['stock'] || '' !== $s['min_price'] || '' !== $s['max_price'] ) {
			printf( '<p class="nx-empty-note"><a class="nx-btn nx-btn--dark" href="%s">%s</a></p>', esc_url( nexus_filter_url( array( 'nx_brand', 'nx_tag', 'nx_sale', 'nx_stock', 'min_price', 'max_price' ) ) ), esc_html__( 'Clear all filters', 'nexus-beauty' ) );
		}
	},
	20
);

// Filtered pages shouldn't be indexed separately.
add_filter(
	'wp_robots',
	function ( $robots ) {
		$s = function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ? nexus_filter_state() : null;
		if ( $s && ( $s['brand'] || $s['tag'] || $s['sale'] || $s['stock'] || '' !== $s['min_price'] || '' !== $s['max_price'] ) ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
		}
		return $robots;
	}
);
