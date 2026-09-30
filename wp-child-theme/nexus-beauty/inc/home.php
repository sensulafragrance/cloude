<?php
/**
 * Home page sections in the design, filled automatically from the store:
 * categories -> category tiles and tabs, featured/best-selling products -> top picks,
 * newest products -> "Just arrived", products tagged "our-label" -> own-label spotlight,
 * brands -> derm corner and brand grid, product tags -> concerns.
 * Texts: Appearance > Customize > Nexus Beauty > Home page.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

/**
 * Top-level product categories in menu order.
 *
 * @param int $limit Limit.
 * @return WP_Term[]
 */
function nexus_home_cats( $limit = 8 ) {
	return nexus_sorted_cats( $limit );
}

/**
 * Short line for a category tile.
 *
 * @param WP_Term $t Category.
 * @return string
 */
function nexus_cat_short( $t ) {
	$short = get_term_meta( $t->term_id, 'nx_short', true );
	if ( $short ) {
		return $short;
	}
	return $t->description ? wp_trim_words( wp_strip_all_tags( $t->description ), 4, '' ) : '';
}

/**
 * Hero.
 */
function nexus_home_hero() {
	$checks = nexus_opt_lines( 'hero_checks' );
	$batch  = nexus_pipe( nexus_opt( 'hero_batch' ) );
	$img    = (int) nexus_opt( 'hero_image' );
	?>
	<section class="hero hero--first">
		<div class="wrap hero__grid">
			<div>
				<p class="eyebrow"><?php echo esc_html( nexus_text( 'hero_eyebrow' ) ); ?></p>
				<h1><?php echo nexus_em( nexus_text( 'hero_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in nexus_em(). ?></h1>
				<p class="lede"><?php echo esc_html( nexus_text( 'hero_text' ) ); ?></p>
				<div class="hero__ctas">
					<a class="btn btn--primary btn--lg" href="<?php echo esc_url( class_exists( 'WooCommerce' ) ? '#picks' : nexus_shop_url() ); ?>"><?php esc_html_e( 'Shop top picks', 'nexus-beauty' ); ?></a>
					<a class="btn btn--ghost btn--lg" href="#concerns"><?php esc_html_e( 'Shop by concern', 'nexus-beauty' ); ?></a>
				</div>
				<?php if ( $checks ) : ?>
					<ul class="hero__checks">
						<?php foreach ( array_slice( $checks, 0, 4 ) as $c ) : ?><li><?php echo nexus_icon( 'check' ); // phpcs:ignore ?><?php echo esc_html( $c ); ?></li><?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<div class="hero__art" aria-hidden="true">
				<div class="disc"></div>
				<?php if ( $img ) : ?>
					<?php echo wp_get_attachment_image( $img, 'large', false, array( 'class' => 'hero__img', 'alt' => '', 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
				<?php else : ?>
					<div class="p p1"><svg viewBox="0 0 100 160"><use href="#i-tube"/></svg></div>
					<div class="p p3"><svg viewBox="0 0 100 160"><use href="#i-perfume"/></svg></div>
					<div class="p p2"><svg viewBox="0 0 100 160"><use href="#i-dropper"/></svg></div>
				<?php endif; ?>
				<?php if ( $batch ) : ?>
					<div class="batch"><b>✓ <?php esc_html_e( 'Verified original', 'nexus-beauty' ); ?></b><span><?php esc_html_e( 'BATCH', 'nexus-beauty' ); ?>&nbsp;&nbsp;<?php echo esc_html( $batch[0] ); ?></span><?php if ( isset( $batch[1] ) ) : ?><span><?php esc_html_e( 'MFG', 'nexus-beauty' ); ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo esc_html( $batch[1] ); ?></span><?php endif; ?><?php if ( isset( $batch[2] ) ) : ?><span><?php esc_html_e( 'EXP', 'nexus-beauty' ); ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo esc_html( $batch[2] ); ?></span><?php endif; ?></div>
				<?php endif; ?>
				<?php if ( nexus_opt( 'hero_tag_title' ) ) : ?>
					<div class="hero__tag"><b><?php echo esc_html( nexus_text( 'hero_tag_title' ) ); ?></b><?php echo esc_html( nexus_text( 'hero_tag_text' ) ); ?></div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Trust strip.
 *
 * @param string $class Extra class.
 */
function nexus_trust_strip( $class = 'trust--after' ) {
	$free  = (float) nexus_opt( 'free_shipping' );
	$items = array(
		array( 'shield', __( 'Batch & expiry shown', 'nexus-beauty' ), __( 'On every order', 'nexus-beauty' ) ),
		array( 'cash', __( 'Cash on delivery', 'nexus-beauty' ), __( 'Anywhere in Pakistan', 'nexus-beauty' ) ),
		/* translators: %s: amount */
		$free > 0 ? array( 'truck', __( 'Free delivery', 'nexus-beauty' ), sprintf( __( 'Over %s', 'nexus-beauty' ), nexus_money( $free ) ) ) : array( 'truck', __( 'Fast delivery', 'nexus-beauty' ), __( 'All over Pakistan', 'nexus-beauty' ) ),
		array( 'chat', __( 'Free skin advice', 'nexus-beauty' ), __( 'On WhatsApp', 'nexus-beauty' ) ),
	);
	echo '<section class="trust ' . esc_attr( $class ) . '" aria-label="' . esc_attr__( 'Why shop with us', 'nexus-beauty' ) . '"><ul class="wrap">';
	foreach ( $items as $i ) {
		echo '<li>' . nexus_icon( $i[0] ) . '<div><strong>' . esc_html( $i[1] ) . '</strong><span>' . esc_html( $i[2] ) . '</span></div></li>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	echo '</ul></section>';
}

/**
 * Category tiles.
 *
 * @param WP_Term[] $cats Categories.
 * @return string
 */
function nexus_cat_tiles( $cats ) {
	if ( ! $cats ) {
		return '';
	}
	$out = '<div class="cats' . ( count( $cats ) >= 7 ? ' cats--8' : '' ) . '">';
	foreach ( $cats as $t ) {
		$look  = nexus_cat_look( $t );
		$thumb = (int) get_term_meta( $t->term_id, 'thumbnail_id', true );
		$vis   = $thumb ? wp_get_attachment_image( $thumb, 'woocommerce_thumbnail', false, array( 'alt' => '', 'loading' => 'lazy' ) ) : nexus_shape_svg( $look[0] );
		$short = nexus_cat_short( $t );
		$out  .= sprintf(
			'<a class="cat" href="%1$s"><span class="cat__img" style="--tone:%2$s;--tile:%3$s">%4$s</span><span>%5$s%6$s</span></a>',
			esc_url( get_term_link( $t ) ),
			esc_attr( $look[1] ),
			esc_attr( $look[2] ),
			$vis,
			esc_html( $t->name ),
			$short ? '<small>' . esc_html( $short ) . '</small>' : ''
		);
	}
	return $out . '</div>';
}

/**
 * Shop by category.
 *
 * @param WP_Term[] $cats Categories.
 */
function nexus_home_categories( $cats ) {
	if ( ! $cats ) {
		return;
	}
	$all = nexus_page_url( 'collections' );
	?>
	<section class="section" aria-labelledby="cat-h">
		<div class="wrap">
			<div class="sec-head"><div><p class="eyebrow"><?php esc_html_e( 'Shop by category', 'nexus-beauty' ); ?></p><h2 class="h2" id="cat-h"><?php esc_html_e( 'Everything for your routine', 'nexus-beauty' ); ?></h2></div><a class="link-arrow" href="<?php echo esc_url( $all ? $all : nexus_shop_url() ); ?>"><?php esc_html_e( 'View all', 'nexus-beauty' ); ?></a></div>
			<?php echo nexus_cat_tiles( $cats ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</section>
	<?php
}

/**
 * Top picks by category (tabs).
 *
 * @param WP_Term[] $cats Categories.
 */
function nexus_home_picks( $cats ) {
	$base = array( 'status' => 'publish', 'visibility' => 'catalog', 'stock_status' => 'instock' );
	$top  = wc_get_products( array_merge( $base, array( 'featured' => true, 'limit' => 8, 'orderby' => 'menu_order', 'order' => 'ASC' ) ) );
	if ( count( $top ) < 4 ) {
		$top = array_merge( $top, wc_get_products( array_merge( $base, array( 'tag' => array( 'bestseller' ), 'limit' => 8 ) ) ) );
	}
	if ( count( $top ) < 4 ) {
		$top = array_merge( $top, wc_get_products( array_merge( $base, array( 'limit' => 8, 'meta_key' => 'total_sales', 'orderby' => 'meta_value_num', 'order' => 'DESC' ) ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	}
	$top_ids = array_slice( array_values( array_unique( array_map( fn( $p ) => $p->get_id(), $top ) ) ), 0, 8 );
	$list    = array();
	$tabs    = array();
	foreach ( $cats as $c ) {
		$prods = wc_get_products( array_merge( $base, array( 'category' => array( $c->slug ), 'limit' => 5, 'meta_key' => 'total_sales', 'orderby' => 'meta_value_num', 'order' => 'DESC' ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
		if ( ! $prods ) {
			continue;
		}
		$note   = get_term_meta( $c->term_id, 'nx_note', true );
		$tabs[] = array( $c->slug, $c->name, $note ? $note : wp_strip_all_tags( $c->description ) );
		foreach ( $prods as $p ) {
			$list[ $p->get_id() ] = $p;
		}
	}
	foreach ( $top_ids as $id ) {
		if ( ! isset( $list[ $id ] ) ) {
			$p = wc_get_product( $id );
			if ( $p ) {
				$list = array( $id => $p ) + $list;
			}
		}
	}
	if ( ! $list ) {
		return;
	}
	$n    = count( $top_ids );
	/* translators: %d: number of products */
	$note = sprintf( __( 'The %d products Pakistan buys most, one or two from each category.', 'nexus-beauty' ), $n );
	?>
	<section class="section section--tint" id="picks" aria-labelledby="picks-h">
		<div class="wrap">
			<div class="sec-head"><div><p class="eyebrow"><?php esc_html_e( 'Proven in Pakistan', 'nexus-beauty' ); ?></p><h2 class="h2" id="picks-h"><?php esc_html_e( 'Top picks by category', 'nexus-beauty' ); ?></h2></div><a class="link-arrow" href="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', nexus_shop_url() ) ); ?>"><?php esc_html_e( 'Shop all bestsellers', 'nexus-beauty' ); ?></a></div>
			<?php if ( $tabs ) : ?>
				<div class="tabs" role="group" aria-label="<?php esc_attr_e( 'Choose a category', 'nexus-beauty' ); ?>">
					<button class="chip" type="button" data-cat-tab="all" aria-pressed="true" data-note="<?php echo esc_attr( $note ); ?>"><?php esc_html_e( 'Top picks', 'nexus-beauty' ); ?></button>
					<?php foreach ( $tabs as $t ) : ?>
						<button class="chip" type="button" data-cat-tab="<?php echo esc_attr( $t[0] ); ?>" aria-pressed="false" data-note="<?php echo esc_attr( $t[2] ); ?>"><?php echo esc_html( $t[1] ); ?></button>
					<?php endforeach; ?>
				</div>
				<p class="tab-note" data-tab-note aria-live="polite"><?php echo esc_html( $note ); ?></p>
			<?php endif; ?>
			<?php echo nexus_cards_grid( array_values( $list ), 'grid', $top_ids, 'data-tab-grid' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</section>
	<?php
}

/**
 * Search block.
 */
function nexus_home_finder() {
	$brands = taxonomy_exists( 'product_brand' ) ? (int) wp_count_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => true ) ) : 0;
	/* translators: %d: number of brands */
	$title = $brands >= 10 ? sprintf( __( 'Search %d+ brands', 'nexus-beauty' ), floor( $brands / 10 ) * 10 ) : __( 'Find your product', 'nexus-beauty' );
	?>
	<section class="section" aria-labelledby="find-h">
		<div class="wrap">
			<div class="finder">
				<div style="display:grid;gap:6px"><p class="eyebrow" style="color:#e9b8cb"><?php esc_html_e( 'Know what you want?', 'nexus-beauty' ); ?></p><h2 class="h2" id="find-h"><?php echo esc_html( $title ); ?></h2></div>
				<?php nexus_search_form( 'q-home' ); ?>
				<?php nexus_popular_searches(); ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Own-label spotlight: products tagged "our-label".
 */
function nexus_home_own() {
	$prods = wc_get_products( array( 'status' => 'publish', 'visibility' => 'catalog', 'tag' => array( 'our-label' ), 'limit' => 6, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
	$seen  = array();
	$show  = array();
	foreach ( $prods as $p ) {
		// Sizes of one product (same sizes group) appear once.
		$group = nexus_meta( $p, '_nx_group' );
		if ( $group ) {
			if ( isset( $seen[ $group ] ) ) {
				continue;
			}
			$seen[ $group ] = true;
		}
		$show[] = $p;
	}
	$show = array_slice( $show, 0, 3 );
	if ( ! $show ) {
		return;
	}
	?>
	<section class="section" style="padding-top:0" aria-labelledby="own-h">
		<div class="wrap own">
			<div class="own__copy">
				<p class="eyebrow"><?php esc_html_e( 'Our own label', 'nexus-beauty' ); ?></p>
				<h2 class="h2" id="own-h"><?php echo esc_html( nexus_text( 'own_title' ) ); ?></h2>
				<p><?php echo esc_html( nexus_text( 'own_text' ) ); ?></p>
				<a class="btn btn--lg" href="<?php echo esc_url( $show[0]->get_permalink() ); ?>"><?php echo esc_html( nexus_meta( $show[0], '_nx_scent_group' ) ? __( 'Choose your scent', 'nexus-beauty' ) : __( 'Shop our label', 'nexus-beauty' ) ); ?></a>
			</div>
			<div class="scents">
				<?php
				foreach ( $show as $p ) {
					$look  = nexus_product_look( $p );
					$label = nexus_meta( $p, '_nx_scent' );
					list( $brand ) = nexus_brand( $p );
					$label = $label ? $label : nexus_short_name( $p, $brand );
					$notes = nexus_lines( nexus_meta( $p, '_nx_notes' ), 2 );
					$small = $notes ? implode( ', ', array_slice( array_map( fn( $n ) => $n[1], $notes ), 0, 1 ) ) : nexus_meta( $p, '_nx_tagline' );
					list( $now ) = nexus_price_parts( $p );
					printf(
						'<a class="scent" href="%1$s"><span class="scent__bottle" style="%2$s">%3$s</span><b>%4$s</b>%5$s<span class="price__now">%6$s</span></a>',
						esc_url( $p->get_permalink() ),
						esc_attr( nexus_look_style( $look ) ),
						nexus_product_visual( $p, 'woocommerce_thumbnail', array( 'loading' => 'lazy' ) ), // phpcs:ignore WordPress.Security.EscapeOutput
						esc_html( $label ),
						$small ? '<small>' . esc_html( wp_trim_words( $small, 8, '' ) ) . '</small>' : '',
						esc_html( nexus_money( $now ) )
					);
				}
				?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Dermatologist brands corner.
 */
function nexus_home_derm() {
	if ( ! taxonomy_exists( 'product_brand' ) ) {
		return;
	}
	$brands = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => true, 'number' => 6, 'orderby' => 'count', 'order' => 'DESC', 'meta_key' => 'nx_derm', 'meta_value' => '1' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	if ( is_wp_error( $brands ) || ! $brands ) {
		$ids    = wc_get_products( array( 'status' => 'publish', 'tag' => array( 'derm' ), 'limit' => 30, 'return' => 'ids' ) );
		$brands = $ids ? wp_get_object_terms( $ids, 'product_brand', array( 'number' => 6 ) ) : array();
	}
	if ( is_wp_error( $brands ) || ! $brands ) {
		return;
	}
	?>
	<section class="section section--tint" aria-labelledby="derm-h">
		<div class="wrap derm">
			<div style="display:grid;gap:12px">
				<p class="eyebrow"><?php esc_html_e( 'Pakistani derm brands', 'nexus-beauty' ); ?></p>
				<h2 class="h2" id="derm-h"><?php echo esc_html( nexus_text( 'derm_title' ) ); ?></h2>
				<p class="lede"><?php echo esc_html( nexus_text( 'derm_text' ) ); ?></p>
				<?php
				$tags = nexus_concern_tags( 4 );
				if ( $tags ) {
					echo '<div class="concerns" style="margin-top:6px">';
					foreach ( $tags as $t ) {
						echo '<a href="' . esc_url( get_term_link( $t ) ) . '">' . esc_html( $t->name ) . '</a>';
					}
					echo '</div>';
				}
				?>
			</div>
			<div class="derm__brands">
				<?php foreach ( $brands as $b ) : ?>
					<?php $note = $b->description ? wp_trim_words( wp_strip_all_tags( $b->description ), 5, '' ) : get_term_meta( $b->term_id, 'nx_note', true ); ?>
					<a href="<?php echo esc_url( get_term_link( $b ) ); ?>"><b><?php echo esc_html( $b->name ); ?></b><span><?php echo esc_html( $note ); ?></span></a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Just arrived.
 */
function nexus_home_new() {
	$prods = wc_get_products( array( 'status' => 'publish', 'visibility' => 'catalog', 'limit' => 4, 'orderby' => 'date', 'order' => 'DESC' ) );
	if ( count( $prods ) < 2 ) {
		return;
	}
	?>
	<section class="section" aria-labelledby="new-h">
		<div class="wrap">
			<div class="sec-head"><div><p class="eyebrow"><?php echo esc_html( nexus_text( 'new_eyebrow' ) ); ?></p><h2 class="h2" id="new-h"><?php echo esc_html( nexus_text( 'new_title' ) ); ?></h2><?php if ( nexus_opt( 'new_text' ) ) : ?><p class="lede"><?php echo esc_html( nexus_text( 'new_text' ) ); ?></p><?php endif; ?></div><a class="link-arrow" href="<?php echo esc_url( add_query_arg( 'orderby', 'date', nexus_shop_url() ) ); ?>"><?php esc_html_e( 'See what\'s new', 'nexus-beauty' ); ?></a></div>
			<?php echo nexus_cards_grid( $prods ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</section>
	<?php
}

/**
 * Two promo cards: minis and bundles.
 */
function nexus_home_promo() {
	$cards = '';
	$mini  = get_term_by( 'slug', 'mini', 'product_tag' );
	if ( $mini && $mini->count ) {
		$cheap = wc_get_products( array( 'status' => 'publish', 'tag' => array( 'mini' ), 'limit' => 1, 'meta_key' => '_price', 'orderby' => 'meta_value_num', 'order' => 'ASC' ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
		$from  = $cheap ? nexus_money( nexus_price_parts( $cheap[0] )[0] ) : '';
		ob_start();
		?>
		<div class="promo__card promo__card--a">
			<p class="eyebrow" style="color:#e9b8cb"><?php esc_html_e( 'Try before you commit', 'nexus-beauty' ); ?></p>
			<?php /* translators: %s: price */ ?>
			<h3><?php echo esc_html( $from ? sprintf( __( 'Minis from %s', 'nexus-beauty' ), $from ) : __( 'Minis and travel sizes', 'nexus-beauty' ) ); ?></h3>
			<p><?php esc_html_e( 'Test a serum, sunscreen or scent before you buy the full size. Every mini shows its batch and expiry too.', 'nexus-beauty' ); ?></p>
			<a class="btn" href="<?php echo esc_url( get_term_link( $mini ) ); ?>"><?php esc_html_e( 'Shop minis', 'nexus-beauty' ); ?></a>
		</div>
		<?php
		$cards .= ob_get_clean();
	}
	$pct = (int) nexus_opt( 'bundle_percent' );
	if ( $pct > 0 ) {
		$routines = nexus_page_url( 'routines' );
		ob_start();
		?>
		<div class="promo__card promo__card--b">
			<p class="eyebrow" style="color:var(--sage)"><?php esc_html_e( 'Bundle & save', 'nexus-beauty' ); ?></p>
			<?php /* translators: %d: percent */ ?>
			<h3><?php echo esc_html( sprintf( __( 'Cleanser, serum and SPF: save %d%%', 'nexus-beauty' ), $pct ) ); ?></h3>
			<?php /* translators: %d: number of products */ ?>
			<p><?php echo esc_html( sprintf( __( 'Pick any %d steps from a routine kit. The discount applies automatically in your bag.', 'nexus-beauty' ), max( 2, (int) nexus_opt( 'bundle_min' ) ) ) ); ?></p>
			<a class="btn btn--dark" href="<?php echo esc_url( $routines ? $routines : nexus_shop_url() ); ?>"><?php esc_html_e( 'Build my routine', 'nexus-beauty' ); ?></a>
		</div>
		<?php
		$cards .= ob_get_clean();
	}
	if ( $cards ) {
		echo '<section class="section" style="padding-top:0" aria-label="' . esc_attr__( 'Offers', 'nexus-beauty' ) . '"><div class="wrap promo">' . $cards . '</div></section>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Shop by concern.
 */
function nexus_home_concerns() {
	$tags = nexus_concern_tags( 10 );
	if ( ! $tags ) {
		return;
	}
	?>
	<section class="section section--tint" id="concerns" aria-labelledby="con-h">
		<div class="wrap">
			<div class="sec-head"><div><p class="eyebrow"><?php esc_html_e( 'Shop by concern', 'nexus-beauty' ); ?></p><h2 class="h2" id="con-h"><?php esc_html_e( 'What would you like to fix?', 'nexus-beauty' ); ?></h2><p class="lede"><?php esc_html_e( 'Pick a concern to see products that target it, with the key ingredient on every card.', 'nexus-beauty' ); ?></p></div></div>
			<div class="concerns"><?php foreach ( $tags as $t ) : ?><a href="<?php echo esc_url( get_term_link( $t ) ); ?>"><?php echo esc_html( $t->name ); ?></a><?php endforeach; ?></div>
		</div>
	</section>
	<?php
}

/**
 * Brand tiles with Pakistani / International tabs.
 *
 * @param int  $limit Limit.
 * @param bool $section Wrap in the home section.
 */
function nexus_brand_tiles( $limit = 12, $section = true ) {
	if ( ! taxonomy_exists( 'product_brand' ) ) {
		return;
	}
	$brands = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => true, 'number' => 300, 'orderby' => $section ? 'count' : 'name', 'order' => $section ? 'DESC' : 'ASC' ) );
	if ( is_wp_error( $brands ) || ! $brands ) {
		return;
	}
	$rows = array();
	foreach ( $brands as $b ) {
		$o      = get_term_meta( $b->term_id, 'nx_origin', true );
		$rows[] = array( $b, in_array( $o, array( 'local', 'intl' ), true ) ? $o : '' );
	}
	if ( $section ) {
		usort(
			$rows,
			function ( $a, $b ) {
				$rank = array( 'local' => 0, 'intl' => 1, '' => 2 );
				return $rank[ $a[1] ] <=> $rank[ $b[1] ];
			}
		);
		$rows = array_slice( $rows, 0, $limit );
	}
	$origins = array_unique( array_filter( wp_list_pluck( array_map( fn( $r ) => array( 'o' => $r[1] ), $rows ), 'o' ) ) );
	$tabs    = in_array( 'local', $origins, true ) && in_array( 'intl', $origins, true );
	$all     = nexus_page_url( 'brands' );
	if ( $section ) {
		echo '<section class="section" id="brands" aria-labelledby="brand-h"><div class="wrap">';
		echo '<div class="sec-head"><div><p class="eyebrow">' . esc_html__( 'Our brands', 'nexus-beauty' ) . '</p><h2 class="h2" id="brand-h">' . esc_html__( 'Pakistani favourites and global icons', 'nexus-beauty' ) . '</h2></div>' . ( $all ? '<a class="link-arrow" href="' . esc_url( $all ) . '">' . esc_html__( 'All brands A–Z', 'nexus-beauty' ) . '</a>' : '' ) . '</div>';
	}
	if ( $tabs ) {
		echo '<div class="brand-tabs" role="group" aria-label="' . esc_attr__( 'Filter brands', 'nexus-beauty' ) . '"><button class="chip" type="button" data-brand-tab="all" aria-pressed="true">' . esc_html__( 'All brands', 'nexus-beauty' ) . '</button><button class="chip" type="button" data-brand-tab="local" aria-pressed="false">' . esc_html__( 'Pakistani', 'nexus-beauty' ) . '</button><button class="chip" type="button" data-brand-tab="intl" aria-pressed="false">' . esc_html__( 'International', 'nexus-beauty' ) . '</button></div>';
	}
	echo '<div class="brands">';
	foreach ( $rows as $r ) {
		$b    = $r[0];
		$note = get_term_meta( $b->term_id, 'nx_note', true );
		if ( ! $note ) {
			/* translators: %d: products */
			$note = 'local' === $r[1] ? __( 'Pakistan', 'nexus-beauty' ) : ( 'intl' === $r[1] ? __( 'International', 'nexus-beauty' ) : sprintf( _n( '%d product', '%d products', $b->count, 'nexus-beauty' ), $b->count ) );
		}
		printf( '<a class="brand" href="%1$s" data-origin="%2$s"><b>%3$s</b><span>%4$s</span></a>', esc_url( get_term_link( $b ) ), esc_attr( $r[1] ), esc_html( $b->name ), esc_html( $note ) );
	}
	echo '</div>';
	if ( $section ) {
		echo '</div></section>';
	}
}

/**
 * Why shop with us.
 */
function nexus_home_why() {
	$store = get_bloginfo( 'name' );
	$items = array(
		array( 'shield', __( 'Batch and expiry on every order', 'nexus-beauty' ), __( 'We buy from brands and authorised distributors only, and show you the proof on the box.', 'nexus-beauty' ) ),
		array( 'cash', __( 'Cash on delivery', 'nexus-beauty' ), __( 'Pay when your parcel arrives, anywhere in Pakistan. Cards, JazzCash and Easypaisa work too.', 'nexus-beauty' ) ),
		/* translators: 1: cut-off time, 2: delivery summary */
		array( 'truck', __( 'Same-day dispatch', 'nexus-beauty' ), sprintf( __( 'Order by %1$s and it leaves today. %2$s.', 'nexus-beauty' ), nexus_cutoff_label(), ucfirst( nexus_delivery_summary() ) ) ),
		array( 'chat', __( 'Free skin advice', 'nexus-beauty' ), __( 'Send us a WhatsApp message and a beauty advisor will suggest a routine for your skin.', 'nexus-beauty' ) ),
	);
	?>
	<section class="section section--tint" aria-labelledby="why-h">
		<div class="wrap">
			<?php /* translators: %s: store name */ ?>
			<div class="sec-head"><div><p class="eyebrow"><?php echo esc_html( sprintf( __( 'Why %s', 'nexus-beauty' ), $store ) ); ?></p><h2 class="h2" id="why-h"><?php esc_html_e( 'Shop with confidence', 'nexus-beauty' ); ?></h2></div></div>
			<div class="usp"><?php foreach ( $items as $i ) : ?><div><?php echo nexus_icon( $i[0] ); // phpcs:ignore ?><h3><?php echo esc_html( $i[1] ); ?></h3><p><?php echo esc_html( $i[2] ); ?></p></div><?php endforeach; ?></div>
		</div>
	</section>
	<?php
}

/**
 * Frequently asked questions.
 */
function nexus_home_faq() {
	$faq = nexus_store_faq();
	?>
	<section class="section" aria-labelledby="faq-h">
		<div class="wrap" style="display:grid;gap:24px">
			<div><p class="eyebrow"><?php esc_html_e( 'Questions', 'nexus-beauty' ); ?></p><h2 class="h2" id="faq-h"><?php esc_html_e( 'Frequently asked questions', 'nexus-beauty' ); ?></h2></div>
			<?php echo nexus_faq_html( $faq ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</section>
	<?php
	nexus_faq_schema( $faq );
}

/**
 * Newsletter.
 */
function nexus_home_news() {
	$custom = trim( (string) nexus_opt( 'newsletter_form' ) );
	$state  = isset( $_GET['nx-sub'] ) ? sanitize_key( wp_unslash( $_GET['nx-sub'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	?>
	<section class="section" style="padding-top:0" aria-label="<?php esc_attr_e( 'Newsletter', 'nexus-beauty' ); ?>" id="newsletter">
		<div class="wrap">
			<div class="news">
				<div><h2><?php esc_html_e( 'Get 10% off your first order', 'nexus-beauty' ); ?></h2><p><?php esc_html_e( 'Hear first about new arrivals, restocks and member offers. One or two emails a week.', 'nexus-beauty' ); ?></p></div>
				<?php if ( $custom ) : ?>
					<div><?php echo do_shortcode( $custom ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<?php else : ?>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="nexus_subscribe">
						<?php wp_nonce_field( 'nexus_subscribe', 'nx_sub_nonce' ); ?>
						<label class="sr-only" for="news-email"><?php esc_html_e( 'Email address', 'nexus-beauty' ); ?></label>
						<input id="news-email" name="email" type="email" required placeholder="<?php esc_attr_e( 'Your email address', 'nexus-beauty' ); ?>" autocomplete="email">
						<button class="btn" type="submit"><?php esc_html_e( 'Get my code', 'nexus-beauty' ); ?></button>
						<small role="status"><?php echo 'ok' === $state ? esc_html__( 'You\'re in. Check your inbox for your code.', 'nexus-beauty' ) : ( 'err' === $state ? esc_html__( 'Please enter a valid email address.', 'nexus-beauty' ) : esc_html__( 'Unsubscribe anytime. We never share your email.', 'nexus-beauty' ) ); ?></small>
					</form>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * SEO text block.
 */
function nexus_home_seo() {
	$store = get_bloginfo( 'name' );
	$rows  = apply_filters(
		'nexus_home_seo',
		array(
			/* translators: %s: store name */
			array( __( 'Local derm brands and international favourites', 'nexus-beauty' ), sprintf( __( '%s stocks Pakistani dermatologist brands such as Rederm, Estelin, Neophar and Jenpharm alongside Korean skincare like AXIS-Y, Dr. Althea and Beauty of Joseon, and mass favourites from L\'Oréal, Garnier, Pond\'s and Vaseline.', 'nexus-beauty' ), $store ) ),
			array( __( 'How we check authenticity', 'nexus-beauty' ), __( 'We buy every product from the brand or its authorised distributor. Each order shows the batch number and expiry date, so you can see how fresh it is before you open it.', 'nexus-beauty' ) ),
			array( __( 'Sunscreen, hair fall and brightening', 'nexus-beauty' ), __( 'Shop sunscreen from SPF 50 to SPF 70, hair fall shampoos with biotin, and serums for dark spots and uneven tone. Filter by concern to find what suits your skin and hair.', 'nexus-beauty' ) ),
			/* translators: 1: cut-off, 2: amount */
			array( __( 'Delivery and cash on delivery', 'nexus-beauty' ), sprintf( __( 'Orders before %1$s ship the same day. Delivery is free over %2$s, and you can pay cash on delivery anywhere in Pakistan.', 'nexus-beauty' ), nexus_cutoff_label(), nexus_money( nexus_opt( 'free_shipping' ) ) ) ),
		)
	);
	?>
	<section class="section section--tint" aria-labelledby="seo-h">
		<div class="wrap seo-copy">
			<h2 class="h2" id="seo-h"><?php echo esc_html( nexus_text( 'seo_title' ) ); ?></h2>
			<?php foreach ( $rows as $r ) : ?><h3><?php echo esc_html( $r[0] ); ?></h3><p><?php echo esc_html( $r[1] ); ?></p><?php endforeach; ?>
		</div>
	</section>
	<?php
}

/**
 * Newsletter sign-up handler: saves the email (Appearance > Nexus setup lists them) and notifies the store.
 */
function nexus_handle_subscribe() {
	$back  = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$back  = remove_query_arg( 'nx-sub', $back );
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( ! isset( $_POST['nx_sub_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nx_sub_nonce'] ) ), 'nexus_subscribe' ) || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'nx-sub', 'err', $back ) . '#newsletter' );
		exit;
	}
	$list = get_option( 'nexus_subscribers', array() );
	$list = is_array( $list ) ? $list : array();
	if ( ! isset( $list[ $email ] ) && count( $list ) < 20000 ) {
		$list[ $email ] = time();
		update_option( 'nexus_subscribers', $list, false );
		do_action( 'nexus_new_subscriber', $email );
	}
	wp_safe_redirect( add_query_arg( 'nx-sub', 'ok', $back ) . '#newsletter' );
	exit;
}
add_action( 'admin_post_nexus_subscribe', 'nexus_handle_subscribe' );
add_action( 'admin_post_nopriv_nexus_subscribe', 'nexus_handle_subscribe' );
