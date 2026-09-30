<?php
/**
 * Shortcodes for building pages in the block editor.
 *
 * [nexus_trust]
 * [nexus_product_tabs tabs="best,new,sale" limit="8" columns="4"]
 * [nexus_categories limit="12" parent="0"]
 * [nexus_brands limit="24"]
 * [nexus_kit ids="12,34,56" title="..." who="..." roles="Cleanse|Treat|Protect" how="...|...|..." tips="...|..." avoid="...|..."]
 * [nexus_videos urls="https://youtu.be/...,https://youtu.be/..." titles="Title one|Title two"]
 * [nexus_whatsapp text="Chat with us"]
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

/**
 * Split a "a|b|c" attribute.
 *
 * @param string $s Attribute.
 * @return array
 */
function nexus_pipe( $s ) {
	return array_values( array_filter( array_map( 'trim', explode( '|', (string) $s ) ), 'strlen' ) );
}

add_shortcode(
	'nexus_trust',
	function () {
		$items = array(
			array( 'shield', __( 'Batch & expiry shown', 'nexus-beauty' ), __( 'On every order', 'nexus-beauty' ) ),
			array( 'cash', __( 'Cash on delivery', 'nexus-beauty' ), __( 'Anywhere in Pakistan', 'nexus-beauty' ) ),
			array( 'truck', __( 'Free delivery', 'nexus-beauty' ), sprintf( /* translators: %s: amount */ __( 'Over %s', 'nexus-beauty' ), nexus_price_text( nexus_opt( 'free_shipping' ) ) ) ),
			array( 'chat', __( 'Free skin advice', 'nexus-beauty' ), __( 'On WhatsApp', 'nexus-beauty' ) ),
		);
		$out = '<ul class="nx-trust">';
		foreach ( $items as $i ) {
			$out .= '<li>' . nexus_icon( $i[0] ) . '<span><b>' . esc_html( $i[1] ) . '</b>' . esc_html( $i[2] ) . '</span></li>';
		}
		return $out . '</ul>';
	}
);

add_shortcode(
	'nexus_product_tabs',
	function ( $atts ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return '';
		}
		$a      = shortcode_atts( array( 'tabs' => 'best,new,sale', 'limit' => 8, 'columns' => 4 ), $atts, 'nexus_product_tabs' );
		$limit  = max( 1, min( 24, (int) $a['limit'] ) );
		$cols   = max( 2, min( 6, (int) $a['columns'] ) );
		$define = array(
			'best' => array( __( 'Best sellers', 'nexus-beauty' ), 'best_selling="true"' ),
			'new'  => array( __( 'New arrivals', 'nexus-beauty' ), 'orderby="date" order="DESC"' ),
			'sale' => array( __( 'On offer', 'nexus-beauty' ), 'on_sale="true"' ),
			'top'  => array( __( 'Top rated', 'nexus-beauty' ), 'top_rated="true"' ),
		);
		$tabs   = array_values( array_intersect( array_map( 'trim', explode( ',', $a['tabs'] ) ), array_keys( $define ) ) );
		if ( ! $tabs ) {
			return '';
		}
		$uid = wp_unique_id( 'nx-tabs-' );
		$out = '<div class="nx-ptabs" data-nx-tabs><div class="nx-tabs" role="tablist">';
		foreach ( $tabs as $i => $t ) {
			$out .= sprintf( '<button class="nx-chip" type="button" role="tab" id="%1$s-t%2$d" aria-controls="%1$s-p%2$d" aria-selected="%3$s">%4$s</button>', esc_attr( $uid ), $i, $i ? 'false' : 'true', esc_html( $define[ $t ][0] ) );
		}
		$out .= '</div>';
		foreach ( $tabs as $i => $t ) {
			$out .= sprintf( '<div class="nx-tabpanel" role="tabpanel" id="%1$s-p%2$d" aria-labelledby="%1$s-t%2$d"%3$s>', esc_attr( $uid ), $i, $i ? ' hidden' : '' );
			$out .= do_shortcode( sprintf( '[products limit="%d" columns="%d" %s]', $limit, $cols, $define[ $t ][1] ) );
			$out .= '</div>';
		}
		return $out . '</div>';
	}
);

add_shortcode(
	'nexus_categories',
	function ( $atts ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return '';
		}
		$a     = shortcode_atts( array( 'limit' => 12, 'parent' => 0, 'ids' => '' ), $atts, 'nexus_categories' );
		$args  = array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'number'     => (int) $a['limit'],
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
			'orderby'    => 'menu_order',
		);
		if ( $a['ids'] ) {
			$args['include'] = array_map( 'absint', explode( ',', $a['ids'] ) );
			$args['orderby'] = 'include';
		} else {
			$args['parent'] = (int) $a['parent'];
		}
		$terms = get_terms( $args );
		if ( ! $terms || is_wp_error( $terms ) ) {
			return '';
		}
		$out = '<div class="nx-cats">';
		foreach ( $terms as $t ) {
			$thumb = (int) get_term_meta( $t->term_id, 'thumbnail_id', true );
			$img   = $thumb ? wp_get_attachment_image( $thumb, 'woocommerce_thumbnail', false, array( 'alt' => '' ) ) : '<span class="nx-cats__ph" aria-hidden="true">' . esc_html( mb_substr( $t->name, 0, 1 ) ) . '</span>';
			$out  .= sprintf( '<a class="nx-cats__item" href="%s"><span class="nx-cats__img">%s</span><span class="nx-cats__name">%s</span></a>', esc_url( get_term_link( $t ) ), $img, esc_html( $t->name ) );
		}
		return $out . '</div>';
	}
);

add_shortcode(
	'nexus_brands',
	function ( $atts ) {
		if ( ! taxonomy_exists( 'product_brand' ) ) {
			return current_user_can( 'edit_posts' ) ? '<p class="nx-note">' . esc_html__( 'Brands need WooCommerce 9.6 or newer (Products > Brands).', 'nexus-beauty' ) . '</p>' : '';
		}
		$a     = shortcode_atts( array( 'limit' => 24 ), $atts, 'nexus_brands' );
		$terms = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => true, 'number' => (int) $a['limit'] ) );
		if ( ! $terms || is_wp_error( $terms ) ) {
			return '';
		}
		$out = '<div class="nx-brands">';
		foreach ( $terms as $t ) {
			$thumb = (int) get_term_meta( $t->term_id, 'thumbnail_id', true );
			$mark  = $thumb ? wp_get_attachment_image( $thumb, 'thumbnail', false, array( 'alt' => '' ) ) : '<span class="nx-brands__mark" aria-hidden="true">' . esc_html( mb_strtoupper( mb_substr( $t->name, 0, 2 ) ) ) . '</span>';
			$desc  = $t->description ? wp_trim_words( wp_strip_all_tags( $t->description ), 6, '…' ) : sprintf( /* translators: %d: product count */ _n( '%d product', '%d products', $t->count, 'nexus-beauty' ), $t->count );
			$out  .= sprintf( '<a class="nx-brands__item" href="%s">%s<b>%s</b><span>%s</span></a>', esc_url( get_term_link( $t ) ), $mark, esc_html( $t->name ), esc_html( $desc ) );
		}
		return $out . '</div>';
	}
);

add_shortcode(
	'nexus_kit',
	function ( $atts ) {
		if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'nexus_bundle_html' ) ) {
			return '';
		}
		$a     = shortcode_atts( array( 'ids' => '', 'title' => '', 'who' => '', 'roles' => '', 'how' => '', 'tips' => '', 'avoid' => '' ), $atts, 'nexus_kit' );
		$ids   = array_filter( array_map( 'absint', explode( ',', $a['ids'] ) ) );
		$roles = explode( '|', $a['roles'] );
		$how   = explode( '|', $a['how'] );
		$rows  = array();
		foreach ( array_values( $ids ) as $i => $id ) {
			$p = wc_get_product( $id );
			if ( $p ) {
				$rows[] = array( $p, isset( $roles[ $i ] ) ? trim( $roles[ $i ] ) : '', isset( $how[ $i ] ) ? trim( $how[ $i ] ) : '', false );
			}
		}
		if ( count( $rows ) < 2 ) {
			return current_user_can( 'edit_posts' ) ? '<p class="nx-note">' . esc_html__( 'Kit: add at least two in-stock simple product IDs to the ids="" attribute.', 'nexus-beauty' ) . '</p>' : '';
		}
		$title   = $a['title'] ? $a['title'] : __( 'Complete kit', 'nexus-beauty' );
		$heading = '<div class="nx-sec-head"><p class="nx-eyebrow">' . esc_html( sprintf( /* translators: %d: products */ __( '%d-product routine', 'nexus-beauty' ), count( $rows ) ) ) . '</p><h3 class="nx-h2">' . esc_html( $title ) . '</h3>' . ( $a['who'] ? '<p class="nx-lede">' . esc_html( $a['who'] ) . '</p>' : '' ) . '</div>';
		$guide   = '';
		if ( $a['tips'] || $a['avoid'] ) {
			$guide = '<div class="nx-kit__guide">';
			if ( $a['tips'] ) {
				$guide .= '<div><h4>' . esc_html__( 'Get the best results', 'nexus-beauty' ) . '</h4><ol>';
				foreach ( nexus_pipe( $a['tips'] ) as $t ) {
					$guide .= '<li>' . esc_html( $t ) . '</li>';
				}
				$guide .= '</ol></div>';
			}
			if ( $a['avoid'] ) {
				$guide .= '<div class="nx-kit__avoid"><h4>' . esc_html__( 'Avoid', 'nexus-beauty' ) . '</h4><ul>';
				foreach ( nexus_pipe( $a['avoid'] ) as $t ) {
					$guide .= '<li>' . esc_html( $t ) . '</li>';
				}
				$guide .= '</ul></div>';
			}
			$guide .= '</div>';
		}
		return '<section class="nx-kit">' . nexus_bundle_html( $rows, $title, $heading ) . $guide . '</section>';
	}
);

add_shortcode(
	'nexus_videos',
	function ( $atts ) {
		if ( ! function_exists( 'nexus_video_embed' ) ) {
			return '';
		}
		$a      = shortcode_atts( array( 'urls' => '', 'titles' => '' ), $atts, 'nexus_videos' );
		$urls   = array_filter( array_map( 'trim', explode( ',', $a['urls'] ) ) );
		$titles = explode( '|', $a['titles'] );
		$out    = '';
		foreach ( array_values( $urls ) as $i => $url ) {
			$title = isset( $titles[ $i ] ) ? trim( $titles[ $i ] ) : '';
			$embed = nexus_video_embed( $url, $title );
			if ( $embed ) {
				$out .= '<figure class="nx-videos__item">' . $embed . ( $title ? '<figcaption>' . esc_html( $title ) . '</figcaption>' : '' ) . '</figure>';
			}
		}
		return $out ? '<div class="nx-videos">' . $out . '</div>' : '';
	}
);

add_shortcode(
	'nexus_whatsapp',
	function ( $atts ) {
		$a = shortcode_atts( array( 'text' => __( 'Chat with us on WhatsApp', 'nexus-beauty' ), 'message' => '' ), $atts, 'nexus_whatsapp' );
		return '<a class="nx-btn nx-btn--wa" href="' . esc_url( nexus_whatsapp_url( $a['message'] ) ) . '" target="_blank" rel="noopener">' . nexus_icon( 'whatsapp' ) . esc_html( $a['text'] ) . '</a>';
	}
);
