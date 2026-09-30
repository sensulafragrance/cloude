<?php
/**
 * Header, navigation, search panel, footer and overlays, in the Sensula design.
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
		remove_action( 'generate_after_header', 'generate_featured_page_header', 10 );
		remove_action( 'generate_footer', 'generate_construct_footer_widgets', 5 );
		remove_action( 'generate_footer', 'generate_construct_footer' );
		remove_action( 'wp_footer', 'generate_back_to_top' );

		add_action( 'wp_body_open', 'nexus_sprite', 1 );
		add_action( 'generate_before_header', 'nexus_announcement', 6 );
		add_action( 'generate_header', 'nexus_header' );
		add_action( 'generate_after_header', 'nexus_panels', 1 );
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
		wp_dequeue_script( 'generate-back-to-top' );
	},
	100
);

// Fallback so the sprite is printed even on themes/plugins that skip wp_body_open.
add_action(
	'wp_footer',
	function () {
		nexus_sprite();
	},
	1
);

/**
 * Announcement bar with rotating messages (Customize > Nexus Beauty > Store & contact).
 */
function nexus_announcement() {
	$lines = nexus_opt_lines( 'announcements' );
	if ( ! $lines ) {
		return;
	}
	echo '<div class="announce" role="region" aria-label="' . esc_attr__( 'Store announcements', 'nexus-beauty' ) . '" data-nx-rotate>';
	foreach ( $lines as $i => $line ) {
		printf( '<p%s>%s</p>', $i ? ' hidden' : '', esc_html( $line ) );
	}
	echo '</div>';
}

/**
 * Menu items for a location as <li> markup, or '' if no menu is assigned.
 *
 * @param string $location Menu location.
 * @return string
 */
function nexus_menu_items( $location ) {
	if ( ! has_nav_menu( $location ) ) {
		return '';
	}
	return (string) wp_nav_menu(
		array(
			'theme_location' => $location,
			'container'      => false,
			'items_wrap'     => '%3$s',
			'depth'          => 1,
			'echo'           => false,
			'fallback_cb'    => false,
		)
	);
}

/**
 * Default main menu when none is set: top-level product categories, Brands and Offers.
 *
 * @return string
 */
function nexus_default_menu_items() {
	$out = '';
	foreach ( nexus_sorted_cats( 9 ) as $c ) {
		$out .= '<li><a href="' . esc_url( get_term_link( $c ) ) . '">' . esc_html( $c->name ) . '</a></li>';
	}
	$brands = nexus_page_url( 'brands' );
	if ( $brands ) {
		$out .= '<li><a href="' . esc_url( $brands ) . '">' . esc_html__( 'Brands', 'nexus-beauty' ) . '</a></li>';
	}
	$offers = nexus_page_url( 'offers' );
	if ( $offers ) {
		$out .= '<li class="is-sale"><a href="' . esc_url( $offers ) . '">' . esc_html__( 'Offers', 'nexus-beauty' ) . '</a></li>';
	}
	if ( ! $out && class_exists( 'WooCommerce' ) ) {
		$out = '<li><a href="' . esc_url( nexus_shop_url() ) . '">' . esc_html__( 'Shop', 'nexus-beauty' ) . '</a></li>';
	}
	return $out;
}

/**
 * The header.
 */
function nexus_header() {
	$woo      = class_exists( 'WooCommerce' );
	$account  = $woo ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
	$wish_url = nexus_wishlist_url();
	$count    = ( $woo && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
	$items    = nexus_menu_items( 'primary' );
	$items    = $items ? $items : nexus_default_menu_items();
	?>
	<header class="header" id="nx-header">
		<div class="wrap header__bar header__bar--slim">
			<div style="display:flex;align-items:center;gap:4px">
				<button class="icon-btn menu-btn" type="button" data-open-menu aria-label="<?php esc_attr_e( 'Open menu', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'menu' ); // phpcs:ignore ?></button>
				<?php nexus_logo(); ?>
			</div>
			<div class="actions">
				<button class="icon-btn" type="button" data-open-search aria-label="<?php esc_attr_e( 'Search', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'search' ); // phpcs:ignore ?></button>
				<a class="icon-btn hide-sm" href="<?php echo esc_url( $account ); ?>" aria-label="<?php esc_attr_e( 'Your account', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'user' ); // phpcs:ignore ?></a>
				<?php if ( $wish_url ) : ?>
					<a class="icon-btn hide-sm" href="<?php echo esc_url( $wish_url ); ?>" aria-label="<?php esc_attr_e( 'Wishlist', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'heart' ); // phpcs:ignore ?><span class="badge-count" data-nx-wish-count hidden>0</span></a>
				<?php endif; ?>
				<?php if ( $woo ) : ?>
					<a class="icon-btn" href="<?php echo esc_url( wc_get_cart_url() ); ?>" data-open-cart aria-label="<?php esc_attr_e( 'Open bag', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'bag' ); // phpcs:ignore ?><?php echo nexus_count_badge( $count ); // phpcs:ignore ?></a>
				<?php endif; ?>
			</div>
		</div>
		<?php if ( $items ) : ?>
			<nav class="nav" aria-label="<?php esc_attr_e( 'Main', 'nexus-beauty' ); ?>"><div class="wrap"><ul><?php echo $items; // phpcs:ignore WordPress.Security.EscapeOutput -- menu markup from WordPress. ?></ul></div></nav>
		<?php endif; ?>
	</header>
	<?php
}

/**
 * Bag count badge (also refreshed as a cart fragment).
 *
 * @param int $count Items in the bag.
 * @return string
 */
function nexus_count_badge( $count ) {
	return '<span class="badge-count" data-cart-count' . ( $count ? '' : ' hidden' ) . '>' . (int) $count . '</span>';
}

/**
 * Mobile menu and search panel (they slide in over the page).
 */
function nexus_panels() {
	$items = nexus_menu_items( 'primary' );
	$items = $items ? $items : nexus_default_menu_items();
	$woo   = class_exists( 'WooCommerce' );
	$track = (int) nexus_opt( 'track_page' );
	?>
	<nav class="mnav" id="nx-mnav" aria-label="<?php esc_attr_e( 'Mobile', 'nexus-beauty' ); ?>" aria-hidden="true">
		<div style="display:flex;justify-content:space-between;align-items:center"><?php nexus_logo( 'span' ); ?><button class="icon-btn" type="button" data-close aria-label="<?php esc_attr_e( 'Close menu', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'close' ); // phpcs:ignore ?></button></div>
		<ul>
			<?php echo $items; // phpcs:ignore WordPress.Security.EscapeOutput -- menu markup from WordPress. ?>
			<?php if ( $woo ) : ?>
				<li><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'My account', 'nexus-beauty' ); ?></a></li>
			<?php endif; ?>
			<?php if ( $track && get_post_status( $track ) ) : ?>
				<li><a href="<?php echo esc_url( get_permalink( $track ) ); ?>"><?php esc_html_e( 'Track my order', 'nexus-beauty' ); ?></a></li>
			<?php endif; ?>
		</ul>
	</nav>
	<div class="searchpanel" id="nx-search" aria-hidden="true" role="dialog" aria-label="<?php esc_attr_e( 'Search', 'nexus-beauty' ); ?>">
		<div class="wrap">
			<?php /* translators: %s: store name */ ?>
			<div style="display:flex;justify-content:space-between;align-items:center"><p class="eyebrow"><?php echo esc_html( sprintf( __( 'Search %s', 'nexus-beauty' ), get_bloginfo( 'name' ) ) ); ?></p><button class="icon-btn" type="button" data-close aria-label="<?php esc_attr_e( 'Close search', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'close' ); // phpcs:ignore ?></button></div>
			<?php nexus_search_form( 'q-panel', true ); ?>
			<div class="search-results" data-nx-results aria-live="polite"></div>
			<?php nexus_popular_searches(); ?>
		</div>
	</div>
	<?php
}

/**
 * The design's search form.
 *
 * @param string $id   Input ID.
 * @param bool   $live Enable live results.
 */
function nexus_search_form( $id, $live = false ) {
	?>
	<form class="finder-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<div class="field"><label class="sr-only" for="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Search products, brands and concerns', 'nexus-beauty' ); ?></label><?php echo nexus_icon( 'search' ); // phpcs:ignore ?><input id="<?php echo esc_attr( $id ); ?>" name="s" type="search" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search a product, brand or concern, e.g. hair fall', 'nexus-beauty' ); ?>" autocomplete="off"<?php echo $live ? ' data-nx-live-search' : ''; ?>></div>
		<?php if ( class_exists( 'WooCommerce' ) ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?>
		<button class="btn btn--primary btn--lg" type="submit"><?php esc_html_e( 'Search', 'nexus-beauty' ); ?></button>
	</form>
	<?php
}

/**
 * Popular search chips (Customize > Nexus Beauty > Store & contact).
 */
function nexus_popular_searches() {
	$raw   = (string) nexus_opt( 'popular' );
	$terms = array_filter( array_map( 'trim', explode( false !== strpos( $raw, '|' ) ? '|' : ',', $raw ) ) );
	if ( ! $terms ) {
		return;
	}
	echo '<div class="popular"><span>' . esc_html__( 'Popular:', 'nexus-beauty' ) . '</span>';
	foreach ( $terms as $t ) {
		echo '<a href="' . esc_url( nexus_search_url( $t ) ) . '">' . esc_html( $t ) . '</a>';
	}
	echo '</div>';
}

/**
 * One footer column: the menu at a location, or sensible default links.
 *
 * @param string $location Menu location.
 * @param string $title    Default title.
 * @param array  $links    Default links [ [label, url], ... ].
 */
function nexus_footer_col( $location, $title, $links ) {
	$items = nexus_menu_items( $location );
	if ( $items ) {
		$name  = wp_get_nav_menu_name( $location );
		$title = $name ? $name : $title;
	} else {
		foreach ( $links as $l ) {
			if ( $l[1] ) {
				$items .= '<li><a href="' . esc_url( $l[1] ) . '">' . esc_html( $l[0] ) . '</a></li>';
			}
		}
	}
	if ( ! $items ) {
		return;
	}
	echo '<div><h3>' . esc_html( $title ) . '</h3><ul>' . $items . '</ul></div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above or menu markup.
}

/**
 * The footer.
 */
function nexus_footer() {
	$badges = array_filter( array_map( 'trim', explode( ',', (string) nexus_opt( 'payment_badges' ) ) ) );
	$store  = get_bloginfo( 'name' );

	$shop = array();
	foreach ( nexus_sorted_cats( 6 ) as $c ) {
		$shop[] = array( $c->name, get_term_link( $c ) );
	}
	$brands = array();
	if ( taxonomy_exists( 'product_brand' ) ) {
		$bs = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => true, 'number' => 4, 'orderby' => 'count', 'order' => 'DESC' ) );
		foreach ( is_wp_error( $bs ) ? array() : $bs as $b ) {
			$brands[] = array( $b->name, get_term_link( $b ) );
		}
	}
	$brands[] = array( __( 'All brands A–Z', 'nexus-beauty' ), nexus_page_url( 'brands' ) );
	$track    = (int) nexus_opt( 'track_page' );
	$help     = array(
		array( __( 'Track my order', 'nexus-beauty' ), $track ? get_permalink( $track ) : nexus_page_url( 'track-order' ) ),
		array( __( 'Delivery across Pakistan', 'nexus-beauty' ), nexus_page_url( 'delivery' ) ),
		array( __( 'Returns & exchanges', 'nexus-beauty' ), nexus_page_url( 'returns' ) ),
		array( __( 'How we check authenticity', 'nexus-beauty' ), nexus_page_url( 'authenticity' ) ),
		array( __( 'Contact us', 'nexus-beauty' ), nexus_page_url( 'contact' ) ),
	);
	$company = array(
		/* translators: %s: store name */
		array( sprintf( __( 'About %s', 'nexus-beauty' ), $store ), nexus_page_url( 'about-us' ) ),
		array( __( 'Beauty journal', 'nexus-beauty' ), nexus_page_url( 'journal' ) ),
		array( __( 'FAQs', 'nexus-beauty' ), nexus_page_url( 'faq' ) ),
		array( __( 'Terms of service', 'nexus-beauty' ), nexus_page_url( 'terms' ) ),
		array( __( 'Privacy policy', 'nexus-beauty' ), get_privacy_policy_url() ),
	);
	?>
	<footer class="footer">
		<div class="wrap">
			<div class="footer__grid">
				<div class="footer__about"><?php nexus_logo( 'span' ); ?>
					<p><?php echo esc_html( nexus_text( 'footer_about' ) ); ?></p>
					<p>
						<?php if ( nexus_opt( 'phone' ) ) : ?><?php esc_html_e( 'Customer care:', 'nexus-beauty' ); ?> <b><?php echo esc_html( nexus_opt( 'phone' ) ); ?></b><br><?php endif; ?>
						<?php if ( nexus_opt( 'whatsapp' ) ) : ?><?php esc_html_e( 'WhatsApp:', 'nexus-beauty' ); ?> <a href="<?php echo esc_url( nexus_whatsapp_url() ); ?>" target="_blank" rel="noopener"><b><?php echo esc_html( nexus_whatsapp_display() ); ?></b></a><br><?php endif; ?>
						<?php echo esc_html( nexus_opt( 'support_hours' ) ); ?>
					</p>
				</div>
				<?php
				nexus_footer_col( 'nexus-footer-1', __( 'Shop', 'nexus-beauty' ), $shop );
				nexus_footer_col( 'nexus-footer-4', __( 'Brands', 'nexus-beauty' ), $brands );
				nexus_footer_col( 'nexus-footer-2', __( 'Help', 'nexus-beauty' ), $help );
				nexus_footer_col( 'nexus-footer-3', __( 'Company', 'nexus-beauty' ), $company );
				?>
			</div>
			<div class="footer__bottom">
				<?php /* translators: 1: year, 2: store name */ ?>
				<span><?php echo esc_html( sprintf( __( '© %1$s %2$s. All rights reserved.', 'nexus-beauty' ), wp_date( 'Y' ), $store ) ); ?></span>
				<?php if ( $badges ) : ?>
					<div class="pay" aria-label="<?php esc_attr_e( 'Payment methods', 'nexus-beauty' ); ?>"><?php foreach ( $badges as $b ) : ?><span><?php echo esc_html( $b ); ?></span><?php endforeach; ?></div>
				<?php endif; ?>
			</div>
		</div>
	</footer>
	<?php
}

/**
 * Side bag, toast, WhatsApp button and cookie notice.
 */
function nexus_overlays() {
	?>
	<div class="scrim" data-close></div>
	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<aside class="drawer" id="nx-cart" aria-label="<?php esc_attr_e( 'Shopping bag', 'nexus-beauty' ); ?>" aria-hidden="true">
			<div class="drawer__head"><h2><?php esc_html_e( 'Your bag', 'nexus-beauty' ); ?></h2><button class="icon-btn" type="button" data-close aria-label="<?php esc_attr_e( 'Close bag', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'close' ); // phpcs:ignore ?></button></div>
			<?php
			if ( function_exists( 'nexus_drawer_body' ) ) {
				nexus_drawer_body();
			}
			?>
		</aside>
	<?php endif; ?>
	<div class="toast" role="status" aria-live="polite"></div>
	<?php if ( nexus_opt( 'whatsapp' ) && nexus_opt( 'whatsapp_button' ) ) : ?>
		<a class="wa-float" href="<?php echo esc_url( nexus_whatsapp_url() ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat with us on WhatsApp', 'nexus-beauty' ); ?>"><?php echo nexus_icon( 'whatsapp' ); // phpcs:ignore ?></a>
	<?php endif; ?>
	<?php if ( nexus_opt( 'cookie_notice' ) ) : ?>
		<div class="cookie" data-nx-cookie hidden role="region" aria-label="<?php esc_attr_e( 'Cookie notice', 'nexus-beauty' ); ?>">
			<p><?php echo esc_html( nexus_opt( 'cookie_text' ) ); ?> <?php if ( get_privacy_policy_url() ) : ?><a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Privacy policy', 'nexus-beauty' ); ?></a><?php endif; ?></p>
			<div><button class="btn btn--ghost" type="button" data-nx-cookie-choice="essential"><?php esc_html_e( 'Essential only', 'nexus-beauty' ); ?></button><button class="btn btn--primary" type="button" data-nx-cookie-choice="all"><?php esc_html_e( 'Accept all', 'nexus-beauty' ); ?></button></div>
		</div>
	<?php endif; ?>
	<?php
}
