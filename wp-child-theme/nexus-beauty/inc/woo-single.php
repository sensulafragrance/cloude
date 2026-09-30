<?php
/**
 * Product page in the design: gallery + buy box, quick facts, accordions, frequently bought together,
 * reviews, questions, related products and the sticky add-to-bag bar.
 * The template is woocommerce/content-single-product.php; the pieces are built here.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

// The design's buy box replaces WooCommerce's default summary parts. Plugins hooked into
// woocommerce_single_product_summary still run (and WooCommerce's product structured data).
add_action(
	'wp',
	function () {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
	}
);

/**
 * Unit price text, e.g. "Rs 12.9/ml".
 *
 * @param WC_Product $product Product or variation.
 * @param float|null $price   Price to use.
 * @return string
 */
function nexus_unit_price( $product, $price = null ) {
	$vol   = (float) nexus_meta( $product, '_nx_volume' );
	$price = null === $price ? (float) wc_get_price_to_display( $product ) : (float) $price;
	if ( $vol <= 0 || $price <= 0 ) {
		return '';
	}
	$unit = nexus_meta( $product, '_nx_unit' );
	$unit = $unit ? $unit : 'ml';
	$per  = $price / $vol;
	return html_entity_decode( wp_strip_all_tags( wc_price( $per, array( 'decimals' => $per < 100 ? 1 : 0 ) ) ), ENT_QUOTES, 'UTF-8' ) . '/' . $unit;
}

/**
 * Batch card (design: .batch.batch--pdp).
 *
 * @param WC_Product $product Product or variation.
 * @return string
 */
function nexus_batch_card( $product ) {
	$batch = nexus_meta( $product, '_nx_batch' );
	$exp   = nexus_meta( $product, '_nx_expiry' );
	if ( ! $batch && ! $exp ) {
		return '';
	}
	$mfg  = nexus_meta( $product, '_nx_mfg' );
	$rows = '';
	if ( $batch ) {
		$rows .= '<span>' . esc_html__( 'BATCH', 'nexus-beauty' ) . '&nbsp;&nbsp;' . esc_html( $batch ) . '</span>';
	}
	if ( $mfg ) {
		$rows .= '<span>' . esc_html__( 'MFG', 'nexus-beauty' ) . '&nbsp;&nbsp;&nbsp;&nbsp;' . esc_html( $mfg ) . '</span>';
	}
	if ( $exp ) {
		$rows .= '<span>' . esc_html__( 'EXP', 'nexus-beauty' ) . '&nbsp;&nbsp;&nbsp;&nbsp;' . esc_html( $exp ) . '</span>';
	}
	return '<div class="batch batch--pdp"><b>✓ ' . esc_html__( 'What your box will show', 'nexus-beauty' ) . '</b>' . $rows . '</div>';
}

// Variation data for JavaScript: unit price, batch card and size label.
add_filter(
	'woocommerce_available_variation',
	function ( $data, $product, $variation ) {
		$data['nx_unit']  = nexus_unit_price( $variation );
		$data['nx_batch'] = nexus_opt( 'show_batch' ) ? nexus_batch_card( $variation ) : '';
		$data['nx_size']  = nexus_meta( $variation, '_nx_size' );
		return $data;
	},
	10,
	3
);

add_filter(
	'woocommerce_product_single_add_to_cart_text',
	function ( $text, $product ) {
		return ( $product && $product->is_type( array( 'simple', 'variable' ) ) ) ? __( 'Add to bag', 'nexus-beauty' ) : $text;
	},
	10,
	2
);

// "Buy now" button: adds to the bag and goes straight to checkout.
add_action(
	'woocommerce_after_add_to_cart_button',
	function () {
		global $product;
		if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() || ! $product->is_type( array( 'simple', 'variable' ) ) ) {
			return;
		}
		printf(
			'<button type="submit" name="add-to-cart" value="%1$d" formaction="%2$s" class="nx-buy-now" data-nx-buy-now>%3$s</button>',
			(int) $product->get_id(),
			esc_url( add_query_arg( 'nx_buy_now', '1', $product->get_permalink() ) ),
			esc_html__( 'Buy now, pay on delivery', 'nexus-beauty' )
		);
	}
);
add_filter(
	'woocommerce_add_to_cart_redirect',
	function ( $url ) {
		if ( ! empty( $_REQUEST['nx_buy_now'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- WooCommerce handles the add-to-cart request.
			return wc_get_checkout_url();
		}
		return $url;
	}
);

/**
 * Products linked to this one as sizes (_nx_group) or scents/shades (_nx_scent_group).
 *
 * @param WC_Product $product Product.
 * @param string     $key     Meta key of the group.
 * @return WC_Product[]
 */
function nexus_linked_products( $product, $key = '_nx_group' ) {
	$group = nexus_meta( $product, $key );
	if ( ! $group ) {
		return array();
	}
	$ids  = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 12,
			'fields'         => 'ids',
			'meta_key'       => $key, // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => $group, // phpcs:ignore WordPress.DB.SlowDBQuery
			'orderby'        => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
		)
	);
	$list = array_values( array_filter( array_map( 'wc_get_product', $ids ) ) );
	if ( count( $list ) < 2 ) {
		return array();
	}
	if ( '_nx_group' === $key ) {
		usort(
			$list,
			function ( $a, $b ) {
				return (float) $a->get_price() <=> (float) $b->get_price();
			}
		);
	}
	return $list;
}

/**
 * The design's gallery: thumbnails + stage.
 *
 * @param WC_Product $product Product.
 */
function nexus_gallery( $product ) {
	$look  = nexus_product_look( $product );
	$ids   = array_values( array_unique( array_filter( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) ) ) );
	?>
	<div class="gallery" style="<?php echo esc_attr( nexus_look_style( $look ) ); ?>">
		<?php if ( $ids ) : ?>
			<?php if ( count( $ids ) > 1 ) : ?>
				<div class="thumbs" aria-label="<?php esc_attr_e( 'Product images', 'nexus-beauty' ); ?>">
					<?php
					foreach ( $ids as $i => $id ) {
						$full = wp_get_attachment_image_src( $id, 'woocommerce_single' );
						$alt  = get_post_meta( $id, '_wp_attachment_image_alt', true );
						/* translators: %d: image number */
						$label = $alt ? $alt : sprintf( __( 'Image %d', 'nexus-beauty' ), $i + 1 );
						printf(
							'<button type="button" data-view="%1$d" data-src="%2$s" data-srcset="%3$s" data-note="%4$s" aria-current="%5$s" aria-label="%6$s">%7$s</button>',
							(int) $i,
							esc_url( $full ? $full[0] : '' ),
							esc_attr( (string) wp_get_attachment_image_srcset( $id, 'woocommerce_single' ) ),
							esc_attr( wp_get_attachment_caption( $id ) ),
							0 === $i ? 'true' : 'false',
							esc_attr( $label ),
							wp_get_attachment_image( $id, 'woocommerce_gallery_thumbnail', false, array( 'alt' => '', 'loading' => 'lazy' ) )
						);
					}
					?>
				</div>
			<?php endif; ?>
			<div class="stage" data-view="0">
				<?php
				echo wp_get_attachment_image(
					$ids[0],
					'woocommerce_single',
					false,
					array(
						'class'         => 'stage__img',
						'alt'           => $product->get_name(),
						'loading'       => 'eager',
						'fetchpriority' => 'high',
						'sizes'         => '(max-width: 900px) 100vw, 600px',
					)
				);
				echo nexus_badges_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput
				echo nexus_wish_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput
				$caption = wp_get_attachment_caption( $ids[0] );
				?>
				<span class="stage__note" data-stage-note<?php echo $caption ? '' : ' hidden'; ?>><?php echo esc_html( $caption ); ?></span>
			</div>
		<?php else : ?>
			<?php
			$views = array( __( 'Front', 'nexus-beauty' ), __( 'Angled view', 'nexus-beauty' ), __( 'Label and batch code', 'nexus-beauty' ), __( 'Up close', 'nexus-beauty' ) );
			?>
			<div class="thumbs" aria-label="<?php esc_attr_e( 'Product images', 'nexus-beauty' ); ?>">
				<?php foreach ( $views as $i => $v ) : ?>
					<button type="button" data-view="<?php echo (int) $i; ?>" data-note="<?php echo esc_attr( $v ); ?>" aria-current="<?php echo 0 === $i ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( $v ); ?>"><?php echo nexus_shape_svg( $look[0] ); // phpcs:ignore ?></button>
				<?php endforeach; ?>
			</div>
			<div class="stage" data-view="0">
				<?php echo nexus_shape_svg( $look[0] ); // phpcs:ignore ?>
				<?php echo nexus_badges_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo nexus_wish_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span class="stage__note" data-stage-note><?php echo esc_html( $views[0] ); ?></span>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Scent swatches and size buttons linking to the related products (design: .swatches, .sizes).
 *
 * @param WC_Product $product Product.
 */
function nexus_linked_choices( $product ) {
	$scents = nexus_linked_products( $product, '_nx_scent_group' );
	if ( $scents ) {
		echo '<div><p class="opt-label">' . esc_html__( 'Scent', 'nexus-beauty' ) . ': <span>' . esc_html( nexus_meta( $product, '_nx_scent' ) ) . '</span></p><div class="swatches">';
		foreach ( $scents as $p ) {
			$label = nexus_meta( $p, '_nx_scent' );
			printf(
				'<a href="%1$s" style="%2$s"%3$s><i aria-hidden="true"></i>%4$s</a>',
				esc_url( $p->get_permalink() ),
				esc_attr( nexus_look_style( nexus_product_look( $p ) ) ),
				$p->get_id() === $product->get_id() ? ' aria-current="true"' : '',
				esc_html( $label ? $label : $p->get_name() )
			);
		}
		echo '</div></div>';
	}
	$sizes = nexus_linked_products( $product, '_nx_group' );
	if ( $sizes ) {
		$current = nexus_meta( $product, '_nx_choice' );
		$current = $current ? $current : nexus_meta( $product, '_nx_size' );
		echo '<div><p class="opt-label">' . esc_html__( 'Size', 'nexus-beauty' ) . ': <span data-size-label>' . esc_html( $current ) . '</span></p><div class="sizes">';
		foreach ( $sizes as $p ) {
			$label = nexus_meta( $p, '_nx_choice' );
			$label = $label ? $label : nexus_meta( $p, '_nx_size' );
			$tag   = nexus_meta( $p, '_nx_choice_tag' );
			$unit  = nexus_unit_price( $p );
			printf(
				'<a href="%1$s"%2$s>%3$s<b>%4$s</b><small>%5$s</small></a>',
				esc_url( $p->get_permalink() ),
				$p->get_id() === $product->get_id() ? ' aria-current="true"' : '',
				$tag ? '<span class="tag">' . esc_html( $tag ) . '</span>' : '',
				esc_html( $label ? $label : $p->get_name() ),
				esc_html( nexus_money( wc_get_price_to_display( $p ) ) . ( $unit ? ' · ' . $unit : '' ) )
			);
		}
		echo '</div></div>';
	}
}

/**
 * Fragrance notes (design: .notes).
 *
 * @param WC_Product $product Product.
 */
function nexus_notes( $product ) {
	$notes = nexus_lines( nexus_meta( $product, '_nx_notes' ), 2 );
	if ( ! $notes ) {
		return;
	}
	echo '<div class="notes">';
	foreach ( array_slice( $notes, 0, 3 ) as $n ) {
		echo '<div><span>' . esc_html( $n[0] ) . '</span><b>' . esc_html( $n[1] ) . '</b></div>';
	}
	echo '</div>';
}

/**
 * Delivery promises (design: ul.deliver), with a live arrival date for the chosen city.
 */
function nexus_deliver_list() {
	$cities = nexus_cities();
	?>
	<ul class="deliver">
		<?php /* translators: %s: time, e.g. 3pm */ ?>
		<li><?php echo nexus_icon( 'clock' ); // phpcs:ignore ?><span><?php echo wp_kses( sprintf( __( 'Order before <b>%s</b> for <b>same-day dispatch</b>', 'nexus-beauty' ), esc_html( nexus_cutoff_label() ) ), array( 'b' => array() ) ); ?></span></li>
		<li><?php echo nexus_icon( 'truck' ); // phpcs:ignore ?><span><?php echo esc_html( nexus_delivery_summary() ); ?>
			<?php if ( $cities ) : ?>
				<small data-nx-eta><?php esc_html_e( 'Arrives', 'nexus-beauty' ); ?> <b data-nx-eta-text></b> <?php esc_html_e( 'in', 'nexus-beauty' ); ?> <label class="sr-only" for="nx-city"><?php esc_html_e( 'Your city', 'nexus-beauty' ); ?></label><select id="nx-city" data-nx-city>
					<?php foreach ( $cities as $city => $days ) : ?><option value="<?php echo esc_attr( $days ); ?>"><?php echo esc_html( $city ); ?></option><?php endforeach; ?>
					<option value="<?php echo esc_attr( nexus_opt( 'default_days' ) ); ?>"><?php esc_html_e( 'Other city', 'nexus-beauty' ); ?></option>
				</select></small>
			<?php endif; ?>
		</span></li>
		<?php /* translators: text in bold */ ?>
		<li><?php echo nexus_icon( 'cash' ); // phpcs:ignore ?><span><?php echo wp_kses( __( '<b>Cash on delivery</b> anywhere in Pakistan', 'nexus-beauty' ), array( 'b' => array() ) ); ?></span></li>
		<?php /* translators: %d: days */ ?>
		<li><?php echo nexus_icon( 'return' ); // phpcs:ignore ?><span><?php echo esc_html( sprintf( __( '%d-day returns on unopened products', 'nexus-beauty' ), (int) nexus_opt( 'returns_days' ) ) ); ?></span></li>
	</ul>
	<?php
	$badges = array_filter( array_map( 'trim', explode( ',', (string) nexus_opt( 'payment_badges' ) ) ) );
	if ( $badges ) {
		echo '<div class="pay-row">' . esc_html__( 'Pay with', 'nexus-beauty' );
		foreach ( $badges as $b ) {
			echo '<span>' . esc_html( 'MASTERCARD' === strtoupper( $b ) ? 'MC' : $b ) . '</span>';
		}
		echo '</div>';
	}
}

/**
 * Quick facts (design: section.facts).
 *
 * @param WC_Product $product Product.
 */
function nexus_quick_facts( $product ) {
	$rows = nexus_lines( nexus_meta( $product, '_nx_facts' ), 2 );
	if ( ! $rows ) {
		$good = nexus_lines( nexus_meta( $product, '_nx_good_for' ) );
		$ing  = nexus_lines( nexus_meta( $product, '_nx_ingredients' ), 3 );
		if ( nexus_meta( $product, '_nx_tagline' ) ) {
			$rows[] = array( __( 'What it does', 'nexus-beauty' ), nexus_meta( $product, '_nx_tagline' ) );
		}
		if ( $good ) {
			$rows[] = array( __( 'Best for', 'nexus-beauty' ), implode( ', ', array_slice( $good, 0, 3 ) ) );
		}
		if ( $ing ) {
			$rows[] = array( __( 'Key ingredients', 'nexus-beauty' ), implode( ', ', wp_list_pluck( array_slice( $ing, 0, 3 ), 0 ) ) );
		}
		if ( nexus_meta( $product, '_nx_size' ) ) {
			$rows[] = array( __( 'Size', 'nexus-beauty' ), nexus_meta( $product, '_nx_size' ) );
		}
		list( $brand, $brand_url ) = nexus_brand( $product );
		if ( $brand ) {
			$origin = '';
			if ( taxonomy_exists( 'product_brand' ) ) {
				$bt = get_the_terms( $product->get_id(), 'product_brand' );
				if ( $bt && ! is_wp_error( $bt ) ) {
					$o      = get_term_meta( $bt[0]->term_id, 'nx_origin', true );
					$origin = 'local' === $o ? ' · ' . __( 'Pakistan', 'nexus-beauty' ) : ( 'intl' === $o ? ' · ' . __( 'Imported', 'nexus-beauty' ) : '' );
				}
			}
			$rows[] = array( __( 'Brand', 'nexus-beauty' ), $brand . $origin );
		}
		foreach ( $product->get_attributes() as $attr ) {
			if ( $attr->get_visible() && ! $attr->get_variation() ) {
				$rows[] = array( wc_attribute_label( $attr->get_name() ), $product->get_attribute( $attr->get_name() ) );
			}
		}
		if ( $product->get_sku() ) {
			$rows[] = array( __( 'SKU', 'nexus-beauty' ), $product->get_sku() );
		}
	}
	if ( ! $rows ) {
		return;
	}
	echo '<section class="facts" aria-labelledby="facts-h"><h2 id="facts-h">' . esc_html__( 'Quick facts', 'nexus-beauty' ) . '</h2><dl>';
	foreach ( $rows as $r ) {
		echo '<dt>' . esc_html( $r[0] ) . '</dt><dd>' . esc_html( $r[1] ) . '</dd>';
	}
	echo '</dl></section>';
}

/**
 * Accordions under the buy box (design: .acc).
 *
 * @param WC_Product $product Product.
 */
function nexus_accordions( $product ) {
	$items = array();
	$desc  = $product->get_description();
	if ( $desc ) {
		$items[] = array( __( 'Description', 'nexus-beauty' ), apply_filters( 'the_content', $desc ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
	}
	$good = nexus_lines( nexus_meta( $product, '_nx_good_for' ) );
	$not  = nexus_lines( nexus_meta( $product, '_nx_not_for' ) );
	if ( $good || $not ) {
		$html = '<div class="fit">';
		if ( $good ) {
			$html .= '<div class="fit__yes"><b>' . esc_html__( 'Good for you if you have', 'nexus-beauty' ) . '</b><ul>' . implode( '', array_map( fn( $g ) => '<li>' . esc_html( $g ) . '</li>', $good ) ) . '</ul></div>';
		}
		if ( $not ) {
			$html .= '<div class="fit__no"><b>' . esc_html__( 'Choose something else if', 'nexus-beauty' ) . '</b><ul>' . implode( '', array_map( fn( $n ) => '<li>' . esc_html( $n ) . '</li>', $not ) ) . '</ul></div>';
		}
		$items[] = array( __( 'Is it right for me?', 'nexus-beauty' ), $html . '</div>' );
	}
	$ing = nexus_lines( nexus_meta( $product, '_nx_ingredients' ), 3 );
	if ( $ing ) {
		$html = '<div class="ingr">';
		foreach ( $ing as $i ) {
			$html .= '<div><span>' . esc_html( $i[1] ) . '</span><b>' . esc_html( $i[0] ) . '</b>' . ( $i[2] ? '<p>' . esc_html( $i[2] ) . '</p>' : '' ) . '</div>';
		}
		$items[] = array( __( 'Key ingredients', 'nexus-beauty' ), $html . '</div>' );
	}
	$how = nexus_lines( nexus_meta( $product, '_nx_howto' ) );
	if ( $how ) {
		$items[] = array( __( 'How to use', 'nexus-beauty' ), '<ol>' . implode( '', array_map( fn( $s ) => '<li>' . esc_html( $s ) . '</li>', $how ) ) . '</ol>' );
	}
	if ( nexus_meta( $product, '_nx_inci' ) ) {
		$items[] = array( __( 'Full ingredients', 'nexus-beauty' ), '<p>' . esc_html( nexus_meta( $product, '_nx_inci' ) ) . '</p>' );
	}
	if ( nexus_youtube_id( nexus_meta( $product, '_nx_video' ) ) ) {
		$items[] = array( __( 'Video', 'nexus-beauty' ), nexus_video_embed( nexus_meta( $product, '_nx_video' ), $product->get_name() ) );
	}
	$free    = (float) nexus_opt( 'free_shipping' );
	$flat    = (float) nexus_opt( 'flat_rate' );
	$items[] = array(
		__( 'Delivery & returns', 'nexus-beauty' ),
		'<p>' . esc_html(
			trim(
				( $free > 0 ? sprintf( /* translators: 1: amount, 2: flat rate */ __( 'Free delivery over %1$s; otherwise a flat %2$s.', 'nexus-beauty' ), nexus_money( $free ), nexus_money( $flat ) ) : '' )
				/* translators: %d: days */
				. ' ' . sprintf( __( 'Unopened items can be returned within %d days. Damaged or wrong items are replaced free of charge.', 'nexus-beauty' ), (int) nexus_opt( 'returns_days' ) )
			)
		) . '</p>',
	);
	echo '<div class="acc">';
	foreach ( $items as $i => $it ) {
		echo '<details' . ( 0 === $i ? ' open' : '' ) . '><summary>' . esc_html( $it[0] ) . '</summary><div>' . $it[1] . '</div></details>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped when built.
	}
	echo '</div>';
}

/**
 * The buy box.
 *
 * @param WC_Product $product Product.
 */
function nexus_buybox( $product ) {
	list( $brand, $brand_url ) = nexus_brand( $product );
	$tagline = nexus_meta( $product, '_nx_subtitle' );
	$tagline = $tagline ? $tagline : nexus_meta( $product, '_nx_tagline' );
	$count   = (int) $product->get_review_count();
	$faq     = nexus_lines( nexus_meta( $product, '_nx_faq' ), 2 );
	$reviews = wc_review_ratings_enabled() && comments_open( $product->get_id() );
	$short   = $product->get_short_description();
	?>
	<div class="buybox">
		<div style="display:grid;gap:8px">
			<?php if ( $brand ) : ?>
				<?php if ( $brand_url ) : ?><a class="buybox__brand" href="<?php echo esc_url( $brand_url ); ?>"><?php echo esc_html( $brand ); ?></a><?php else : ?><p class="buybox__brand"><?php echo esc_html( $brand ); ?></p><?php endif; ?>
			<?php endif; ?>
			<h1 class="product_title"><?php echo esc_html( nexus_short_name( $product, $brand ) ); ?></h1>
			<?php if ( $tagline ) : ?><p class="buybox__sub"><?php echo esc_html( $tagline ); ?></p><?php endif; ?>
			<?php
			$meta = array();
			if ( $reviews ) {
				if ( $count ) {
					$avg    = (float) $product->get_average_rating();
					/* translators: 1: average rating, 2: review count */
					$meta[] = '<span class="rating"><span class="stars" style="--pct:' . esc_attr( round( $avg / 5 * 100 ) ) . '%"></span>' . esc_html( number_format_i18n( $avg, 1 ) ) . '</span> <a href="#reviews">' . esc_html( sprintf( _n( '%d review', '%d reviews', $count, 'nexus-beauty' ), $count ) ) . '</a>';
				} else {
					$meta[] = ( nexus_is_new( $product ) ? esc_html__( 'New launch', 'nexus-beauty' ) . ' · ' : '' ) . '<a href="#reviews">' . esc_html__( 'Be the first to review', 'nexus-beauty' ) . '</a>';
				}
			}
			if ( $faq ) {
				/* translators: %d: number of questions */
				$meta[] = '<a href="#qa">' . esc_html( sprintf( _n( '%d answered question', '%d answered questions', count( $faq ), 'nexus-beauty' ), count( $faq ) ) ) . '</a>';
			}
			if ( $meta ) {
				echo '<p class="buybox__meta">' . implode( ' · ', $meta ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above.
			}
			?>
		</div>
		<div>
			<?php echo nexus_price_html( $product, true, ' data-pdp-price' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php
			$unit = $product->is_type( 'simple' ) ? nexus_unit_price( $product ) : '';
			$note = $product->is_on_sale() ? __( 'Offer price, tax included. Pay cash on delivery.', 'nexus-beauty' ) : __( 'Tax included. Pay cash on delivery.', 'nexus-beauty' );
			?>
			<p class="tax-note"><span data-nx-unit><?php echo esc_html( $unit ? $unit . ' · ' : '' ); ?></span><?php echo esc_html( $note ); ?></p>
		</div>
		<?php if ( $short && wp_strip_all_tags( $short ) !== $tagline ) : ?>
			<div class="buybox__desc"><?php echo wp_kses_post( wpautop( $short ) ); ?></div>
		<?php endif; ?>
		<?php nexus_linked_choices( $product ); ?>
		<?php nexus_notes( $product ); ?>
		<?php woocommerce_template_single_add_to_cart(); ?>
		<?php
		if ( function_exists( 'nexus_free_ship_bar' ) ) {
			echo nexus_free_ship_bar(); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		if ( nexus_opt( 'show_batch' ) ) {
			echo '<div data-nx-batch>' . nexus_batch_card( $product ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		nexus_deliver_list();
		do_action( 'woocommerce_single_product_summary' );
		nexus_quick_facts( $product );
		nexus_accordions( $product );
		?>
	</div>
	<?php
}

/* ---------- Sections under the product ---------- */

/**
 * Bundle builder in the design (.bundle), used by "Frequently bought together" and routine kits.
 *
 * @param WC_Product[] $products Products (the first is the current one on product pages).
 * @param string       $name     Bundle name (saved on the order).
 * @return string
 */
function nexus_bundle_html( $products, $name ) {
	$products = array_values(
		array_filter(
			$products,
			function ( $p ) {
				return $p instanceof WC_Product && $p->is_purchasable() && $p->is_in_stock() && $p->is_type( 'simple' );
			}
		)
	);
	if ( count( $products ) < 2 ) {
		return '';
	}
	$pct   = (int) nexus_opt( 'bundle_percent' );
	$min   = max( 2, (int) nexus_opt( 'bundle_min' ) );
	$sum   = 0;
	$items = array();
	foreach ( $products as $p ) {
		$price = (float) wc_get_price_to_display( $p );
		$sum  += $price;
		$look  = nexus_product_look( $p );
		$size  = nexus_meta( $p, '_nx_size' );
		list( $brand ) = nexus_brand( $p );
		$items[] = sprintf(
			'<a class="bundle__item" href="%1$s" data-bundle-item data-id="%2$d"><div class="card__media" style="%3$s">%4$s</div><span>%5$s</span><b>%6$s</b></a>',
			esc_url( $p->get_permalink() ),
			$p->get_id(),
			esc_attr( nexus_look_style( $look ) ),
			nexus_product_visual( $p, 'woocommerce_gallery_thumbnail', array( 'loading' => 'lazy' ) ),
			esc_html( nexus_short_name( $p, $brand ) . ( $size ? ' · ' . $size : '' ) ),
			esc_html( nexus_money( $price ) )
		);
	}
	$n     = count( $products );
	$disc  = ( $pct > 0 && $n >= $min ) ? round( $sum * $pct / 100, wc_get_price_decimals() ) : 0;
	$ids   = implode( ',', array_map( fn( $p ) => $p->get_id(), $products ) );
	$out   = '<div class="bundle" data-nx-bundle data-ids="' . esc_attr( $ids ) . '" data-name="' . esc_attr( $name ) . '">';
	$out  .= '<div class="bundle__items">' . implode( '<span class="bundle__plus" aria-hidden="true">+</span>', $items ) . '</div>';
	$out  .= '<div class="bundle__sum">';
	/* translators: %d: number of items */
	$out .= '<p>' . esc_html( sprintf( _n( 'Total for %d item', 'Total for %d items', $n, 'nexus-beauty' ), $n ) ) . '</p>';
	$out .= '<p class="price" style="justify-content:inherit"><span class="price__now" style="font-size:24px">' . esc_html( nexus_money( $sum - $disc ) ) . '</span>' . ( $disc ? '<span class="price__was">' . esc_html( nexus_money( $sum ) ) . '</span>' : '' ) . '</p>';
	if ( $disc ) {
		/* translators: 1: amount, 2: percent */
		$out .= '<p class="price__save">' . esc_html( sprintf( __( 'You save %1$s (%2$d%%)', 'nexus-beauty' ), nexus_money( $disc ), $pct ) ) . '</p>';
	} elseif ( $pct > 0 ) {
		/* translators: 1: number of products, 2: percent */
		$out .= '<p class="bundle__note">' . esc_html( sprintf( __( 'Bundles of %1$d or more get %2$d%% off.', 'nexus-beauty' ), $min, $pct ) ) . '</p>';
	}
	/* translators: %d: number of items */
	$out .= '<button class="btn btn--primary btn--lg" type="button" data-nx-bundle-add>' . esc_html( sprintf( _n( 'Add %d to bag', 'Add all %d to bag', $n, 'nexus-beauty' ), $n ) ) . '</button>';
	$out .= '</div></div>';
	return $out;
}

/**
 * Frequently bought together: this product + up to two cross-sells.
 *
 * @param WC_Product $product Product.
 */
function nexus_fbt_section( $product ) {
	$ids = array_slice( $product->get_cross_sell_ids(), 0, 2 );
	if ( ! $ids ) {
		return;
	}
	$list = array_merge( array( $product ), array_filter( array_map( 'wc_get_product', $ids ) ) );
	$html = nexus_bundle_html( $list, $product->get_name() . ' ' . __( 'routine', 'nexus-beauty' ) );
	if ( ! $html ) {
		return;
	}
	$title = nexus_meta( $product, '_nx_fbt_title' );
	$text  = nexus_meta( $product, '_nx_fbt_text' );
	?>
	<section class="section section--tint" aria-labelledby="fbt-h">
		<div class="wrap">
			<div class="sec-head"><div><p class="eyebrow"><?php esc_html_e( 'Frequently bought together', 'nexus-beauty' ); ?></p><h2 class="h2" id="fbt-h"><?php echo esc_html( $title ? $title : __( 'Complete the routine', 'nexus-beauty' ) ); ?></h2>
				<?php if ( $text || (int) nexus_opt( 'bundle_percent' ) ) : ?>
					<?php /* translators: 1: number of products, 2: percent */ ?>
					<p class="lede"><?php echo esc_html( $text ? $text : sprintf( __( 'Add them together and save %2$d%% on %1$d or more.', 'nexus-beauty' ), max( 2, (int) nexus_opt( 'bundle_min' ) ), (int) nexus_opt( 'bundle_percent' ) ) ); ?></p>
				<?php endif; ?>
			</div></div>
			<?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in nexus_bundle_html(). ?>
		</div>
	</section>
	<?php
}

/**
 * Reviews section (design: rv-summary, rv list, or the launch box when there are none yet).
 *
 * @param WC_Product $product Product.
 */
function nexus_reviews_section( $product ) {
	if ( ! comments_open( $product->get_id() ) && ! $product->get_review_count() ) {
		return;
	}
	$count = (int) $product->get_review_count();
	$wear  = nexus_lines( nexus_meta( $product, '_nx_wear' ), 3 );
	?>
	<section class="section" id="reviews" aria-labelledby="rvs-h">
		<div class="wrap" style="display:grid;gap:28px">
			<div class="sec-head" style="margin:0"><div><p class="eyebrow"><?php esc_html_e( 'Reviews', 'nexus-beauty' ); ?></p><h2 class="h2" id="rvs-h"><?php esc_html_e( 'What customers say', 'nexus-beauty' ); ?></h2></div>
				<?php if ( $count && comments_open( $product->get_id() ) ) : ?><button class="btn btn--ghost" type="button" data-nx-review-toggle><?php esc_html_e( 'Write a review', 'nexus-beauty' ); ?></button><?php endif; ?>
			</div>
			<?php if ( $count ) : ?>
				<?php
				$avg    = (float) $product->get_average_rating();
				$counts = $product->get_rating_counts();
				?>
				<div class="rv-summary">
					<div class="score">
						<span class="score__big"><?php echo esc_html( number_format_i18n( $avg, 1 ) ); ?></span>
						<?php /* translators: %d: review count */ ?>
						<div><span class="stars" style="--pct:<?php echo esc_attr( round( $avg / 5 * 100 ) ); ?>%"></span><p style="font-size:13px;color:var(--ink-2)"><?php echo esc_html( sprintf( _n( 'Based on %d review', 'Based on %d reviews', $count, 'nexus-beauty' ), $count ) ); ?></p></div>
					</div>
					<div class="bars">
						<?php for ( $s = 5; $s >= 1; $s-- ) : ?>
							<?php $n = isset( $counts[ $s ] ) ? (int) $counts[ $s ] : 0; ?>
							<div><span><?php echo esc_html( $s ); ?> ★</span><i style="--w:<?php echo esc_attr( $count ? round( $n / $count * 100 ) : 0 ); ?>%"></i><span><?php echo esc_html( $n ); ?></span></div>
						<?php endfor; ?>
					</div>
				</div>
				<div class="rv-list">
					<?php
					$reviews = get_comments( array( 'post_id' => $product->get_id(), 'status' => 'approve', 'type' => 'review', 'number' => 20, 'parent' => 0 ) );
					foreach ( $reviews as $c ) {
						$rating   = (int) get_comment_meta( $c->comment_ID, 'rating', true );
						$verified = wc_review_is_from_verified_owner( $c->comment_ID );
						?>
						<article class="rv">
							<div class="rv__who"><b><?php echo esc_html( $c->comment_author ); ?></b><span><?php echo esc_html( get_comment_date( wc_date_format(), $c ) ); ?></span><?php if ( $verified ) : ?><span class="verified"><?php esc_html_e( 'Verified buyer', 'nexus-beauty' ); ?></span><?php endif; ?></div>
							<div>
								<?php if ( $rating ) : ?><span class="stars rv__stars" style="--pct:<?php echo esc_attr( $rating * 20 ); ?>%" role="img" aria-label="<?php /* translators: %d: stars */ echo esc_attr( sprintf( __( 'Rated %d out of 5', 'nexus-beauty' ), $rating ) ); ?>"></span><?php endif; ?>
								<?php echo wp_kses_post( wpautop( $c->comment_content ) ); ?>
							</div>
						</article>
						<?php
					}
					?>
				</div>
			<?php else : ?>
				<div class="launch-rv">
					<div><h3><?php echo esc_html( nexus_is_new( $product ) ? __( 'No reviews yet. This product launched this month.', 'nexus-beauty' ) : __( 'No reviews yet.', 'nexus-beauty' ) ); ?></h3><p><?php esc_html_e( 'Try it and tell us what you think. Honest reviews, good or bad, help other shoppers choose.', 'nexus-beauty' ); ?></p></div>
					<?php if ( comments_open( $product->get_id() ) ) : ?><button class="btn btn--ghost" type="button" data-nx-review-toggle><?php esc_html_e( 'Write the first review', 'nexus-beauty' ); ?></button><?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ( $wear ) : ?>
				<div class="facts" style="background:var(--blush)">
					<h2><?php esc_html_e( 'How long it lasts, by surface', 'nexus-beauty' ); ?></h2>
					<div class="wear" aria-label="<?php esc_attr_e( 'Wear time by surface', 'nexus-beauty' ); ?>">
						<?php foreach ( $wear as $w ) : ?><div><span><?php echo esc_html( $w[0] ); ?></span><i style="--w:<?php echo esc_attr( min( 100, absint( $w[1] ) ) ); ?>%"></i><span><?php echo esc_html( $w[2] ); ?></span></div><?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
			<?php
			if ( comments_open( $product->get_id() ) ) {
				comments_template();
			}
			?>
		</div>
	</section>
	<?php
}

/**
 * Questions and answers (design: #qa).
 *
 * @param WC_Product $product Product.
 */
function nexus_qa_section( $product ) {
	$faq = nexus_lines( nexus_meta( $product, '_nx_faq' ), 2 );
	if ( ! $faq ) {
		return;
	}
	?>
	<section class="section section--tint" id="qa" aria-labelledby="qa-h">
		<div class="wrap" style="display:grid;gap:24px">
			<?php /* translators: %s: product */ ?>
			<div><p class="eyebrow"><?php esc_html_e( 'Questions & answers', 'nexus-beauty' ); ?></p><h2 class="h2" id="qa-h"><?php esc_html_e( 'Questions about this product', 'nexus-beauty' ); ?></h2></div>
			<?php echo nexus_faq_html( $faq ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</section>
	<?php
	nexus_faq_schema( $faq );
}

/**
 * "You may also like": up-sells, or related products.
 *
 * @param WC_Product $product Product.
 */
function nexus_related_section( $product ) {
	$ids = $product->get_upsell_ids();
	if ( count( $ids ) < 2 ) {
		$ids = wc_get_related_products( $product->get_id(), 4, $product->get_upsell_ids() );
		$ids = array_merge( $product->get_upsell_ids(), $ids );
	}
	$list = array_filter(
		array_map( 'wc_get_product', array_slice( array_unique( $ids ), 0, 4 ) ),
		function ( $p ) {
			return $p && $p->is_visible();
		}
	);
	if ( ! $list ) {
		return;
	}
	$cat = nexus_top_cat( $product );
	?>
	<section class="section" aria-labelledby="rel-h">
		<div class="wrap">
			<div class="sec-head"><div><p class="eyebrow"><?php esc_html_e( 'You may also like', 'nexus-beauty' ); ?></p><h2 class="h2" id="rel-h"><?php esc_html_e( 'More to go with it', 'nexus-beauty' ); ?></h2></div>
				<?php if ( $cat ) : ?>
					<?php /* translators: %s: category */ ?>
					<a class="link-arrow" href="<?php echo esc_url( get_term_link( $cat ) ); ?>"><?php echo esc_html( sprintf( __( 'Shop all %s', 'nexus-beauty' ), strtolower( $cat->name ) ) ); ?></a>
				<?php endif; ?>
			</div>
			<?php echo nexus_cards_grid( $list ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
	</section>
	<?php
}

/**
 * Recently viewed (filled in by JavaScript so it works with page caching).
 *
 * @param int $limit Limit.
 * @return string
 */
function nexus_recent_block( $limit = 4 ) {
	return '<section class="section section--tint" data-nx-recent data-limit="' . (int) $limit . '" hidden><div class="wrap"><div class="sec-head"><div><p class="eyebrow">' . esc_html__( 'Recently viewed', 'nexus-beauty' ) . '</p><h2 class="h2">' . esc_html__( 'Pick up where you left off', 'nexus-beauty' ) . '</h2></div></div><div class="grid" data-nx-recent-list></div></div></section>';
}

/**
 * Sticky add-to-bag bar (design: .sticky-atc).
 *
 * @param WC_Product $product Product.
 */
function nexus_sticky_bar( $product ) {
	if ( ! $product->is_purchasable() || ! $product->is_in_stock() || ! $product->is_type( array( 'simple', 'variable' ) ) ) {
		return;
	}
	list( $now ) = nexus_price_parts( $product );
	list( $brand ) = nexus_brand( $product );
	$size = nexus_meta( $product, '_nx_size' );
	?>
	<div class="sticky-atc" aria-label="<?php esc_attr_e( 'Quick add to bag', 'nexus-beauty' ); ?>" aria-hidden="true">
		<div class="sticky-atc__info"><b><?php echo esc_html( nexus_short_name( $product, $brand ) ); ?><?php if ( $size ) : ?> · <span data-size-label><?php echo esc_html( $size ); ?></span><?php endif; ?></b><span data-pdp-price><?php echo esc_html( ( $product->is_type( 'variable' ) ? __( 'From', 'nexus-beauty' ) . ' ' : '' ) . nexus_money( $now ) ); ?></span></div>
		<button class="btn btn--primary" type="button" data-nx-sticky-btn tabindex="-1"><?php echo $product->is_type( 'simple' ) ? esc_html__( 'Add to bag', 'nexus-beauty' ) : esc_html__( 'Choose options', 'nexus-beauty' ); ?></button>
	</div>
	<?php
}

/* ---------- Video ---------- */

/**
 * YouTube video ID from a URL.
 *
 * @param string $url URL.
 * @return string
 */
function nexus_youtube_id( $url ) {
	if ( preg_match( '~(?:youtu\.be/|youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/))([A-Za-z0-9_-]{11})~', (string) $url, $m ) ) {
		return $m[1];
	}
	return '';
}

/**
 * Lightweight YouTube embed: a thumbnail until clicked, then the privacy-enhanced player.
 *
 * @param string $url   Video URL.
 * @param string $title Title.
 * @return string
 */
function nexus_video_embed( $url, $title ) {
	$id = nexus_youtube_id( $url );
	if ( ! $id ) {
		return '';
	}
	return sprintf(
		'<a class="video" href="%1$s" target="_blank" rel="noopener" data-nx-yt="%2$s" data-title="%3$s" aria-label="%4$s"><img src="https://i.ytimg.com/vi/%2$s/hqdefault.jpg" alt="" loading="lazy" decoding="async" width="480" height="360"><span class="video__play">%5$s</span></a>',
		esc_url( 'https://www.youtube.com/watch?v=' . $id ),
		esc_attr( $id ),
		esc_attr( $title ),
		/* translators: %s: video title */
		esc_attr( sprintf( __( 'Play video: %s', 'nexus-beauty' ), $title ) ),
		nexus_icon( 'play' )
	);
}
