<?php
/**
 * Product cards (the design's .card), badges, the shop grid, filters, sorting and pagination.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

/* ---------- Badges ---------- */

/**
 * Badge labels and pill styles for product tags (slug => [label, pill modifier]).
 * Tag a product with one of these slugs to show the badge on its card.
 *
 * @return array
 */
function nexus_tag_badges() {
	return apply_filters(
		'nexus_tag_badges',
		array(
			'our-label'  => array( __( 'Our label', 'nexus-beauty' ), 'own' ),
			'mini'       => array( __( 'Mini', 'nexus-beauty' ), 'mini' ),
			'derm'       => array( __( 'Derm brand', 'nexus-beauty' ), 'derm' ),
			'bestseller' => array( __( 'Bestseller', 'nexus-beauty' ), '' ),
			'korea'      => array( __( 'Korea', 'nexus-beauty' ), 'intl' ),
			'imported'   => array( __( 'Imported', 'nexus-beauty' ), 'intl' ),
			'pakistani'  => array( __( 'Pakistani', 'nexus-beauty' ), 'local' ),
		)
	);
}

/**
 * Tag slugs that are badges or groupings rather than concerns.
 *
 * @return array
 */
function nexus_badge_slugs() {
	return array_merge( array_keys( nexus_tag_badges() ), array( 'new', 'featured', 'men', 'lips', 'made-in-pakistan' ) );
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
 * Whether a product counts as new.
 *
 * @param WC_Product $product Product.
 * @return bool
 */
function nexus_is_new( $product ) {
	$created = $product->get_date_created();
	return $created && ( time() - $created->getTimestamp() ) < DAY_IN_SECONDS * (int) nexus_opt( 'new_days' );
}

/**
 * Badges for a product, in the design's order.
 *
 * @param WC_Product $product Product.
 * @param int        $max     Maximum badges.
 * @return array [ [label, modifier], ... ]
 */
function nexus_badges( $product, $max = 3 ) {
	$out  = array();
	$tags = array();
	foreach ( (array) get_the_terms( $product->get_id(), 'product_tag' ) as $term ) {
		if ( $term instanceof WP_Term ) {
			$tags[] = $term->slug;
		}
	}
	if ( ! $product->is_in_stock() ) {
		$out[] = array( __( 'Sold out', 'nexus-beauty' ), 'out' );
	}
	$map    = nexus_tag_badges();
	$origin = array();
	foreach ( $map as $slug => $b ) {
		if ( ! in_array( $slug, $tags, true ) ) {
			continue;
		}
		if ( in_array( $b[1], array( 'intl', 'local' ), true ) ) {
			$origin[] = $b;
		} else {
			$out[] = $b;
		}
	}
	if ( nexus_is_new( $product ) || in_array( 'new', $tags, true ) ) {
		$out[] = array( __( 'New', 'nexus-beauty' ), 'new' );
	} elseif ( nexus_sale_percent( $product ) > 0 ) {
		/* translators: %d: discount percentage */
		$out[] = array( sprintf( __( '−%d%%', 'nexus-beauty' ), nexus_sale_percent( $product ) ), 'sale' );
	}
	if ( $product->managing_stock() && $product->is_in_stock() ) {
		$qty = (int) $product->get_stock_quantity();
		if ( $qty > 0 && $qty <= (int) nexus_opt( 'low_stock' ) ) {
			/* translators: %d: items left */
			$out[] = array( sprintf( __( 'Only %d left', 'nexus-beauty' ), $qty ), 'low' );
		}
	}
	$out = array_merge( $out, $origin );
	return array_slice( $out, 0, $max );
}

/**
 * Badge pills HTML.
 *
 * @param WC_Product $product Product.
 * @param array      $extra   Extra badges to add first.
 * @return string
 */
function nexus_badges_html( $product, $extra = array() ) {
	$badges = array_slice( array_merge( $extra, nexus_badges( $product ) ), 0, 3 + count( $extra ) );
	if ( ! $badges ) {
		return '';
	}
	$out = '<div class="card__badges">';
	foreach ( $badges as $b ) {
		$out .= '<span class="pill ' . ( $b[1] ? 'pill--' . esc_attr( $b[1] ) : '' ) . '">' . esc_html( $b[0] ) . '</span>';
	}
	return $out . '</div>';
}

/* ---------- Look: illustration and colours ---------- */

/**
 * The product's top-level category.
 *
 * @param WC_Product $product Product.
 * @return WP_Term|null
 */
function nexus_top_cat( $product ) {
	$id    = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
	$terms = get_the_terms( $id, 'product_cat' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return null;
	}
	$default = (int) get_option( 'default_product_cat' );
	foreach ( $terms as $t ) {
		if ( $t->term_id === $default && count( $terms ) > 1 ) {
			continue;
		}
		while ( $t->parent ) {
			$p = get_term( $t->parent, 'product_cat' );
			if ( ! $p || is_wp_error( $p ) ) {
				break;
			}
			$t = $p;
		}
		return $t;
	}
	return null;
}

/**
 * Illustration shape and colours for a product: its own settings, or its category's.
 *
 * @param WC_Product $product Product.
 * @return array [ shape, tone, bg ]
 */
function nexus_product_look( $product ) {
	$look  = nexus_cat_look( nexus_top_cat( $product ) );
	$shape = nexus_meta( $product, '_nx_shape' );
	$tone  = nexus_meta( $product, '_nx_tone' );
	$bg    = nexus_meta( $product, '_nx_bg' );
	return array(
		in_array( $shape, nexus_shapes(), true ) ? $shape : $look[0],
		sanitize_hex_color( $tone ) ? $tone : $look[1],
		sanitize_hex_color( $bg ) ? $bg : $look[2],
	);
}

/**
 * Inline style with the product's colours.
 *
 * @param array $look [ shape, tone, bg ].
 * @return string
 */
function nexus_look_style( $look ) {
	return '--tone:' . $look[1] . ';--bg:' . $look[2];
}

/**
 * Product image, or the design's illustration when the product has no image.
 *
 * @param WC_Product $product Product.
 * @param string     $size    Image size.
 * @param array      $attr    Image attributes.
 * @return string
 */
function nexus_product_visual( $product, $size = 'woocommerce_thumbnail', $attr = array() ) {
	$img_id = $product->get_image_id();
	if ( ! $img_id && $product->get_parent_id() ) {
		$parent = wc_get_product( $product->get_parent_id() );
		$img_id = $parent ? $parent->get_image_id() : 0;
	}
	if ( $img_id ) {
		return wp_get_attachment_image( $img_id, $size, false, array_merge( array( 'alt' => $product->get_name() ), $attr ) );
	}
	$look = nexus_product_look( $product );
	return nexus_shape_svg( $look[0] );
}

/**
 * Product name without the brand in front (the card shows the brand above the name, as in the design).
 *
 * @param WC_Product $product Product.
 * @param string     $brand   Brand name.
 * @return string
 */
function nexus_short_name( $product, $brand ) {
	$name = $product->get_name();
	if ( ! $brand ) {
		return $name;
	}
	$candidates = array_unique( array( $brand, trim( preg_replace( '/\s+by\s+.*$/i', '', $brand ) ) ) );
	foreach ( $candidates as $c ) {
		if ( $c && 0 === stripos( $name, $c . ' ' ) ) {
			$rest = trim( substr( $name, strlen( $c ) ) );
			return $rest ? $rest : $name;
		}
	}
	return $name;
}

/* ---------- Prices ---------- */

/**
 * Price parts for display.
 *
 * @param WC_Product $product Product.
 * @return array [ now (float), was (float|0), from (bool) ]
 */
function nexus_price_parts( $product ) {
	if ( $product->is_type( 'variable' ) ) {
		$min     = (float) $product->get_variation_price( 'min', true );
		$max     = (float) $product->get_variation_price( 'max', true );
		$reg_min = (float) $product->get_variation_regular_price( 'min', true );
		return array( $min, ( $product->is_on_sale() && $reg_min > $min ) ? $reg_min : 0, $min !== $max );
	}
	if ( $product->is_type( 'grouped' ) ) {
		$prices = array();
		foreach ( $product->get_children() as $child_id ) {
			$child = wc_get_product( $child_id );
			if ( $child && '' !== $child->get_price() ) {
				$prices[] = (float) wc_get_price_to_display( $child );
			}
		}
		return array( $prices ? min( $prices ) : 0, 0, count( $prices ) > 1 );
	}
	$now = (float) wc_get_price_to_display( $product );
	$was = $product->is_on_sale() ? (float) wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ) : 0;
	return array( $now, $was > $now ? $was : 0, false );
}

/**
 * The design's price markup.
 *
 * @param WC_Product $product Product.
 * @param bool       $save    Show "Save x%".
 * @param string     $attr    Extra attributes for the "now" span.
 * @return string
 */
function nexus_price_html( $product, $save = false, $attr = '' ) {
	if ( '' === $product->get_price() && ! $product->is_type( array( 'variable', 'grouped' ) ) ) {
		return '';
	}
	list( $now, $was, $from ) = nexus_price_parts( $product );
	$out  = '<p class="price">';
	$out .= '<span class="price__now"' . $attr . '>' . ( $from ? esc_html__( 'From', 'nexus-beauty' ) . ' ' : '' ) . esc_html( nexus_money( $now ) ) . '</span>';
	if ( $was ) {
		$out .= '<span class="price__was" data-pdp-was><span class="sr-only">' . esc_html__( 'Was', 'nexus-beauty' ) . ' </span>' . esc_html( nexus_money( $was ) ) . '</span>';
		if ( $save ) {
			/* translators: %d: percent */
			$out .= '<span class="price__save" data-pdp-save>' . esc_html( sprintf( __( 'Save %d%%', 'nexus-beauty' ), round( ( 1 - $now / $was ) * 100 ) ) ) . '</span>';
		}
	} elseif ( $save ) {
		$out .= '<span class="price__was" data-pdp-was hidden></span><span class="price__save" data-pdp-save hidden></span>';
	}
	return $out . '</p>';
}

/* ---------- Card ---------- */

/**
 * Add-to-bag control for a card.
 *
 * @param WC_Product $product Product.
 * @return string
 */
function nexus_card_button( $product ) {
	if ( ! $product->is_in_stock() ) {
		return '<span class="btn btn--ghost btn--block" aria-disabled="true">' . esc_html__( 'Sold out', 'nexus-beauty' ) . '</span>';
	}
	if ( $product->is_type( 'simple' ) && $product->is_purchasable() ) {
		$ajax = 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' ) ? ' ajax_add_to_cart' : '';
		return sprintf(
			'<a href="%1$s" data-quantity="1" class="btn btn--dark btn--block add_to_cart_button%2$s" data-product_id="%3$d" data-product_sku="%4$s" aria-label="%5$s" rel="nofollow">%6$s</a>',
			esc_url( $product->add_to_cart_url() ),
			$ajax,
			$product->get_id(),
			esc_attr( $product->get_sku() ),
			/* translators: %s: product name */
			esc_attr( sprintf( __( 'Add %s to your bag', 'nexus-beauty' ), $product->get_name() ) ),
			esc_html__( 'Add to bag', 'nexus-beauty' )
		);
	}
	$label = $product->is_type( 'variable' ) ? __( 'Choose options', 'nexus-beauty' ) : ( $product->is_type( 'external' ) ? $product->single_add_to_cart_text() : __( 'View product', 'nexus-beauty' ) );
	return '<a class="btn btn--dark btn--block" href="' . esc_url( $product->get_permalink() ) . '">' . esc_html( $label ) . '</a>';
}

/**
 * Wishlist heart.
 *
 * @param WC_Product $product Product.
 * @param string     $class   Class.
 * @return string
 */
function nexus_wish_html( $product, $class = 'card__wish' ) {
	return sprintf(
		'<button class="%1$s" type="button" data-nx-wish="%2$d" aria-pressed="false" aria-label="%3$s">%4$s</button>',
		esc_attr( $class ),
		$product->get_id(),
		/* translators: %s: product name */
		esc_attr( sprintf( __( 'Save %s to wishlist', 'nexus-beauty' ), $product->get_name() ) ),
		nexus_icon( 'heart' )
	);
}

/**
 * One product card, exactly as in the design.
 *
 * @param WC_Product $product Product.
 * @param array      $args    'top' (bool) marks a top pick for the home tabs.
 * @return string
 */
function nexus_card( $product, $args = array() ) {
	if ( ! $product instanceof WC_Product ) {
		return '';
	}
	$look            = nexus_product_look( $product );
	list( $brand )   = nexus_brand( $product );
	$top             = nexus_top_cat( $product );
	$why             = nexus_meta( $product, '_nx_tagline' );
	$size            = nexus_meta( $product, '_nx_size' );
	list( $now )     = nexus_price_parts( $product );
	$name            = nexus_short_name( $product, $brand );
	$attrs           = ' data-cat="' . esc_attr( $top ? $top->slug : '' ) . '" data-price="' . esc_attr( $now ) . '"';
	$attrs          .= ! empty( $args['top'] ) ? ' data-top' : '';
	ob_start();
	?>
	<article <?php wc_product_class( 'card', $product ); ?><?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?>>
		<div class="card__media" style="<?php echo esc_attr( nexus_look_style( $look ) ); ?>">
			<?php echo nexus_product_visual( $product, 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php echo nexus_badges_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<?php echo nexus_wish_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<div class="card__quick"><?php echo nexus_card_button( $product ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
		<?php if ( $brand ) : ?><p class="card__brand"><?php echo esc_html( $brand ); ?></p><?php endif; ?>
		<h3 class="card__title"><a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $name ); ?></a></h3>
		<?php if ( $why ) : ?><p class="card__why"><?php echo esc_html( $why ); ?></p><?php endif; ?>
		<div class="card__meta"><?php echo nexus_price_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php if ( $size ) : ?><span class="card__size"><?php echo esc_html( $size ); ?></span><?php endif; ?></div>
	</article>
	<?php
	return ob_get_clean();
}

/**
 * Cards for a list of products.
 *
 * @param WC_Product[] $products Products.
 * @param string       $class    Grid class.
 * @param array        $top_ids  IDs to mark as top picks.
 * @param string       $attrs    Extra attribute name (e.g. data-tab-grid).
 * @return string
 */
function nexus_cards_grid( $products, $class = 'grid', $top_ids = array(), $attrs = '' ) {
	$out = '';
	foreach ( $products as $p ) {
		$out .= nexus_card( $p, array( 'top' => in_array( $p->get_id(), $top_ids, true ) ) );
	}
	return $out ? '<div class="' . esc_attr( $class ) . '"' . ( $attrs ? ' ' . esc_attr( $attrs ) : '' ) . '>' . $out . '</div>' : '';
}

// WooCommerce loops (shortcodes, related products, the wishlist) use the design grid.
add_filter(
	'woocommerce_product_loop_start',
	function () {
		$cols = (int) wc_get_loop_prop( 'columns', 4 );
		$mod  = 4 === $cols ? '' : ' grid--' . max( 2, min( 6, $cols ) );
		return '<div class="grid' . $mod . ' products">';
	}
);
add_filter(
	'woocommerce_product_loop_end',
	function () {
		return '</div>';
	}
);

// Product images: packshots sit on the design's tinted card, photos fill it.
add_filter(
	'body_class',
	function ( $classes ) {
		if ( 'photo' === nexus_opt( 'image_style' ) ) {
			$classes[] = 'nx-photo';
		}
		return $classes;
	}
);

// Clearer sort labels.
add_filter(
	'woocommerce_catalog_orderby',
	function ( $options ) {
		$labels = array(
			'menu_order' => __( 'Sort: Featured', 'nexus-beauty' ),
			'popularity' => __( 'Best sellers', 'nexus-beauty' ),
			'rating'     => __( 'Top rated', 'nexus-beauty' ),
			'date'       => __( 'Newest', 'nexus-beauty' ),
			'price'      => __( 'Price: low to high', 'nexus-beauty' ),
			'price-desc' => __( 'Price: high to low', 'nexus-beauty' ),
		);
		foreach ( $options as $k => $v ) {
			if ( isset( $labels[ $k ] ) ) {
				$options[ $k ] = $labels[ $k ];
			}
		}
		return $options;
	}
);

// Clear stock wording on product pages.
add_filter(
	'woocommerce_get_availability_text',
	function ( $text, $product ) {
		if ( ! $product->is_in_stock() ) {
			return __( 'Sold out', 'nexus-beauty' );
		}
		if ( $product->is_on_backorder( 1 ) ) {
			return __( 'On backorder, ships in 7–10 days', 'nexus-beauty' );
		}
		if ( $product->managing_stock() ) {
			$qty = (int) $product->get_stock_quantity();
			if ( $qty > 0 && $qty <= (int) nexus_opt( 'low_stock' ) ) {
				/* translators: %d: items left */
				return sprintf( __( 'Only %d left, ready to ship', 'nexus-beauty' ), $qty );
			}
		}
		return __( 'In stock, ready to ship', 'nexus-beauty' );
	},
	10,
	2
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
	$arr   = function ( $key ) {
		if ( empty( $_GET[ $key ] ) ) {
			return array();
		}
		$raw = is_array( $_GET[ $key ] ) ? wp_unslash( $_GET[ $key ] ) : explode( ',', wp_unslash( $_GET[ $key ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		return array_values( array_filter( array_map( 'sanitize_title', (array) $raw ) ) );
	};
	$range = nexus_price_bounds();
	$max   = isset( $_GET['max_price'] ) && '' !== $_GET['max_price'] ? absint( $_GET['max_price'] ) : '';
	$state = array(
		'cat'       => $arr( 'nx_cat' ),
		'origin'    => array_values( array_intersect( $arr( 'nx_origin' ), array( 'local', 'intl' ) ) ),
		'brand'     => $arr( 'nx_brand' ),
		'tag'       => $arr( 'nx_tag' ),
		'sale'      => ! empty( $_GET['nx_sale'] ),
		'stock'     => ! empty( $_GET['nx_stock'] ),
		'min_price' => isset( $_GET['min_price'] ) && '' !== $_GET['min_price'] ? absint( $_GET['min_price'] ) : '',
		'max_price' => ( '' !== $max && $max < $range[1] ) ? $max : '',
	);
	// phpcs:enable
	return $state;
}

/**
 * Whether any filter is on.
 *
 * @return bool
 */
function nexus_filters_active() {
	$s = nexus_filter_state();
	return $s['cat'] || $s['origin'] || $s['brand'] || $s['tag'] || $s['sale'] || $s['stock'] || '' !== $s['min_price'] || '' !== $s['max_price'];
}

/**
 * Lowest and highest product price in the store (cached), rounded for the slider.
 *
 * @return array [ min, max ]
 */
function nexus_price_bounds() {
	$cached = get_transient( 'nexus_price_bounds' );
	if ( is_array( $cached ) ) {
		return $cached;
	}
	global $wpdb;
	$row = $wpdb->get_row( "SELECT MIN(min_price) AS lo, MAX(max_price) AS hi FROM {$wpdb->prefix}wc_product_meta_lookup WHERE max_price > 0" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$lo  = $row && $row->lo ? floor( (float) $row->lo / 100 ) * 100 : 0;
	$hi  = $row && $row->hi ? ceil( (float) $row->hi / 500 ) * 500 : 10000;
	$out = array( (int) $lo, (int) max( $hi, $lo + 500 ) );
	set_transient( 'nexus_price_bounds', $out, DAY_IN_SECONDS );
	return $out;
}
add_action(
	'woocommerce_update_product',
	function () {
		delete_transient( 'nexus_price_bounds' );
	}
);

/**
 * Brand term IDs with a given origin (Products > Brands > edit > Origin).
 *
 * @param array $origins local / intl.
 * @return int[]
 */
function nexus_brands_by_origin( $origins ) {
	if ( ! taxonomy_exists( 'product_brand' ) || ! $origins ) {
		return array();
	}
	return get_terms(
		array(
			'taxonomy'   => 'product_brand',
			'hide_empty' => false,
			'fields'     => 'ids',
			'meta_query' => array( array( 'key' => 'nx_origin', 'value' => $origins, 'compare' => 'IN' ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
}

// Apply the filters to the main product query.
add_action(
	'woocommerce_product_query',
	function ( $q ) {
		$s   = nexus_filter_state();
		$tax = (array) $q->get( 'tax_query' );
		if ( $s['cat'] ) {
			$tax[] = array( 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $s['cat'], 'operator' => 'IN' );
		}
		if ( $s['brand'] && taxonomy_exists( 'product_brand' ) ) {
			$tax[] = array( 'taxonomy' => 'product_brand', 'field' => 'slug', 'terms' => $s['brand'], 'operator' => 'IN' );
		}
		if ( $s['origin'] ) {
			$ids   = nexus_brands_by_origin( $s['origin'] );
			$tax[] = array( 'taxonomy' => 'product_brand', 'field' => 'term_id', 'terms' => $ids ? $ids : array( 0 ), 'operator' => 'IN' );
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
 * Concern tags (product tags that aren't badges), most used first.
 *
 * @param int $limit Limit.
 * @return WP_Term[]
 */
function nexus_concern_tags( $limit = 30 ) {
	$tags = get_terms( array( 'taxonomy' => 'product_tag', 'hide_empty' => true, 'number' => $limit + 12, 'orderby' => 'count', 'order' => 'DESC' ) );
	if ( ! $tags || is_wp_error( $tags ) ) {
		return array();
	}
	$skip = nexus_badge_slugs();
	$out  = array();
	foreach ( $tags as $t ) {
		if ( ! in_array( $t->slug, $skip, true ) ) {
			$out[] = $t;
		}
	}
	return array_slice( $out, 0, $limit );
}

/**
 * One group of filter checkboxes.
 *
 * @param string $legend  Title.
 * @param string $name    Field name (without []).
 * @param array  $options [ [value, label, count], ... ].
 * @param array  $checked Checked values.
 */
function nexus_filter_group( $legend, $name, $options, $checked ) {
	if ( ! $options ) {
		return;
	}
	echo '<fieldset class="fgroup"><legend>' . esc_html( $legend ) . '</legend>' . ( count( $options ) > 8 ? '<div class="fgroup__scroll">' : '' );
	foreach ( $options as $o ) {
		printf(
			'<label class="check"><input type="checkbox" name="%1$s[]" value="%2$s"%3$s> %4$s%5$s</label>',
			esc_attr( $name ),
			esc_attr( $o[0] ),
			checked( in_array( (string) $o[0], $checked, true ), true, false ),
			esc_html( $o[1] ),
			'' !== (string) $o[2] ? '<span>' . esc_html( $o[2] ) . '</span>' : ''
		);
	}
	echo ( count( $options ) > 8 ? '</div>' : '' ) . '</fieldset>';
}

/**
 * The filter panel (design: aside.filters).
 */
function nexus_filter_panel() {
	$s      = nexus_filter_state();
	$action = is_shop() ? wc_get_page_permalink( 'shop' ) : get_term_link( get_queried_object() );
	$action = is_wp_error( $action ) ? '' : $action;

	// Categories: top-level on the shop, sub-categories on a category page.
	$current = is_product_category() ? get_queried_object() : null;
	$cats    = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => $current ? $current->term_id : 0,
			'hide_empty' => true,
			'orderby'    => 'menu_order',
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
		)
	);
	$cat_opts = array();
	foreach ( is_wp_error( $cats ) ? array() : $cats as $c ) {
		$cat_opts[] = array( $c->slug, $c->name, $c->count );
	}

	$brand_opts  = array();
	$origin_opts = array();
	if ( taxonomy_exists( 'product_brand' ) && ! is_tax( 'product_brand' ) ) {
		$brands = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => true, 'number' => 60 ) );
		$counts = array( 'local' => 0, 'intl' => 0 );
		foreach ( is_wp_error( $brands ) ? array() : $brands as $b ) {
			$brand_opts[] = array( $b->slug, $b->name, $b->count );
			$o            = get_term_meta( $b->term_id, 'nx_origin', true );
			if ( isset( $counts[ $o ] ) ) {
				$counts[ $o ]++;
			}
		}
		if ( $counts['local'] && $counts['intl'] ) {
			$origin_opts = array( array( 'local', __( 'Local brands', 'nexus-beauty' ), $counts['local'] ), array( 'intl', __( 'International brands', 'nexus-beauty' ), $counts['intl'] ) );
		}
	}
	$tag_opts = array();
	foreach ( nexus_concern_tags( 20 ) as $t ) {
		$tag_opts[] = array( $t->slug, $t->name, $t->count );
	}
	$range = nexus_price_bounds();
	$max   = '' !== $s['max_price'] ? $s['max_price'] : $range[1];
	$step  = $range[1] > 5000 ? 100 : 50;
	?>
	<aside class="filters" id="nx-filters" aria-label="<?php esc_attr_e( 'Filters', 'nexus-beauty' ); ?>">
		<div class="filters__head"><h2><?php esc_html_e( 'Filter', 'nexus-beauty' ); ?></h2><div style="display:flex;gap:12px;align-items:center"><a class="linkish" href="<?php echo esc_url( nexus_filter_url( array( 'nx_cat', 'nx_origin', 'nx_brand', 'nx_tag', 'nx_sale', 'nx_stock', 'min_price', 'max_price' ) ) ); ?>"><?php esc_html_e( 'Clear all', 'nexus-beauty' ); ?></a><button class="icon-btn filter-toggle" type="button" data-close aria-label="<?php esc_attr_e( 'Close filters', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'close' ); // phpcs:ignore ?></button></div></div>
		<form method="get" action="<?php echo esc_url( $action ); ?>" data-nx-filter-form>
			<?php
			foreach ( array( 'orderby', 's', 'post_type' ) as $keep ) {
				if ( ! empty( $_GET[ $keep ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					printf( '<input type="hidden" name="%s" value="%s">', esc_attr( $keep ), esc_attr( sanitize_text_field( wp_unslash( $_GET[ $keep ] ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				}
			}
			nexus_filter_group( $current ? __( 'Type', 'nexus-beauty' ) : __( 'Category', 'nexus-beauty' ), 'nx_cat', $cat_opts, $s['cat'] );
			nexus_filter_group( __( 'Brand origin', 'nexus-beauty' ), 'nx_origin', $origin_opts, $s['origin'] );
			nexus_filter_group( __( 'Brand', 'nexus-beauty' ), 'nx_brand', $brand_opts, $s['brand'] );
			nexus_filter_group( __( 'Concern', 'nexus-beauty' ), 'nx_tag', $tag_opts, $s['tag'] );
			?>
			<fieldset class="fgroup"><legend><?php esc_html_e( 'Show only', 'nexus-beauty' ); ?></legend>
				<label class="check"><input type="checkbox" name="nx_sale" value="1"<?php checked( $s['sale'] ); ?>> <?php esc_html_e( 'On offer', 'nexus-beauty' ); ?></label>
				<label class="check"><input type="checkbox" name="nx_stock" value="1"<?php checked( $s['stock'] ); ?>> <?php esc_html_e( 'In stock', 'nexus-beauty' ); ?></label>
			</fieldset>
			<div class="fgroup range"><p><?php esc_html_e( 'Price', 'nexus-beauty' ); ?></p><label for="price-max"><?php esc_html_e( 'Up to', 'nexus-beauty' ); ?> <output id="price-out" for="price-max"><?php echo esc_html( nexus_money( $max ) ); ?></output></label>
				<input id="price-max" name="max_price" type="range" min="<?php echo esc_attr( $range[0] ); ?>" max="<?php echo esc_attr( $range[1] ); ?>" step="<?php echo esc_attr( $step ); ?>" value="<?php echo esc_attr( $max ); ?>" data-max="<?php echo esc_attr( $range[1] ); ?>"></div>
			<div class="filters__apply"><button class="btn btn--primary btn--block" type="submit"><?php esc_html_e( 'Show products', 'nexus-beauty' ); ?></button></div>
		</form>
		<?php if ( is_active_sidebar( 'nexus-shop-filters' ) ) : ?>
			<?php dynamic_sidebar( 'nexus-shop-filters' ); ?>
		<?php endif; ?>
	</aside>
	<?php
}

/**
 * Active filter chips with remove links (design: .active-filters).
 */
function nexus_active_filters() {
	$s     = nexus_filter_state();
	$chips = array();
	$lists = array(
		'nx_cat'    => array( $s['cat'], 'product_cat' ),
		'nx_brand'  => array( $s['brand'], 'product_brand' ),
		'nx_tag'    => array( $s['tag'], 'product_tag' ),
		'nx_origin' => array( $s['origin'], '' ),
	);
	foreach ( $lists as $key => $l ) {
		foreach ( $l[0] as $slug ) {
			if ( $l[1] ) {
				$t     = get_term_by( 'slug', $slug, $l[1] );
				$label = $t ? $t->name : $slug;
			} else {
				$label = 'local' === $slug ? __( 'Local brands', 'nexus-beauty' ) : __( 'International brands', 'nexus-beauty' );
			}
			$rest    = array_values( array_diff( $l[0], array( $slug ) ) );
			$chips[] = array( $label, $rest ? add_query_arg( $key, $rest, nexus_filter_url( array( $key ) ) ) : nexus_filter_url( array( $key ) ) );
		}
	}
	if ( $s['sale'] ) {
		$chips[] = array( __( 'On offer', 'nexus-beauty' ), nexus_filter_url( array( 'nx_sale' ) ) );
	}
	if ( $s['stock'] ) {
		$chips[] = array( __( 'In stock', 'nexus-beauty' ), nexus_filter_url( array( 'nx_stock' ) ) );
	}
	if ( '' !== $s['max_price'] || '' !== $s['min_price'] ) {
		/* translators: %s: price */
		$chips[] = array( '' !== $s['max_price'] ? sprintf( __( 'Up to %s', 'nexus-beauty' ), nexus_money( $s['max_price'] ) ) : nexus_money( $s['min_price'] ) . '+', nexus_filter_url( array( 'min_price', 'max_price' ) ) );
	}
	echo '<div class="active-filters" aria-live="polite">';
	foreach ( $chips as $c ) {
		/* translators: %s: filter name */
		printf( '<a href="%s" aria-label="%s">%s ×</a>', esc_url( $c[1] ), esc_attr( sprintf( __( 'Remove filter: %s', 'nexus-beauty' ), $c[0] ) ), esc_html( $c[0] ) );
	}
	echo '</div>';
}

/**
 * The design's pager for WooCommerce archives.
 */
function nexus_shop_pager() {
	global $wp_query;
	$total   = (int) $wp_query->max_num_pages;
	$current = max( 1, (int) get_query_var( 'paged' ) );
	if ( $total < 2 ) {
		return;
	}
	$base  = esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) );
	$links = paginate_links(
		array(
			'base'      => $base,
			'format'    => '',
			'current'   => $current,
			'total'     => $total,
			'type'      => 'array',
			'prev_text' => '<span aria-hidden="true">‹</span><span class="sr-only">' . esc_html__( 'Previous page', 'nexus-beauty' ) . '</span>',
			'next_text' => '<span aria-hidden="true">›</span><span class="sr-only">' . esc_html__( 'Next page', 'nexus-beauty' ) . '</span>',
			'end_size'  => 1,
			'mid_size'  => 1,
		)
	);
	if ( $links ) {
		echo '<nav class="pager" aria-label="' . esc_attr__( 'Pagination', 'nexus-beauty' ) . '">' . implode( '', $links ) . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput -- core markup.
	}
}

/**
 * Buying guide and shopping questions under the shop (design: seo-copy + faq sections).
 */
function nexus_shop_guide() {
	$guide = apply_filters(
		'nexus_shop_guide',
		array(
			array( __( 'Choose by concern, not by hype', 'nexus-beauty' ), __( 'Start with the one thing you want to change: breakouts, dryness, dark spots or dullness. Use the Concern filter to narrow the list, then compare the key ingredient shown on each product page.', 'nexus-beauty' ) ),
			array( __( 'Local or international?', 'nexus-beauty' ), __( 'Local brands are usually priced lower and formulated for the local climate. International brands give you access to proven formulas from Korea, France and Japan. Both are sourced directly and carry the same authenticity guarantee.', 'nexus-beauty' ) ),
			array( __( 'Check size and price per ml', 'nexus-beauty' ), __( 'Larger sizes often cost less per ml. Each product page shows all available sizes and how much you save on the larger one.', 'nexus-beauty' ) ),
		)
	);
	$faq   = apply_filters(
		'nexus_shop_faq',
		array(
			array( __( 'Which skincare products should I start with?', 'nexus-beauty' ), __( 'A simple routine has three steps: a gentle cleanser, a serum for your main concern, and a broad-spectrum SPF 30 or higher in the morning. Add a moisturiser at night if your skin feels tight.', 'nexus-beauty' ) ),
			array( __( 'Is Korean skincare suitable for oily or acne-prone skin?', 'nexus-beauty' ), __( 'Yes. Many Korean products are lightweight and water-based. Look for non-comedogenic formulas with ingredients such as centella, niacinamide or salicylic acid.', 'nexus-beauty' ) ),
			array( __( 'How do I know which products are local and which are imported?', 'nexus-beauty' ), __( 'Every product card shows a "Pakistani" or "Imported" label, and you can filter by brand origin in the sidebar.', 'nexus-beauty' ) ),
		)
	);
	?>
	<section class="section section--tint" aria-labelledby="guide-h">
		<div class="wrap seo-copy">
			<h2 class="h2" id="guide-h"><?php esc_html_e( 'A quick guide to buying beauty online', 'nexus-beauty' ); ?></h2>
			<?php foreach ( $guide as $g ) : ?>
				<h3><?php echo esc_html( $g[0] ); ?></h3>
				<p><?php echo esc_html( $g[1] ); ?></p>
			<?php endforeach; ?>
		</div>
	</section>
	<section class="section" aria-labelledby="sfaq-h">
		<div class="wrap" style="display:grid;gap:24px">
			<h2 class="h2" id="sfaq-h"><?php esc_html_e( 'Shopping questions', 'nexus-beauty' ); ?></h2>
			<?php echo nexus_faq_html( $faq ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</section>
	<?php
	nexus_faq_schema( $faq );
}

// Filtered pages shouldn't be indexed separately.
add_filter(
	'wp_robots',
	function ( $robots ) {
		if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) && nexus_filters_active() ) {
			$robots['noindex'] = true;
			$robots['follow']  = true;
		}
		return $robots;
	}
);

// Default Woo archive bits we replace with the design's own.
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10 );
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description', 10 );
remove_action( 'woocommerce_no_products_found', 'wc_no_products_found', 10 );
