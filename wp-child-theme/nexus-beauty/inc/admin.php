<?php
/**
 * Admin: Appearance > Nexus setup (checklist and one-click fixes) and a checkout-block notice.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'admin_menu',
	function () {
		add_theme_page( __( 'Nexus setup', 'nexus-beauty' ), __( 'Nexus setup', 'nexus-beauty' ), 'edit_theme_options', 'nexus-setup', 'nexus_setup_page' );
	}
);

/**
 * Does a WooCommerce page use the block version?
 *
 * @param string $page cart|checkout.
 * @return bool
 */
function nexus_uses_block( $page ) {
	if ( ! function_exists( 'wc_get_page_id' ) ) {
		return false;
	}
	$id = wc_get_page_id( $page );
	return $id > 0 && has_block( 'woocommerce/' . $page, get_post( $id ) );
}

/**
 * The setup page.
 */
function nexus_setup_page() {
	$woo   = class_exists( 'WooCommerce' );
	$items = array(
		array( __( 'Parent theme GeneratePress installed', 'nexus-beauty' ), 'generatepress' === get_template(), __( 'Install GeneratePress from Appearance > Themes > Add New.', 'nexus-beauty' ), '' ),
		array( __( 'WooCommerce active', 'nexus-beauty' ), $woo, __( 'Install and activate WooCommerce.', 'nexus-beauty' ), '' ),
		array( __( 'Main menu assigned', 'nexus-beauty' ), has_nav_menu( 'primary' ), __( 'Appearance > Menus: assign a menu to "Main menu".', 'nexus-beauty' ), '' ),
		array( __( 'Footer menus assigned', 'nexus-beauty' ), has_nav_menu( 'nexus-footer-1' ), __( 'Appearance > Menus: assign menus to the three footer locations.', 'nexus-beauty' ), '' ),
		array( __( 'Wishlist page', 'nexus-beauty' ), (bool) nexus_opt( 'wishlist_page' ), __( 'Create a page with [nexus_wishlist].', 'nexus-beauty' ), 'wishlist' ),
		array( __( 'Order tracking page', 'nexus-beauty' ), (bool) nexus_opt( 'track_page' ), __( 'Create a page with [woocommerce_order_tracking].', 'nexus-beauty' ), 'track' ),
	);
	if ( $woo ) {
		$items[] = array( __( 'Cart and checkout use the classic version (needed for the Pakistan checkout features)', 'nexus-beauty' ), ! nexus_uses_block( 'cart' ) && ! nexus_uses_block( 'checkout' ), __( 'Your cart or checkout page uses the WooCommerce block.', 'nexus-beauty' ), 'classic' );
		$items[] = array( __( 'Free shipping method matches the free delivery bar', 'nexus-beauty' ), null, sprintf( /* translators: %s: amount */ __( 'In WooCommerce > Settings > Shipping, add "Free shipping" with a minimum order of %s.', 'nexus-beauty' ), nexus_price_text( nexus_opt( 'free_shipping' ) ) ), '' );
	}
	$labels = array(
		'wishlist' => __( 'Create wishlist page', 'nexus-beauty' ),
		'track'    => __( 'Create tracking page', 'nexus-beauty' ),
		'classic'  => __( 'Switch cart and checkout to classic', 'nexus-beauty' ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Nexus Beauty setup', 'nexus-beauty' ); ?></h1>
		<?php if ( isset( $_GET['nexus-done'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success"><p><?php esc_html_e( 'Done.', 'nexus-beauty' ); ?></p></div>
		<?php endif; ?>
		<table class="widefat striped" style="max-width:900px">
			<tbody>
			<?php foreach ( $items as $it ) : ?>
				<tr>
					<td style="width:28px"><?php echo null === $it[1] ? '<span style="color:#2271b1;font-weight:700">i</span>' : ( $it[1] ? '<span style="color:#1f8f4e;font-weight:700">✓</span>' : '<span style="color:#b3261e;font-weight:700">!</span>' ); ?></td>
					<td><strong><?php echo esc_html( $it[0] ); ?></strong><?php echo true === $it[1] ? '' : '<br>' . esc_html( $it[2] ); ?></td>
					<td style="width:260px">
						<?php if ( false === $it[1] && $it[3] ) : ?>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
								<input type="hidden" name="action" value="nexus_setup">
								<input type="hidden" name="task" value="<?php echo esc_attr( $it[3] ); ?>">
								<?php wp_nonce_field( 'nexus_setup' ); ?>
								<button class="button button-primary"><?php echo esc_html( $labels[ $it[3] ] ); ?></button>
							</form>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<h2><?php esc_html_e( 'Automatic setup', 'nexus-beauty' ); ?></h2>
		<p><?php esc_html_e( 'Setup ran when you activated the theme. Running it again only adds what is missing: it never changes or deletes your existing pages, menus or products.', 'nexus-beauty' ); ?></p>
		<p style="display:flex;gap:8px;flex-wrap:wrap">
			<?php
			$nexus_buttons = array( 'rerun' => __( 'Run setup again', 'nexus-beauty' ) );
			if ( class_exists( 'WooCommerce' ) ) {
				$nexus_buttons['import'] = __( 'Import starter products', 'nexus-beauty' );
			}
			foreach ( $nexus_buttons as $nexus_task => $nexus_label ) :
				?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="nexus_setup">
					<input type="hidden" name="task" value="<?php echo esc_attr( $nexus_task ); ?>">
					<?php wp_nonce_field( 'nexus_setup' ); ?>
					<button class="button"><?php echo esc_html( $nexus_label ); ?></button>
				</form>
			<?php endforeach; ?>
		</p>
		<?php
		$nexus_log = get_option( 'nexus_setup_log', array() );
		if ( $nexus_log ) :
			?>
			<details><summary><?php esc_html_e( 'Last setup log', 'nexus-beauty' ); ?></summary>
				<ul style="list-style:disc;padding-left:20px"><?php foreach ( (array) ( isset( $nexus_log['items'] ) ? $nexus_log['items'] : array() ) as $nexus_line ) : ?><li><?php echo esc_html( $nexus_line ); ?></li><?php endforeach; ?></ul>
			</details>
		<?php endif; ?>
		<h2><?php esc_html_e( 'Building pages', 'nexus-beauty' ); ?></h2>
		<p><?php esc_html_e( 'In the block editor, click + > Patterns > Nexus Beauty. "Full home page" builds the whole home page. Set the page template to "Nexus full width" for edge-to-edge sections.', 'nexus-beauty' ); ?></p>
		<h2><?php esc_html_e( 'Shortcodes', 'nexus-beauty' ); ?></h2>
		<ul style="list-style:disc;padding-left:20px">
			<li><code>[nexus_product_tabs tabs="best,new,sale" limit="8" columns="4"]</code></li>
			<li><code>[nexus_categories limit="12"]</code> · <code>[nexus_brands limit="24"]</code> · <code>[nexus_trust]</code></li>
			<li><code>[nexus_kit ids="12,34,56" title="Hair Fall Kit" roles="Shampoo|Mask|Serum" how="3 times a week|Once a week|Daily" tips="Tip one|Tip two" avoid="Avoid one"]</code></li>
			<li><code>[nexus_videos urls="https://youtu.be/…,https://youtu.be/…" titles="Title one|Title two"]</code> · <code>[nexus_whatsapp]</code></li>
			<li><code>[nexus_wishlist]</code> · <code>[nexus_recently_viewed limit="8"]</code></li>
		</ul>
		<p><?php esc_html_e( 'Product tags with the slugs bestseller, mini, derm, our-label, korea and pakistani show as badges on product cards.', 'nexus-beauty' ); ?></p>
	</div>
	<?php
}

add_action(
	'admin_post_nexus_setup',
	function () {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'nexus-beauty' ) );
		}
		check_admin_referer( 'nexus_setup' );
		$task = isset( $_POST['task'] ) ? sanitize_key( wp_unslash( $_POST['task'] ) ) : '';
		if ( 'wishlist' === $task ) {
			$id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => __( 'Wishlist', 'nexus-beauty' ), 'post_name' => 'wishlist', 'post_content' => '<!-- wp:shortcode -->[nexus_wishlist]<!-- /wp:shortcode -->' ) );
			if ( $id && ! is_wp_error( $id ) ) {
				set_theme_mod( 'nexus_wishlist_page', $id );
			}
		} elseif ( 'track' === $task ) {
			$id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => __( 'Track your order', 'nexus-beauty' ), 'post_name' => 'track-order', 'post_content' => '<!-- wp:shortcode -->[woocommerce_order_tracking]<!-- /wp:shortcode -->' ) );
			if ( $id && ! is_wp_error( $id ) ) {
				set_theme_mod( 'nexus_track_page', $id );
			}
		} elseif ( 'rerun' === $task && function_exists( 'nexus_run_setup' ) ) {
			nexus_run_setup();
			wp_safe_redirect( admin_url( 'themes.php?page=nexus-setup&nexus-done=1' ) );
			exit;
		} elseif ( 'import' === $task && current_user_can( 'manage_woocommerce' ) && function_exists( 'nexus_run_setup' ) ) {
			nexus_run_setup( true );
			wp_safe_redirect( admin_url( 'themes.php?page=nexus-setup&nexus-done=1' ) );
			exit;
		} elseif ( 'classic' === $task && current_user_can( 'manage_woocommerce' ) && function_exists( 'wc_get_page_id' ) ) {
			foreach ( array( 'cart' => '[woocommerce_cart]', 'checkout' => '[woocommerce_checkout]' ) as $page => $code ) {
				$id = wc_get_page_id( $page );
				if ( $id > 0 ) {
					wp_update_post( array( 'ID' => $id, 'post_content' => '<!-- wp:shortcode -->' . $code . '<!-- /wp:shortcode -->' ) );
				}
			}
		}
		wp_safe_redirect( admin_url( 'themes.php?page=nexus-setup&nexus-done=1' ) );
		exit;
	}
);

// Remind admins once if the checkout block is in use.
add_action(
	'admin_notices',
	function () {
		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'themes', 'woocommerce_page_wc-settings' ), true ) || ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		if ( nexus_uses_block( 'checkout' ) || nexus_uses_block( 'cart' ) ) {
			printf(
				'<div class="notice notice-warning"><p>%s <a href="%s">%s</a></p></div>',
				esc_html__( 'Nexus Beauty: your cart or checkout uses the WooCommerce block, so the Pakistan phone check, city suggestions and delivery estimate won\'t show.', 'nexus-beauty' ),
				esc_url( admin_url( 'themes.php?page=nexus-setup' ) ),
				esc_html__( 'Switch to classic in one click', 'nexus-beauty' )
			);
		}
	}
);
