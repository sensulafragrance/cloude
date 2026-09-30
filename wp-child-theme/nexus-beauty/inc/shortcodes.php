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

add_shortcode(
	'nexus_trust',
	function () {
		ob_start();
		nexus_trust_strip( '' );
		return ob_get_clean();
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
		$out = '<div class="ptabs" data-nx-tabs><div class="tabs" role="tablist">';
		foreach ( $tabs as $i => $t ) {
			$out .= sprintf( '<button class="chip" type="button" role="tab" id="%1$s-t%2$d" aria-controls="%1$s-p%2$d" aria-selected="%3$s" aria-pressed="%3$s">%4$s</button>', esc_attr( $uid ), $i, $i ? 'false' : 'true', esc_html( $define[ $t ][0] ) );
		}
		$out .= '</div>';
		foreach ( $tabs as $i => $t ) {
			$out .= sprintf( '<div role="tabpanel" id="%1$s-p%2$d" aria-labelledby="%1$s-t%2$d"%3$s>', esc_attr( $uid ), $i, $i ? ' hidden' : '' );
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
		return nexus_cat_tiles( $terms );
	}
);

add_shortcode(
	'nexus_brands',
	function ( $atts ) {
		if ( ! taxonomy_exists( 'product_brand' ) ) {
			return current_user_can( 'edit_posts' ) ? '<p class="nx-note">' . esc_html__( 'Brands need WooCommerce 9.6 or newer (Products > Brands).', 'nexus-beauty' ) . '</p>' : '';
		}
		$a = shortcode_atts( array( 'limit' => 300 ), $atts, 'nexus_brands' );
		ob_start();
		nexus_brand_tiles( (int) $a['limit'], false );
		return ob_get_clean();
	}
);

add_shortcode(
	'nexus_kit',
	function ( $atts ) {
		if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'nexus_bundle_html' ) ) {
			return '';
		}
		$a     = shortcode_atts( array( 'ids' => '', 'skus' => '', 'tag' => '', 'limit' => 5, 'title' => '', 'who' => '', 'roles' => '', 'how' => '', 'tips' => '', 'avoid' => '' ), $atts, 'nexus_kit' );
		$roles = explode( '|', $a['roles'] );
		$how   = explode( '|', $a['how'] );
		// Products by ID, or by SKU (SKUs survive exports and imports between sites).
		$list = array_filter( array_map( 'absint', explode( ',', $a['ids'] ) ) );
		if ( ! $list && $a['skus'] ) {
			$list = array_map(
				function ( $sku ) {
					return (int) wc_get_product_id_by_sku( trim( $sku ) );
				},
				explode( ',', $a['skus'] )
			);
		}
		$rows = array();
		foreach ( array_values( $list ) as $i => $id ) {
			$p = $id ? wc_get_product( $id ) : null;
			if ( $p && 'publish' === $p->get_status() ) {
				$rows[] = array( $p, isset( $roles[ $i ] ) ? trim( $roles[ $i ] ) : '', isset( $how[ $i ] ) ? trim( $how[ $i ] ) : '', false );
			}
		}
		// Fallback: best-selling in-stock products with these tags, so kits fill themselves from your own catalogue.
		if ( count( $rows ) < 2 && $a['tag'] ) {
			$rows  = array();
			$found = wc_get_products(
				array(
					'status'       => 'publish',
					'type'         => 'simple',
					'stock_status' => 'instock',
					'tag'          => array_map( 'sanitize_title', explode( ',', $a['tag'] ) ),
					'limit'        => max( 2, min( 8, (int) $a['limit'] ) ),
					'meta_key'     => 'total_sales', // phpcs:ignore WordPress.DB.SlowDBQuery
					'orderby'      => 'meta_value_num',
					'order'        => 'DESC',
				)
			);
			foreach ( $found as $p ) {
				$rows[] = array( $p, '', '', false );
			}
		}
		if ( count( $rows ) < 2 ) {
			return current_user_can( 'edit_posts' ) ? '<p class="nx-note">' . esc_html__( 'Kit: no matching products yet. Add product IDs or SKUs, or tag at least two in-stock products with the kit\'s tag.', 'nexus-beauty' ) . '</p>' : '';
		}
		$title  = $a['title'] ? $a['title'] : __( 'Complete kit', 'nexus-beauty' );
		$bundle = nexus_bundle_html( wp_list_pluck( $rows, 0 ), $title );
		if ( ! $bundle ) {
			return '';
		}
		/* translators: %d: products */
		$out  = '<section class="kit"><div class="sec-head"><div><p class="eyebrow">' . esc_html( sprintf( __( '%d-product routine', 'nexus-beauty' ), count( $rows ) ) ) . '</p><h2 class="h2">' . esc_html( $title ) . '</h2>' . ( $a['who'] ? '<p class="lede">' . esc_html( $a['who'] ) . '</p>' : '' ) . '</div></div>';
		$steps = '';
		foreach ( $rows as $r ) {
			if ( $r[1] || $r[2] ) {
				list( $brand ) = nexus_brand( $r[0] );
				$steps        .= '<li><span>' . ( $r[1] ? '<b>' . esc_html( $r[1] ) . ':</b> ' : '' ) . esc_html( $r[0]->get_name() ) . ( $r[2] ? '. ' . esc_html( $r[2] ) : '' ) . '</span></li>';
			}
		}
		$out .= $steps ? '<ol class="kit-steps">' . $steps . '</ol>' : '';
		$out .= $bundle;
		if ( $a['tips'] || $a['avoid'] ) {
			$out .= '<div class="kit-guide">';
			if ( $a['tips'] ) {
				$out .= '<div><h4>' . esc_html__( 'Get the best results', 'nexus-beauty' ) . '</h4><ol>' . implode( '', array_map( fn( $t ) => '<li>' . esc_html( $t ) . '</li>', nexus_pipe( $a['tips'] ) ) ) . '</ol></div>';
			}
			if ( $a['avoid'] ) {
				$out .= '<div><h4>' . esc_html__( 'Avoid', 'nexus-beauty' ) . '</h4><ul>' . implode( '', array_map( fn( $t ) => '<li>' . esc_html( $t ) . '</li>', nexus_pipe( $a['avoid'] ) ) ) . '</ul></div>';
			}
			$out .= '</div>';
		}
		return $out . '</section>';
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
				$out .= '<figure>' . $embed . ( $title ? '<figcaption>' . esc_html( $title ) . '</figcaption>' : '' ) . '</figure>';
			}
		}
		return $out ? '<div class="videos">' . $out . '</div>' : '';
	}
);

add_shortcode(
	'nexus_whatsapp',
	function ( $atts ) {
		$a = shortcode_atts( array( 'text' => __( 'Chat with us on WhatsApp', 'nexus-beauty' ), 'message' => '' ), $atts, 'nexus_whatsapp' );
		return '<a class="nx-btn nx-btn--wa" href="' . esc_url( nexus_whatsapp_url( $a['message'] ) ) . '" target="_blank" rel="noopener">' . nexus_icon( 'whatsapp' ) . esc_html( $a['text'] ) . '</a>';
	}
);

/**
 * Delivery times table built from Customize > Nexus Beauty > Delivery.
 */
add_shortcode(
	'nexus_delivery_table',
	function () {
		$free = (float) nexus_opt( 'free_shipping' );
		$out  = '<div class="nx-table-wrap"><table class="nx-table"><thead><tr><th scope="col">' . esc_html__( 'City', 'nexus-beauty' ) . '</th><th scope="col">' . esc_html__( 'Delivery time', 'nexus-beauty' ) . '</th><th scope="col">' . esc_html__( 'Estimated arrival if you order now', 'nexus-beauty' ) . '</th></tr></thead><tbody>';
		$rows = nexus_cities();
		$rows[ __( 'Other cities', 'nexus-beauty' ) ] = nexus_opt( 'default_days' );
		foreach ( $rows as $city => $days ) {
			/* translators: %s: day range such as 1–2 */
			$out .= '<tr><td>' . esc_html( $city ) . '</td><td>' . esc_html( sprintf( __( '%s working days', 'nexus-beauty' ), str_replace( '-', '–', $days ) ) ) . '</td><td>' . esc_html( nexus_eta_text( $city ) ) . '</td></tr>';
		}
		$out .= '</tbody></table></div>';
		if ( $free > 0 ) {
			/* translators: %s: amount */
			$out .= '<p class="nx-note">' . esc_html( sprintf( __( 'Free delivery on orders over %s. Orders placed before the cut-off leave the same day.', 'nexus-beauty' ), nexus_price_text( $free ) ) ) . '</p>';
		}
		return $out;
	}
);

/**
 * Contact form. Sends an email to the support address (Customize > Nexus Beauty > Store & contact).
 */
add_shortcode(
	'nexus_contact_form',
	function () {
		$sent = isset( $_GET['nx-sent'] ) ? sanitize_key( wp_unslash( $_GET['nx-sent'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		ob_start();
		if ( 'ok' === $sent ) {
			echo '<p class="nx-note nx-note--ok" role="status">' . esc_html__( 'Thanks! We\'ve received your message and will reply within 2 hours during opening hours.', 'nexus-beauty' ) . '</p>';
		} elseif ( 'err' === $sent ) {
			echo '<p class="nx-note nx-note--err" role="alert">' . esc_html__( 'Please fill in your name, a phone number or email, and a message of at least 10 characters.', 'nexus-beauty' ) . '</p>';
		}
		?>
		<form class="nx-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="nexus_contact">
			<input type="hidden" name="back" value="<?php echo esc_url( get_permalink() ); ?>">
			<?php wp_nonce_field( 'nexus_contact', 'nx_contact_nonce' ); ?>
			<p class="nx-hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></p>
			<p><label for="nx-cname"><?php esc_html_e( 'Your name', 'nexus-beauty' ); ?></label><input id="nx-cname" name="cname" autocomplete="name" required></p>
			<p><label for="nx-creply"><?php esc_html_e( 'Mobile number or email', 'nexus-beauty' ); ?></label><input id="nx-creply" name="creply" autocomplete="tel" required></p>
			<p><label for="nx-ctopic"><?php esc_html_e( 'Topic', 'nexus-beauty' ); ?></label><select id="nx-ctopic" name="ctopic">
				<?php foreach ( array( __( 'Question about a product', 'nexus-beauty' ), __( 'My order', 'nexus-beauty' ), __( 'Returns and refunds', 'nexus-beauty' ), __( 'Skin or hair advice', 'nexus-beauty' ), __( 'Stock my brand', 'nexus-beauty' ), __( 'Something else', 'nexus-beauty' ) ) as $t ) : ?>
					<option><?php echo esc_html( $t ); ?></option>
				<?php endforeach; ?>
			</select></p>
			<p><label for="nx-cmsg"><?php esc_html_e( 'Message', 'nexus-beauty' ); ?></label><textarea id="nx-cmsg" name="cmsg" rows="5" required minlength="10"></textarea></p>
			<p><button class="btn btn--primary" type="submit"><?php esc_html_e( 'Send message', 'nexus-beauty' ); ?></button></p>
		</form>
		<?php
		return ob_get_clean();
	}
);

/**
 * Handle the contact form.
 */
function nexus_handle_contact() {
	$back = isset( $_POST['back'] ) ? esc_url_raw( wp_unslash( $_POST['back'] ) ) : home_url( '/' );
	if ( ! isset( $_POST['nx_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nx_contact_nonce'] ) ), 'nexus_contact' ) ) {
		wp_safe_redirect( add_query_arg( 'nx-sent', 'err', $back ) );
		exit;
	}
	// Bots fill the hidden "website" field.
	if ( ! empty( $_POST['website'] ) ) {
		wp_safe_redirect( add_query_arg( 'nx-sent', 'ok', $back ) );
		exit;
	}
	$name  = isset( $_POST['cname'] ) ? sanitize_text_field( wp_unslash( $_POST['cname'] ) ) : '';
	$reply = isset( $_POST['creply'] ) ? sanitize_text_field( wp_unslash( $_POST['creply'] ) ) : '';
	$topic = isset( $_POST['ctopic'] ) ? sanitize_text_field( wp_unslash( $_POST['ctopic'] ) ) : '';
	$msg   = isset( $_POST['cmsg'] ) ? sanitize_textarea_field( wp_unslash( $_POST['cmsg'] ) ) : '';
	if ( strlen( $name ) < 2 || strlen( $reply ) < 5 || strlen( $msg ) < 10 ) {
		wp_safe_redirect( add_query_arg( 'nx-sent', 'err', $back ) );
		exit;
	}
	$to      = nexus_opt( 'support_email' ) ? nexus_opt( 'support_email' ) : get_option( 'admin_email' );
	$headers = is_email( $reply ) ? array( 'Reply-To: ' . $name . ' <' . $reply . '>' ) : array();
	/* translators: 1: topic, 2: name */
	wp_mail( $to, sprintf( __( '[Contact] %1$s – %2$s', 'nexus-beauty' ), $topic, $name ), "Name: $name\nReply to: $reply\nTopic: $topic\n\n$msg", $headers );
	wp_safe_redirect( add_query_arg( 'nx-sent', 'ok', $back ) . '#nx-contact' );
	exit;
}
add_action( 'admin_post_nexus_contact', 'nexus_handle_contact' );
add_action( 'admin_post_nopriv_nexus_contact', 'nexus_handle_contact' );
