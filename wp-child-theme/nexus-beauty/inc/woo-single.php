<?php
/**
 * Product page: brand, tagline, unit price, batch stamp, delivery estimate, buy now,
 * "is it right for me", extra tabs, frequently bought together and sticky add to bag.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

/**
 * Unit price text, e.g. "Rs 81 per ml".
 *
 * @param WC_Product $product Product or variation.
 * @return string
 */
function nexus_unit_price( $product ) {
	$vol = (float) nexus_meta( $product, '_nx_volume' );
	$price = (float) wc_get_price_to_display( $product );
	if ( $vol <= 0 || $price <= 0 ) {
		return '';
	}
	$unit = nexus_meta( $product, '_nx_unit' );
	$unit = $unit ? $unit : 'ml';
	$per  = $price / $vol;
	/* translators: 1: price per unit, 2: unit (ml, g) */
	return sprintf( __( '%1$s per %2$s', 'nexus-beauty' ), wp_strip_all_tags( wc_price( $per, array( 'decimals' => $per < 100 ? 1 : 0 ) ) ), $unit );
}

/**
 * Batch stamp HTML.
 *
 * @param WC_Product $product Product or variation.
 * @return string
 */
function nexus_batch_stamp( $product ) {
	$batch = nexus_meta( $product, '_nx_batch' );
	$exp   = nexus_meta( $product, '_nx_expiry' );
	if ( ! $batch && ! $exp ) {
		return '';
	}
	$mfg  = nexus_meta( $product, '_nx_mfg' );
	$rows = '';
	if ( $batch ) {
		$rows .= '<span>' . esc_html__( 'BATCH', 'nexus-beauty' ) . ' <b>' . esc_html( $batch ) . '</b></span>';
	}
	if ( $mfg ) {
		$rows .= '<span>' . esc_html__( 'MFG', 'nexus-beauty' ) . ' <b>' . esc_html( $mfg ) . '</b></span>';
	}
	if ( $exp ) {
		$rows .= '<span>' . esc_html__( 'EXP', 'nexus-beauty' ) . ' <b>' . esc_html( $exp ) . '</b></span>';
	}
	return '<div class="nx-batch"><strong>✓ ' . esc_html__( 'On your box', 'nexus-beauty' ) . '</strong>' . $rows . '</div>';
}

// Badges, brand and tagline around the title.
add_action(
	'woocommerce_single_product_summary',
	function () {
		global $product;
		nexus_print_badges( $product );
		list( $brand, $url ) = nexus_brand( $product );
		if ( $brand ) {
			echo $url ? '<a class="nx-single__brand" href="' . esc_url( $url ) . '">' . esc_html( $brand ) . '</a>' : '<p class="nx-single__brand">' . esc_html( $brand ) . '</p>';
		}
	},
	3
);
add_action(
	'woocommerce_single_product_summary',
	function () {
		global $product;
		$tagline = nexus_meta( $product, '_nx_tagline' );
		if ( $tagline ) {
			echo '<p class="nx-single__tagline">' . esc_html( $tagline ) . '</p>';
		}
	},
	6
);

// Unit price under the price.
add_action(
	'woocommerce_single_product_summary',
	function () {
		global $product;
		$unit = $product->is_type( 'variable' ) ? '' : nexus_unit_price( $product );
		echo '<p class="nx-unit" data-nx-unit' . ( $unit ? '' : ' hidden' ) . '>' . esc_html( $unit ) . '</p>';
	},
	11
);

// Batch stamp before the add-to-cart form.
add_action(
	'woocommerce_single_product_summary',
	function () {
		global $product;
		if ( ! nexus_opt( 'show_batch' ) ) {
			return;
		}
		echo '<div data-nx-batch>' . nexus_batch_stamp( $product ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in nexus_batch_stamp().
	},
	25
);

// Variation data for JS: unit price, batch stamp and size.
add_filter(
	'woocommerce_available_variation',
	function ( $data, $product, $variation ) {
		$data['nx_unit']  = nexus_unit_price( $variation );
		$data['nx_batch'] = nexus_opt( 'show_batch' ) ? nexus_batch_stamp( $variation ) : '';
		$data['nx_size']  = nexus_meta( $variation, '_nx_size' );
		return $data;
	},
	10,
	3
);

// "Buy now" button: adds to bag and goes straight to checkout.
add_action(
	'woocommerce_after_add_to_cart_button',
	function () {
		global $product;
		if ( ! $product->is_purchasable() || ! $product->is_in_stock() || $product->is_type( array( 'external', 'grouped' ) ) ) {
			return;
		}
		// Submits the same form with the product ID, and flags the request so it goes straight to checkout.
		printf(
			'<button type="submit" name="add-to-cart" value="%1$d" formaction="%2$s" class="nx-btn nx-btn--dark nx-buy-now">%3$s</button>',
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

// Delivery estimate, reassurance and WhatsApp help under the form.
add_action(
	'woocommerce_after_add_to_cart_form',
	function () {
		global $product;
		$cities = nexus_cities();
		?>
		<div class="nx-eta" data-nx-eta>
			<div class="nx-eta__row"><?php echo nexus_icon( 'truck' ); // phpcs:ignore ?>
				<label for="nx-city"><?php esc_html_e( 'Delivery to', 'nexus-beauty' ); ?></label>
				<select id="nx-city" data-nx-city>
					<?php foreach ( $cities as $city => $days ) : ?>
						<option value="<?php echo esc_attr( $days ); ?>"><?php echo esc_html( $city ); ?></option>
					<?php endforeach; ?>
					<option value="<?php echo esc_attr( nexus_opt( 'default_days' ) ); ?>"><?php esc_html_e( 'Other city', 'nexus-beauty' ); ?></option>
				</select>
			</div>
			<p data-nx-eta-text></p>
			<p class="nx-eta__cut" data-nx-eta-cut></p>
		</div>
		<ul class="nx-assure">
			<li><?php echo nexus_icon( 'shield' ); // phpcs:ignore ?><span><b><?php esc_html_e( '100% original', 'nexus-beauty' ); ?></b><?php esc_html_e( 'From the authorised distributor', 'nexus-beauty' ); ?></span></li>
			<li><?php echo nexus_icon( 'cash' ); // phpcs:ignore ?><span><b><?php esc_html_e( 'Cash on delivery', 'nexus-beauty' ); ?></b><?php esc_html_e( 'Pay when it arrives', 'nexus-beauty' ); ?></span></li>
			<li><?php echo nexus_icon( 'truck' ); // phpcs:ignore ?><span><b><?php esc_html_e( 'Free delivery', 'nexus-beauty' ); ?></b><?php /* translators: %s: amount */ printf( esc_html__( 'On orders over %s', 'nexus-beauty' ), esc_html( nexus_price_text( nexus_opt( 'free_shipping' ) ) ) ); ?></span></li>
			<li><?php echo nexus_icon( 'return' ); // phpcs:ignore ?><span><b><?php esc_html_e( 'Easy returns', 'nexus-beauty' ); ?></b><?php esc_html_e( 'On unopened products', 'nexus-beauty' ); ?></span></li>
		</ul>
		<?php if ( nexus_opt( 'whatsapp' ) ) : ?>
			<p class="nx-help"><?php esc_html_e( 'Not sure it suits your skin?', 'nexus-beauty' ); ?> <a href="<?php echo esc_url( nexus_whatsapp_url( sprintf( /* translators: %s: product name */ __( 'Hi, is %s right for my skin?', 'nexus-beauty' ), $product->get_name() ) ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Ask us on WhatsApp', 'nexus-beauty' ); ?></a></p>
		<?php endif; ?>
		<?php
	}
);

// "Is it right for me?" and key ingredients, above the tabs.
add_action(
	'woocommerce_after_single_product_summary',
	function () {
		global $product;
		$good = nexus_lines( nexus_meta( $product, '_nx_good_for' ) );
		$not  = nexus_lines( nexus_meta( $product, '_nx_not_for' ) );
		$ing  = nexus_lines( nexus_meta( $product, '_nx_ingredients' ), 3 );
		if ( ! $good && ! $not && ! $ing ) {
			return;
		}
		echo '<section class="nx-fit-wrap" aria-labelledby="nx-fit-h"><h2 id="nx-fit-h" class="nx-h2">' . esc_html__( 'Is it right for me?', 'nexus-beauty' ) . '</h2>';
		if ( $good || $not ) {
			echo '<div class="nx-fit">';
			if ( $good ) {
				echo '<div class="nx-fit__yes"><h3>' . esc_html__( 'Good for you if you have', 'nexus-beauty' ) . '</h3><ul>';
				foreach ( $good as $g ) {
					echo '<li>' . esc_html( $g ) . '</li>';
				}
				echo '</ul></div>';
			}
			if ( $not ) {
				echo '<div class="nx-fit__no"><h3>' . esc_html__( 'Choose something else if', 'nexus-beauty' ) . '</h3><ul>';
				foreach ( $not as $n ) {
					echo '<li>' . esc_html( $n ) . '</li>';
				}
				echo '</ul></div>';
			}
			echo '</div>';
		}
		if ( $ing ) {
			echo '<h3 class="nx-h3">' . esc_html__( 'Key ingredients', 'nexus-beauty' ) . '</h3><div class="nx-ingr">';
			foreach ( $ing as $i ) {
				printf( '<div><span>%s</span><b>%s</b><p>%s</p></div>', esc_html( $i[1] ), esc_html( $i[0] ), esc_html( $i[2] ) );
			}
			echo '</div>';
		}
		echo '</section>';
	},
	5
);

// Extra tabs: how to use, ingredients list, questions, video.
add_filter(
	'woocommerce_product_tabs',
	function ( $tabs ) {
		global $product;
		if ( ! $product ) {
			return $tabs;
		}
		if ( nexus_lines( nexus_meta( $product, '_nx_howto' ) ) ) {
			$tabs['nx_howto'] = array(
				'title'    => __( 'How to use', 'nexus-beauty' ),
				'priority' => 15,
				'callback' => function () use ( $product ) {
					echo '<ol class="nx-steps">';
					foreach ( nexus_lines( nexus_meta( $product, '_nx_howto' ) ) as $step ) {
						echo '<li>' . esc_html( $step ) . '</li>';
					}
					echo '</ol>';
				},
			);
		}
		if ( nexus_meta( $product, '_nx_inci' ) ) {
			$tabs['nx_inci'] = array(
				'title'    => __( 'Ingredients', 'nexus-beauty' ),
				'priority' => 16,
				'callback' => function () use ( $product ) {
					echo '<p>' . esc_html( nexus_meta( $product, '_nx_inci' ) ) . '</p>';
				},
			);
		}
		if ( nexus_lines( nexus_meta( $product, '_nx_faq' ), 2 ) ) {
			$tabs['nx_faq'] = array(
				'title'    => __( 'Questions', 'nexus-beauty' ),
				'priority' => 25,
				'callback' => function () use ( $product ) {
					echo '<div class="nx-faq">';
					foreach ( nexus_lines( nexus_meta( $product, '_nx_faq' ), 2 ) as $qa ) {
						printf( '<details><summary>%s</summary><p>%s</p></details>', esc_html( $qa[0] ), esc_html( $qa[1] ) );
					}
					echo '</div>';
				},
			);
		}
		if ( nexus_youtube_id( nexus_meta( $product, '_nx_video' ) ) ) {
			$tabs['nx_video'] = array(
				'title'    => __( 'Video', 'nexus-beauty' ),
				'priority' => 26,
				'callback' => function () use ( $product ) {
					echo nexus_video_embed( nexus_meta( $product, '_nx_video' ), $product->get_name() ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in function.
				},
			);
		}
		return $tabs;
	},
	20
);

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
		'<a class="nx-video" href="%1$s" target="_blank" rel="noopener" data-nx-yt="%2$s" data-title="%3$s" aria-label="%4$s"><img src="https://i.ytimg.com/vi/%2$s/hqdefault.jpg" alt="" loading="lazy" decoding="async" width="480" height="360"><span class="nx-video__play">%5$s</span></a>',
		esc_url( 'https://www.youtube.com/watch?v=' . $id ),
		esc_attr( $id ),
		esc_attr( $title ),
		/* translators: %s: video title */
		esc_attr( sprintf( __( 'Play video: %s', 'nexus-beauty' ), $title ) ),
		nexus_icon( 'play' )
	);
}

/**
 * Bundle builder markup used by "Frequently bought together" and [nexus_kit].
 *
 * @param array  $rows    [ [ WC_Product, role, note, locked ], ... ].
 * @param string $name    Bundle name.
 * @param string $heading Heading HTML (already escaped).
 * @return string
 */
function nexus_bundle_html( $rows, $name, $heading = '' ) {
	$rows = array_filter(
		$rows,
		function ( $r ) {
			return $r[0] instanceof WC_Product && $r[0]->is_purchasable() && $r[0]->is_in_stock() && $r[0]->is_type( 'simple' );
		}
	);
	if ( count( $rows ) < 2 ) {
		return '';
	}
	$pct = (int) nexus_opt( 'bundle_percent' );
	$min = max( 2, (int) nexus_opt( 'bundle_min' ) );
	ob_start();
	?>
	<div class="nx-bundle" data-nx-bundle data-name="<?php echo esc_attr( $name ); ?>">
		<?php echo $heading; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped by caller. ?>
		<div class="nx-bundle__grid">
			<div class="nx-bundle__list" role="group" aria-label="<?php echo esc_attr( $name ); ?>">
				<?php foreach ( $rows as $r ) : ?>
					<?php
					$p      = $r[0];
					$price  = wc_get_price_to_display( $p );
					$locked = ! empty( $r[3] );
					?>
					<label class="nx-bundle__item">
						<input type="checkbox" checked value="<?php echo esc_attr( $p->get_id() ); ?>" data-price="<?php echo esc_attr( $price ); ?>" <?php disabled( $locked ); ?> <?php echo $locked ? 'data-locked' : ''; ?>>
						<span class="nx-bundle__thumb"><?php echo $p->get_image( 'woocommerce_gallery_thumbnail' ); // phpcs:ignore ?></span>
						<span class="nx-bundle__txt">
							<?php if ( $r[1] ) : ?><small><?php echo esc_html( $r[1] ); ?></small><?php endif; ?>
							<b><?php echo esc_html( $p->get_name() ); ?></b>
							<?php if ( $r[2] ) : ?><em><?php echo esc_html( $r[2] ); ?></em><?php endif; ?>
							<?php if ( ! $locked ) : ?><a href="<?php echo esc_url( $p->get_permalink() ); ?>"><?php esc_html_e( 'View product', 'nexus-beauty' ); ?></a><?php endif; ?>
						</span>
						<span class="nx-bundle__price"><?php echo wp_kses_post( wc_price( $price ) ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
			<div class="nx-bundle__buy">
				<div class="nx-row"><span><?php esc_html_e( 'Selected', 'nexus-beauty' ); ?>: <b data-nx-b-count></b></span><span data-nx-b-sub></span></div>
				<?php if ( $pct ) : ?>
					<div class="nx-row nx-row--disc" data-nx-b-disc-row>
						<?php /* translators: %d: discount percent */ ?>
						<span><?php printf( esc_html__( 'Bundle discount (%d%%)', 'nexus-beauty' ), (int) $pct ); ?></span><span data-nx-b-disc></span>
					</div>
				<?php endif; ?>
				<div class="nx-row nx-row--tot"><span><?php esc_html_e( 'Total', 'nexus-beauty' ); ?></span><b data-nx-b-total></b></div>
				<button class="nx-btn nx-btn--primary nx-btn--block" type="button" data-nx-b-add><?php esc_html_e( 'Add to bag', 'nexus-beauty' ); ?></button>
				<small data-nx-b-note><?php /* translators: 1: number of items, 2: percent */ printf( esc_html__( '%2$d%% off when you choose %1$d or more.', 'nexus-beauty' ), (int) $min, (int) $pct ); ?></small>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

// Frequently bought together: this product + its cross-sells.
add_action(
	'woocommerce_after_single_product_summary',
	function () {
		global $product;
		$ids = array_slice( $product->get_cross_sell_ids(), 0, 3 );
		if ( ! $ids ) {
			return;
		}
		$rows = array( array( $product, __( 'This item', 'nexus-beauty' ), '', true ) );
		foreach ( $ids as $id ) {
			$p = wc_get_product( $id );
			if ( $p ) {
				$rows[] = array( $p, '', '', false );
			}
		}
		$heading = '<div class="nx-sec-head"><p class="nx-eyebrow">' . esc_html__( 'Frequently bought together', 'nexus-beauty' ) . '</p><h2 class="nx-h2">' . esc_html__( 'Complete the routine', 'nexus-beauty' ) . '</h2></div>';
		echo '<section class="nx-fbt">' . nexus_bundle_html( $rows, $product->get_name() . ' ' . __( 'routine', 'nexus-beauty' ), $heading ) . '</section>'; // phpcs:ignore WordPress.Security.EscapeOutput
	},
	12
);

// Recently viewed (filled in by JavaScript so it works with page caching).
add_action(
	'woocommerce_after_single_product_summary',
	function () {
		echo nexus_recent_block( 8 ); // phpcs:ignore WordPress.Security.EscapeOutput
	},
	30
);

/**
 * Recently viewed container.
 *
 * @param int $limit Limit.
 * @return string
 */
function nexus_recent_block( $limit = 8 ) {
	return '<section class="nx-recent" data-nx-recent data-limit="' . (int) $limit . '" hidden><h2 class="nx-h2">' . esc_html__( 'Recently viewed', 'nexus-beauty' ) . '</h2><div class="nx-minis" data-nx-recent-list></div></section>';
}

// Sticky add-to-bag bar.
add_action(
	'wp_footer',
	function () {
		if ( ! is_product() ) {
			return;
		}
		$product = wc_get_product( get_queried_object_id() );
		if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
			return;
		}
		?>
		<div class="nx-sticky" data-nx-sticky aria-hidden="true">
			<div class="nx-sticky__info"><?php echo $product->get_image( 'woocommerce_gallery_thumbnail' ); // phpcs:ignore ?><div><b><?php echo esc_html( $product->get_name() ); ?></b><span><?php echo wp_kses_post( $product->get_price_html() ); ?></span></div></div>
			<button class="nx-btn nx-btn--primary" type="button" data-nx-sticky-btn tabindex="-1"><?php echo $product->is_type( 'simple' ) ? esc_html__( 'Add to bag', 'nexus-beauty' ) : esc_html__( 'Choose options', 'nexus-beauty' ); ?></button>
		</div>
		<?php
	}
);
