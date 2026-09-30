<?php
/**
 * Header, footer, announcement bar and floating elements.
 * Replaces the GeneratePress header and footer through its hooks, so the parent theme can update safely.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		// Swap the GeneratePress header, navigation and footer for ours.
		remove_action( 'generate_header', 'generate_construct_header' );
		remove_action( 'generate_after_header', 'generate_add_navigation_after_header', 5 );
		remove_action( 'generate_before_header', 'generate_add_navigation_before_header', 5 );
		remove_action( 'generate_before_header', 'generate_top_bar', 5 );
		remove_action( 'generate_footer', 'generate_construct_footer_widgets', 5 );
		remove_action( 'generate_footer', 'generate_construct_footer' );
		remove_action( 'wp_footer', 'generate_back_to_top' );

		add_action( 'generate_before_header', 'nexus_announcement', 6 );
		add_action( 'generate_header', 'nexus_header' );
		add_action( 'generate_footer', 'nexus_footer' );
		add_action( 'wp_footer', 'nexus_overlays', 5 );
	},
	50
);

// GeneratePress menu scripts are not needed with our navigation.
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_dequeue_script( 'generate-menu' );
		wp_dequeue_script( 'generate-navigation-search' );
	},
	100
);

/**
 * Announcement bar with rotating messages.
 */
function nexus_announcement() {
	$lines = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) nexus_opt( 'announcements' ) ) ) );
	if ( ! $lines ) {
		return;
	}
	echo '<div class="nx-announce" role="region" aria-label="' . esc_attr__( 'Store announcements', 'nexus-beauty' ) . '"><div class="nx-announce__track" data-nx-rotate>';
	foreach ( array_values( $lines ) as $i => $line ) {
		printf( '<p%s>%s</p>', $i ? ' hidden' : '', esc_html( $line ) );
	}
	echo '</div></div>';
}

/**
 * Site logo: custom logo if set, otherwise the Nexus mark and site name.
 */
function nexus_logo() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	$name  = get_bloginfo( 'name' );
	$words = preg_split( '/\s+/', trim( $name ), 2 );
	printf(
		'<a class="nx-logo" href="%1$s" rel="home" aria-label="%2$s">%3$s<span class="nx-logo__word"><b>%4$s</b>%5$s</span></a>',
		esc_url( home_url( '/' ) ),
		esc_attr( $name ),
		nexus_mark(), // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG.
		esc_html( $words[0] ),
		isset( $words[1] ) ? '<small>' . esc_html( $words[1] ) . '</small>' : ''
	);
}

/**
 * Wishlist page URL.
 *
 * @return string
 */
function nexus_wishlist_url() {
	$id = (int) nexus_opt( 'wishlist_page' );
	if ( $id ) {
		return get_permalink( $id );
	}
	$page = get_page_by_path( 'wishlist' );
	return $page ? get_permalink( $page ) : '';
}

/**
 * The header.
 */
function nexus_header() {
	$woo       = class_exists( 'WooCommerce' );
	$account   = $woo ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
	$wish_url  = nexus_wishlist_url();
	$count     = ( $woo && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
	?>
	<header class="nx-header" id="nx-header">
		<div class="nx-wrap nx-header__bar">
			<button class="nx-icon-btn nx-menu-btn" type="button" data-nx-open="nx-mnav" aria-controls="nx-mnav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open menu', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'menu' ); // phpcs:ignore ?></button>
			<?php nexus_logo(); ?>
			<nav class="nx-nav" aria-label="<?php esc_attr_e( 'Main', 'nexus-beauty' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'nx-menu',
						'depth'          => 3,
						'fallback_cb'    => 'nexus_menu_fallback',
					)
				);
				?>
			</nav>
			<div class="nx-actions">
				<button class="nx-icon-btn" type="button" data-nx-search aria-controls="nx-search" aria-expanded="false" aria-label="<?php esc_attr_e( 'Search', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'search' ); // phpcs:ignore ?></button>
				<a class="nx-icon-btn nx-hide-sm" href="<?php echo esc_url( $account ); ?>" aria-label="<?php esc_attr_e( 'My account', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'user' ); // phpcs:ignore ?></a>
				<?php if ( $wish_url ) : ?>
					<a class="nx-icon-btn" href="<?php echo esc_url( $wish_url ); ?>" aria-label="<?php esc_attr_e( 'Wishlist', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'heart' ); // phpcs:ignore ?><span class="nx-count" data-nx-wish-count hidden>0</span></a>
				<?php endif; ?>
				<?php if ( $woo ) : ?>
					<a class="nx-icon-btn" href="<?php echo esc_url( wc_get_cart_url() ); ?>" data-nx-open="nx-cart" aria-label="<?php esc_attr_e( 'Open bag', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'bag' ); // phpcs:ignore ?><span class="nx-count nx-cart-count"<?php echo $count ? '' : ' hidden'; ?>><?php echo esc_html( $count ); ?></span></a>
				<?php endif; ?>
			</div>
		</div>
		<div class="nx-search" id="nx-search" hidden>
			<div class="nx-wrap">
				<form class="nx-search__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label class="screen-reader-text" for="nx-q"><?php esc_html_e( 'Search products', 'nexus-beauty' ); ?></label>
					<?php echo nexus_icon( 'search' ); // phpcs:ignore ?>
					<input id="nx-q" type="search" name="s" placeholder="<?php esc_attr_e( 'Search a product, brand or concern', 'nexus-beauty' ); ?>" autocomplete="off" data-nx-live-search>
					<?php if ( $woo ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?>
					<button class="nx-btn nx-btn--primary" type="submit"><?php esc_html_e( 'Search', 'nexus-beauty' ); ?></button>
				</form>
				<div class="nx-search__results" data-nx-results aria-live="polite"></div>
			</div>
		</div>
	</header>
	<?php
}

/**
 * Menu fallback before a menu is assigned: link to key WooCommerce pages.
 */
function nexus_menu_fallback() {
	echo '<ul class="nx-menu">';
	if ( class_exists( 'WooCommerce' ) ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( wc_get_page_permalink( 'shop' ) ), esc_html__( 'Shop', 'nexus-beauty' ) );
	}
	if ( current_user_can( 'edit_theme_options' ) ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( admin_url( 'nav-menus.php' ) ), esc_html__( 'Add a menu', 'nexus-beauty' ) );
	}
	echo '</ul>';
}

/**
 * One footer menu column.
 *
 * @param string $location Menu location.
 * @param string $fallback Title if the menu has no name.
 */
function nexus_footer_menu( $location, $fallback ) {
	if ( ! has_nav_menu( $location ) ) {
		return;
	}
	$title = wp_get_nav_menu_name( $location );
	echo '<div class="nx-footer__col"><h2 class="nx-footer__title">' . esc_html( $title ? $title : $fallback ) . '</h2>';
	wp_nav_menu(
		array(
			'theme_location' => $location,
			'container'      => false,
			'depth'          => 1,
			'menu_class'     => 'nx-footer__menu',
		)
	);
	echo '</div>';
}

/**
 * The footer.
 */
function nexus_footer() {
	$badges = array_filter( array_map( 'trim', explode( ',', (string) nexus_opt( 'payment_badges' ) ) ) );
	?>
	<footer class="nx-footer">
		<div class="nx-wrap">
			<div class="nx-footer__grid">
				<div class="nx-footer__about">
					<?php nexus_logo(); ?>
					<p><?php echo esc_html( nexus_opt( 'footer_about' ) ); ?></p>
					<p>
						<?php esc_html_e( 'WhatsApp:', 'nexus-beauty' ); ?> <a href="<?php echo esc_url( nexus_whatsapp_url() ); ?>" target="_blank" rel="noopener"><b><?php echo esc_html( nexus_whatsapp_display() ); ?></b></a><br>
						<?php esc_html_e( 'Email:', 'nexus-beauty' ); ?> <a href="mailto:<?php echo esc_attr( nexus_opt( 'support_email' ) ); ?>"><b><?php echo esc_html( nexus_opt( 'support_email' ) ); ?></b></a><br>
						<?php echo esc_html( nexus_opt( 'support_hours' ) ); ?>
					</p>
				</div>
				<?php
				nexus_footer_menu( 'nexus-footer-1', __( 'Shop', 'nexus-beauty' ) );
				nexus_footer_menu( 'nexus-footer-2', __( 'Help', 'nexus-beauty' ) );
				nexus_footer_menu( 'nexus-footer-3', __( 'Company', 'nexus-beauty' ) );
				?>
			</div>
			<div class="nx-footer__bottom">
				<span>&copy; <?php echo esc_html( wp_date( 'Y' ) . ' ' . get_bloginfo( 'name' ) ); ?></span>
				<?php if ( $badges ) : ?>
					<div class="nx-pay" aria-label="<?php esc_attr_e( 'Payment methods', 'nexus-beauty' ); ?>">
						<?php foreach ( $badges as $b ) : ?><span><?php echo esc_html( $b ); ?></span><?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</footer>
	<?php
}

/**
 * Side cart, mobile menu, toast, WhatsApp button, back-to-top and cookie notice.
 */
function nexus_overlays() {
	$woo = class_exists( 'WooCommerce' );
	?>
	<div class="nx-scrim" data-nx-close hidden></div>
	<nav class="nx-drawer nx-drawer--left" id="nx-mnav" aria-label="<?php esc_attr_e( 'Mobile menu', 'nexus-beauty' ); ?>" aria-hidden="true" inert>
		<div class="nx-drawer__head"><?php nexus_logo(); ?><button class="nx-icon-btn" type="button" data-nx-close aria-label="<?php esc_attr_e( 'Close menu', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'close' ); // phpcs:ignore ?></button></div>
		<div class="nx-drawer__body">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nx-mmenu',
					'depth'          => 2,
					'fallback_cb'    => 'nexus_menu_fallback',
				)
			);
			if ( $woo ) {
				printf( '<p class="nx-mmenu__extra"><a href="%s">%s</a></p>', esc_url( wc_get_page_permalink( 'myaccount' ) ), esc_html__( 'My account', 'nexus-beauty' ) );
			}
			?>
		</div>
	</nav>
	<?php if ( $woo ) : ?>
		<aside class="nx-drawer nx-drawer--right" id="nx-cart" aria-label="<?php esc_attr_e( 'Shopping bag', 'nexus-beauty' ); ?>" aria-hidden="true" inert>
			<div class="nx-drawer__head"><h2><?php esc_html_e( 'Your bag', 'nexus-beauty' ); ?></h2><button class="nx-icon-btn" type="button" data-nx-close aria-label="<?php esc_attr_e( 'Close bag', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'close' ); // phpcs:ignore ?></button></div>
			<?php
			if ( function_exists( 'nexus_drawer_body' ) ) {
				nexus_drawer_body();
			}
			?>
		</aside>
	<?php endif; ?>
	<div class="nx-toast" role="status" aria-live="polite"></div>
	<?php if ( nexus_opt( 'whatsapp' ) ) : ?>
		<a class="nx-wa" href="<?php echo esc_url( nexus_whatsapp_url() ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat with us on WhatsApp', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'whatsapp' ); // phpcs:ignore ?></a>
	<?php endif; ?>
	<button class="nx-top" type="button" data-nx-top hidden aria-label="<?php esc_attr_e( 'Back to top', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'up' ); // phpcs:ignore ?></button>
	<?php if ( nexus_opt( 'cookie_notice' ) ) : ?>
		<div class="nx-cookie" data-nx-cookie hidden role="region" aria-label="<?php esc_attr_e( 'Cookie notice', 'nexus-beauty' ); ?>">
			<p><?php echo esc_html( nexus_opt( 'cookie_text' ) ); ?> <?php if ( get_privacy_policy_url() ) : ?><a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Privacy policy', 'nexus-beauty' ); ?></a><?php endif; ?></p>
			<div><button class="nx-btn nx-btn--ghost-light" type="button" data-nx-cookie-choice="essential"><?php esc_html_e( 'Essential only', 'nexus-beauty' ); ?></button><button class="nx-btn nx-btn--primary" type="button" data-nx-cookie-choice="all"><?php esc_html_e( 'Accept all', 'nexus-beauty' ); ?></button></div>
		</div>
	<?php endif; ?>
	<?php
}
