<?php
/**
 * Automatic store setup.
 *
 * Runs once after the theme is activated (and again for the WooCommerce parts as soon as WooCommerce is active):
 * creates the pages, sets the home page, builds the menus, imports the starter products when the store is empty,
 * switches cart and checkout to the classic version, adds a Pakistan shipping zone and enables cash on delivery.
 *
 * Safety rules:
 * - Existing pages are never overwritten or deleted; only missing pages are created.
 * - Starter products are imported only when the store has no products (or when you click the import button).
 * - Settings that change (home page, cart/checkout content) are remembered and restored if you switch themes.
 *
 * @package NexusBeauty
 */

defined( 'ABSPATH' ) || exit;

const NEXUS_SETUP_VERSION = '2';

// Flag the setup to run on the next admin page load after activation.
add_action(
	'after_switch_theme',
	function () {
		update_option( 'nexus_setup_pending', 1, false );
	}
);

add_action(
	'admin_init',
	function () {
		if ( ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) {
			return;
		}
		if ( get_option( 'nexus_setup_pending' ) || NEXUS_SETUP_VERSION !== get_option( 'nexus_setup_version' ) ) {
			// First activation, or the theme was updated to a version with new setup steps.
			delete_option( 'nexus_setup_pending' );
			nexus_run_setup();
		} elseif ( class_exists( 'WooCommerce' ) && get_option( 'nexus_setup_woo_pending' ) ) {
			// WooCommerce was activated after the theme: finish the store part now.
			nexus_run_setup();
		}
	},
	20
);

/**
 * Run the whole setup. Safe to run more than once.
 *
 * @param bool $import_products Force the starter product import even if the store has products.
 * @return array Log of what was done.
 */
function nexus_run_setup( $import_products = false ) {
	$log   = array();
	$woo   = class_exists( 'WooCommerce' );
	$pages = nexus_setup_pages( $log );

	if ( $woo ) {
		nexus_setup_store( $log, $import_products );
		delete_option( 'nexus_setup_woo_pending' );
	} else {
		update_option( 'nexus_setup_woo_pending', 1, false );
		$log[] = __( 'WooCommerce is not active yet. Products, shipping and checkout will be set up automatically as soon as you activate it.', 'nexus-beauty' );
	}

	nexus_setup_reading( $pages, $log );
	nexus_setup_menus( $pages, $log );

	if ( ! empty( $pages['wishlist'] ) ) {
		set_theme_mod( 'nexus_wishlist_page', $pages['wishlist'] );
	}
	if ( ! empty( $pages['track-order'] ) ) {
		set_theme_mod( 'nexus_track_page', $pages['track-order'] );
	}
	flush_rewrite_rules( false );

	update_option( 'nexus_setup_log', array( 'time' => time(), 'items' => $log ), false );
	update_option( 'nexus_setup_version', NEXUS_SETUP_VERSION, false );
	update_option( 'nexus_setup_notice', 1, false );
	return $log;
}

/* ------------------------------------------------------------------------ */
/* Pages                                                                     */
/* ------------------------------------------------------------------------ */

/**
 * The pages the theme creates: slug => [ title, content callback, full-width template ].
 *
 * @return array
 */
function nexus_page_definitions() {
	return array(
		'home'           => array( __( 'Home', 'nexus-beauty' ), 'nexus_content_home', true ),
		'routines'       => array( __( 'Routines', 'nexus-beauty' ), 'nexus_content_routines', true ),
		'collections'    => array( __( 'All collections', 'nexus-beauty' ), 'nexus_content_collections', false ),
		'brands'         => array( __( 'Brands A–Z', 'nexus-beauty' ), 'nexus_content_brands', false ),
		'offers'         => array( __( 'Offers', 'nexus-beauty' ), 'nexus_content_offers', false ),
		'about-us'       => array( __( 'About us', 'nexus-beauty' ), 'nexus_content_about', false ),
		'contact'        => array( __( 'Contact us', 'nexus-beauty' ), 'nexus_content_contact', false ),
		'authenticity'   => array( __( 'How we check authenticity', 'nexus-beauty' ), 'nexus_content_authenticity', false ),
		'delivery'       => array( __( 'Delivery information', 'nexus-beauty' ), 'nexus_content_delivery', false ),
		'returns'        => array( __( 'Returns & refunds', 'nexus-beauty' ), 'nexus_content_returns', false ),
		'faq'            => array( __( 'FAQs', 'nexus-beauty' ), 'nexus_content_faq', false ),
		'terms'          => array( __( 'Terms of service', 'nexus-beauty' ), 'nexus_content_terms', false ),
		'privacy'        => array( __( 'Privacy policy', 'nexus-beauty' ), 'nexus_content_privacy', false ),
		'wishlist'       => array( __( 'Wishlist', 'nexus-beauty' ), 'nexus_content_wishlist', false ),
		'track-order'    => array( __( 'Track your order', 'nexus-beauty' ), 'nexus_content_track', false ),
		'journal'        => array( __( 'Journal', 'nexus-beauty' ), '__return_empty_string', false ),
	);
}

/**
 * Create missing pages. Returns [ slug => page ID ] for every page (new or existing).
 *
 * @param array $log Log (by reference).
 * @return array
 */
function nexus_setup_pages( &$log ) {
	$ids     = array();
	$created = array();
	foreach ( nexus_page_definitions() as $slug => $def ) {
		$existing = get_page_by_path( $slug );
		if ( $existing && 'trash' !== $existing->post_status ) {
			$ids[ $slug ] = $existing->ID;
			continue;
		}
		$id = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $def[0],
				'post_name'    => $slug,
				'post_content' => call_user_func( $def[1] ),
				'meta_input'   => array( '_nexus_created' => 1 ),
			),
			true
		);
		if ( ! is_wp_error( $id ) ) {
			$ids[ $slug ] = $id;
			$created[]    = $def[0];
			if ( $def[2] ) {
				update_post_meta( $id, '_wp_page_template', 'page-templates/template-full-width.php' );
			}
		}
	}
	if ( $created ) {
		/* translators: %s: list of page titles */
		$log[] = sprintf( __( 'Created pages: %s.', 'nexus-beauty' ), implode( ', ', $created ) );
	} else {
		$log[] = __( 'All pages already existed, so none were changed.', 'nexus-beauty' );
	}
	return $ids;
}

/**
 * Home page, blog page, privacy page and WooCommerce terms page.
 *
 * @param array $pages Page IDs.
 * @param array $log   Log.
 */
function nexus_setup_reading( $pages, &$log ) {
	if ( empty( get_option( 'nexus_prev_reading' ) ) ) {
		update_option(
			'nexus_prev_reading',
			array(
				'show_on_front'  => get_option( 'show_on_front' ),
				'page_on_front'  => get_option( 'page_on_front' ),
				'page_for_posts' => get_option( 'page_for_posts' ),
			),
			false
		);
	}
	if ( ! empty( $pages['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $pages['home'] );
		$log[] = __( 'Set the home page (the store design) as your front page.', 'nexus-beauty' );
	}
	if ( ! empty( $pages['journal'] ) && ! get_option( 'page_for_posts' ) ) {
		update_option( 'page_for_posts', $pages['journal'] );
	}
	$privacy = (int) get_option( 'wp_page_for_privacy_policy' );
	if ( ! empty( $pages['privacy'] ) && ( ! $privacy || 'publish' !== get_post_status( $privacy ) ) ) {
		update_option( 'wp_page_for_privacy_policy', $pages['privacy'] );
	}
	if ( class_exists( 'WooCommerce' ) && ! empty( $pages['terms'] ) && ! get_option( 'woocommerce_terms_page_id' ) ) {
		update_option( 'woocommerce_terms_page_id', $pages['terms'] );
	}
}

/**
 * Create and assign menus for any menu location that has none.
 *
 * @param array $pages Page IDs.
 * @param array $log   Log.
 */
function nexus_setup_menus( $pages, &$log ) {
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$made      = array();

	$page_item = function ( $menu, $slug, $parent = 0, $classes = '', $title = '' ) use ( $pages ) {
		if ( empty( $pages[ $slug ] ) ) {
			return 0;
		}
		return wp_update_nav_menu_item(
			$menu,
			0,
			array(
				'menu-item-title'     => $title,
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $pages[ $slug ],
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
				'menu-item-parent-id' => $parent,
				'menu-item-classes'   => $classes,
			)
		);
	};
	$cat_items = function ( $menu, $parent, $limit ) {
		foreach ( nexus_sorted_cats( $limit ) as $c ) {
			wp_update_nav_menu_item(
				$menu,
				0,
				array(
					'menu-item-object'    => 'product_cat',
					'menu-item-object-id' => $c->term_id,
					'menu-item-type'      => 'taxonomy',
					'menu-item-status'    => 'publish',
					'menu-item-parent-id' => $parent,
				)
			);
		}
	};
	$new_menu = function ( $name ) {
		$exists = wp_get_nav_menu_object( $name );
		if ( $exists ) {
			wp_delete_nav_menu( $exists->term_id );
		}
		$id = wp_create_nav_menu( $name );
		return is_wp_error( $id ) ? 0 : $id;
	};

	// The main menu from the design: categories, Brands and Offers. A menu this theme built in
	// an earlier version is rebuilt; a menu you made yourself is never touched.
	$current = ! empty( $locations['primary'] ) ? wp_get_nav_menu_object( $locations['primary'] ) : null;
	if ( ! $current || 'Nexus main menu' === $current->name ) {
		$m = $new_menu( 'Nexus main menu' );
		if ( $m ) {
			$cat_items( $m, 0, 8 );
			$page_item( $m, 'brands', 0, '', __( 'Brands', 'nexus-beauty' ) );
			$page_item( $m, 'offers', 0, 'is-sale' );
			$locations['primary'] = $m;
			$made[]               = __( 'main menu', 'nexus-beauty' );
		}
	}
	if ( empty( $locations['nexus-footer-4'] ) && taxonomy_exists( 'product_brand' ) ) {
		$m = $new_menu( __( 'Brands', 'nexus-beauty' ) );
		if ( $m ) {
			$brands = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => true, 'number' => 4, 'orderby' => 'count', 'order' => 'DESC' ) );
			foreach ( is_wp_error( $brands ) ? array() : $brands as $b ) {
				wp_update_nav_menu_item( $m, 0, array( 'menu-item-object' => 'product_brand', 'menu-item-object-id' => $b->term_id, 'menu-item-type' => 'taxonomy', 'menu-item-status' => 'publish' ) );
			}
			$page_item( $m, 'brands' );
			$locations['nexus-footer-4'] = $m;
			$made[]                      = __( 'footer brands menu', 'nexus-beauty' );
		}
	}
	if ( empty( $locations['nexus-footer-1'] ) && taxonomy_exists( 'product_cat' ) ) {
		$m = $new_menu( __( 'Shop', 'nexus-beauty' ) );
		if ( $m ) {
			$cat_items( $m, 0, 7 );
			$page_item( $m, 'collections' );
			$locations['nexus-footer-1'] = $m;
			$made[]                      = __( 'footer shop menu', 'nexus-beauty' );
		}
	}
	if ( empty( $locations['nexus-footer-2'] ) ) {
		$m = $new_menu( __( 'Help', 'nexus-beauty' ) );
		if ( $m ) {
			foreach ( array( 'track-order', 'delivery', 'returns', 'authenticity', 'faq', 'contact' ) as $slug ) {
				$page_item( $m, $slug );
			}
			$locations['nexus-footer-2'] = $m;
			$made[]                      = __( 'footer help menu', 'nexus-beauty' );
		}
	}
	if ( empty( $locations['nexus-footer-3'] ) ) {
		$m = $new_menu( __( 'Company', 'nexus-beauty' ) );
		if ( $m ) {
			foreach ( array( 'about-us', 'journal', 'routines', 'terms', 'privacy' ) as $slug ) {
				$page_item( $m, $slug );
			}
			$locations['nexus-footer-3'] = $m;
			$made[]                      = __( 'footer company menu', 'nexus-beauty' );
		}
	}
	set_theme_mod( 'nav_menu_locations', $locations );
	if ( $made ) {
		/* translators: %s: list of menus */
		$log[] = sprintf( __( 'Built menus: %s.', 'nexus-beauty' ), implode( ', ', $made ) );
	}
}

/* ------------------------------------------------------------------------ */
/* Store (WooCommerce)                                                       */
/* ------------------------------------------------------------------------ */

/**
 * Store settings, starter products, classic cart/checkout, shipping and cash on delivery.
 *
 * @param array $log             Log.
 * @param bool  $import_products Force the product import.
 */
function nexus_setup_store( &$log, $import_products = false ) {
	$has_products = (bool) wc_get_products( array( 'limit' => 1, 'return' => 'ids', 'status' => array( 'publish', 'draft', 'private' ) ) );
	$has_orders   = (bool) wc_get_orders( array( 'limit' => 1, 'return' => 'ids' ) );

	// Currency and country: only on a fresh store still using WooCommerce's defaults.
	if ( ! $has_orders && 'USD' === get_option( 'woocommerce_currency' ) ) {
		update_option( 'woocommerce_currency', 'PKR' );
		update_option( 'woocommerce_price_num_decimals', 0 );
		update_option( 'woocommerce_currency_pos', 'left_space' );
		$log[] = __( 'Set the store currency to Pakistani rupee (Rs).', 'nexus-beauty' );
	}
	if ( ! $has_orders && 0 === strpos( (string) get_option( 'woocommerce_default_country' ), 'US' ) ) {
		update_option( 'woocommerce_default_country', 'PK' );
		update_option( 'woocommerce_allowed_countries', 'specific' );
		update_option( 'woocommerce_specific_allowed_countries', array( 'PK' ) );
		update_option( 'woocommerce_ship_to_countries', '' );
	}

	// Starter products.
	if ( ! $has_products || $import_products ) {
		$n = nexus_import_products();
		/* translators: %d: number of products */
		$log[] = sprintf( __( 'Imported %d starter products. Add photos and check the batch and expiry fields.', 'nexus-beauty' ), $n );
	} else {
		$log[] = __( 'Your store already has products, so no starter products were imported. Your products now use the design everywhere.', 'nexus-beauty' );
		nexus_apply_term_design( require NEXUS_DIR . '/sample-data/design.php' );
	}

	// Classic cart and checkout (needed for the Pakistan checkout features).
	$prev = get_option( 'nexus_prev_wc_pages', array() );
	foreach ( array( 'cart' => '[woocommerce_cart]', 'checkout' => '[woocommerce_checkout]' ) as $page => $code ) {
		$id = wc_get_page_id( $page );
		if ( $id > 0 && nexus_uses_block( $page ) ) {
			$prev[ $page ] = get_post_field( 'post_content', $id );
			wp_update_post( array( 'ID' => $id, 'post_content' => '<!-- wp:shortcode -->' . $code . '<!-- /wp:shortcode -->' ) );
			/* translators: %s: page name */
			$log[] = sprintf( __( 'Switched the %s page to the classic version.', 'nexus-beauty' ), $page );
		}
	}
	update_option( 'nexus_prev_wc_pages', $prev, false );

	// Shipping: a Pakistan zone with flat-rate delivery and free delivery over the threshold, if no zones exist yet.
	if ( class_exists( 'WC_Shipping_Zones' ) && ! WC_Shipping_Zones::get_zones() ) {
		$zone = new WC_Shipping_Zone();
		$zone->set_zone_name( __( 'Pakistan', 'nexus-beauty' ) );
		$zone->add_location( 'PK', 'country' );
		$zone->save();
		$flat = $zone->add_shipping_method( 'flat_rate' );
		$free = $zone->add_shipping_method( 'free_shipping' );
		update_option( 'woocommerce_flat_rate_' . $flat . '_settings', array( 'title' => __( 'Delivery', 'nexus-beauty' ), 'tax_status' => 'none', 'cost' => '250' ) );
		update_option( 'woocommerce_free_shipping_' . $free . '_settings', array( 'title' => __( 'Free delivery', 'nexus-beauty' ), 'requires' => 'min_amount', 'min_amount' => (string) nexus_opt( 'free_shipping' ), 'ignore_discounts' => 'no' ) );
		/* translators: %s: amount */
		$log[] = sprintf( __( 'Added a Pakistan shipping zone: Rs 250 delivery, free over %s.', 'nexus-beauty' ), nexus_price_text( nexus_opt( 'free_shipping' ) ) );
	}

	// Cash on delivery, if no payment method is enabled yet.
	$any_enabled = false;
	foreach ( WC()->payment_gateways()->payment_gateways() as $gw ) {
		if ( 'yes' === $gw->enabled ) {
			$any_enabled = true;
			break;
		}
	}
	if ( ! $any_enabled ) {
		$cod = (array) get_option( 'woocommerce_cod_settings', array() );
		update_option(
			'woocommerce_cod_settings',
			array_merge(
				$cod,
				array(
					'enabled'      => 'yes',
					'title'        => __( 'Cash on delivery', 'nexus-beauty' ),
					'description'  => __( 'Pay the rider in cash when your parcel arrives. Check the batch and expiry on the sticker first.', 'nexus-beauty' ),
					'instructions' => __( 'Please keep the exact amount ready when your parcel arrives.', 'nexus-beauty' ),
				)
			)
		);
		$log[] = __( 'Turned on cash on delivery.', 'nexus-beauty' );
	}
}

/**
 * Import the starter catalogue. Skips SKUs that already exist, so it never duplicates products.
 *
 * @return int Number of products created.
 */
function nexus_import_products() {
	$items  = require NEXUS_DIR . '/sample-data/products.php';
	$design = require NEXUS_DIR . '/sample-data/design.php';
	$store  = get_bloginfo( 'name' );
	$made   = 0;
	$cross  = array();
	foreach ( $items as $it ) {
		if ( wc_get_product_id_by_sku( $it['sku'] ) ) {
			continue;
		}
		$look    = isset( $design['products'][ $it['sku'] ] ) ? $design['products'][ $it['sku'] ] : array();
		$details = isset( $design['details'][ $it['sku'] ] ) ? $design['details'][ $it['sku'] ] : array();
		$brand   = str_replace( 'Nexus Beauty', $store, (string) $it['brand'] );
		$bmeta   = nexus_design_brand( $design, $brand );
		$tags    = array_filter( array_map( 'trim', explode( ',', $it['tags'] ) ) );
		$lower   = array_map( 'strtolower', $tags );
		if ( $bmeta && 'intl' === $bmeta['origin'] && ! array_intersect( $lower, array( 'korea', 'imported' ) ) ) {
			$tags[] = 'Imported';
		}
		if ( $bmeta && 'local' === $bmeta['origin'] && ! in_array( 'pakistani', $lower, true ) && ! in_array( 'our label', $lower, true ) ) {
			$tags[] = 'Pakistani';
		}

		$p = new WC_Product_Simple();
		$p->set_name( str_replace( 'Nexus Beauty', $store, $it['name'] ) );
		$p->set_sku( $it['sku'] );
		$p->set_status( 'publish' );
		$p->set_catalog_visibility( 'visible' );
		$p->set_short_description( $it['short'] );
		$p->set_regular_price( $it['regular'] );
		if ( '' !== $it['sale'] ) {
			$p->set_sale_price( $it['sale'] );
		}
		$p->set_stock_status( 'instock' );
		$p->set_featured( ! empty( $look['featured'] ) );
		$p->set_category_ids( array( nexus_term_id( $it['category'], 'product_cat' ) ) );
		$p->set_tag_ids( array_map( function ( $t ) { return nexus_term_id( $t, 'product_tag' ); }, $tags ) );
		// Launch dates: the newest four fill "Just arrived"; older ones don't show the "New" badge.
		$days = isset( $look['days_old'] ) ? (int) $look['days_old'] : 120;
		$p->set_date_created( time() - $days * DAY_IN_SECONDS - $made * 60 );
		foreach ( $it['meta'] as $k => $v ) {
			$p->update_meta_data( $k, $v );
		}
		foreach ( array( 'shape', 'tone', 'bg' ) as $k ) {
			if ( ! empty( $look[ $k ] ) ) {
				$p->update_meta_data( '_nx_' . $k, $look[ $k ] );
			}
		}
		if ( ! empty( $look['size'] ) && empty( $it['meta']['_nx_size'] ) ) {
			$p->update_meta_data( '_nx_size', $look['size'] );
		}
		foreach ( $details as $k => $v ) {
			if ( 'description' === $k ) {
				$p->set_description( $v );
				continue;
			}
			$p->update_meta_data( '_nx_' . $k, $v );
		}
		$p->update_meta_data( '_nexus_starter', 1 );
		$id = $p->save();
		if ( $id ) {
			$made++;
			if ( $brand && taxonomy_exists( 'product_brand' ) ) {
				wp_set_object_terms( $id, array( nexus_term_id( $brand, 'product_brand' ) ), 'product_brand' );
			} elseif ( $brand ) {
				$p->update_meta_data( '_nx_brand', $brand );
				$p->save();
			}
			if ( $it['cross'] ) {
				$cross[ $id ] = $it['cross'];
			}
		}
	}
	nexus_apply_term_design( $design );
	// Cross-sells power "Frequently bought together".
	foreach ( $cross as $id => $skus ) {
		$ids = array_filter( array_map( function ( $s ) { return (int) wc_get_product_id_by_sku( trim( $s ) ); }, explode( ',', $skus ) ) );
		$p   = wc_get_product( $id );
		if ( $p && $ids ) {
			$p->set_cross_sell_ids( $ids );
			$p->save();
		}
	}
	delete_transient( 'nexus_price_bounds' );
	if ( function_exists( 'wc_delete_product_transients' ) ) {
		wc_delete_product_transients();
	}
	return $made;
}

/**
 * Get or create a term by name.
 *
 * @param string $name     Term name.
 * @param string $taxonomy Taxonomy.
 * @return int
 */
function nexus_term_id( $name, $taxonomy ) {
	$t = get_term_by( 'name', $name, $taxonomy );
	if ( $t ) {
		return (int) $t->term_id;
	}
	$new = wp_insert_term( $name, $taxonomy );
	return is_wp_error( $new ) ? 0 : (int) $new['term_id'];
}

/* ------------------------------------------------------------------------ */
/* Switching away: restore what we changed                                   */
/* ------------------------------------------------------------------------ */

add_action(
	'switch_theme',
	function () {
		$reading = get_option( 'nexus_prev_reading' );
		if ( is_array( $reading ) ) {
			foreach ( $reading as $k => $v ) {
				update_option( $k, $v );
			}
			delete_option( 'nexus_prev_reading' );
		}
		$prev = get_option( 'nexus_prev_wc_pages' );
		if ( is_array( $prev ) && function_exists( 'wc_get_page_id' ) ) {
			foreach ( $prev as $page => $content ) {
				$id = wc_get_page_id( $page );
				if ( $id > 0 ) {
					wp_update_post( array( 'ID' => $id, 'post_content' => $content ) );
				}
			}
		}
		delete_option( 'nexus_prev_wc_pages' );
	}
);

/* ------------------------------------------------------------------------ */
/* Admin notice after setup                                                  */
/* ------------------------------------------------------------------------ */

add_action(
	'admin_notices',
	function () {
		if ( ! get_option( 'nexus_setup_notice' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		delete_option( 'nexus_setup_notice' );
		$log = get_option( 'nexus_setup_log' );
		echo '<div class="notice notice-success"><p><strong>' . esc_html__( 'Nexus Beauty is set up.', 'nexus-beauty' ) . '</strong></p><ul style="list-style:disc;padding-left:20px">';
		foreach ( (array) ( $log['items'] ?? array() ) as $line ) {
			echo '<li>' . esc_html( $line ) . '</li>';
		}
		echo '</ul><p><a class="button button-primary" href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'View your store', 'nexus-beauty' ) . '</a> <a class="button" href="' . esc_url( admin_url( 'themes.php?page=nexus-setup' ) ) . '">' . esc_html__( 'Setup checklist', 'nexus-beauty' ) . '</a></p></div>';
	}
);

/**
 * Design settings for a brand by name (the own label matches by its first words).
 *
 * @param array  $design Design data.
 * @param string $brand  Brand name.
 * @return array|null
 */
function nexus_design_brand( $design, $brand ) {
	foreach ( $design['brands'] as $name => $meta ) {
		if ( 0 === strcasecmp( $name, $brand ) || ( 'Koh-e-Noor' === $name && 0 === stripos( $brand, 'Koh-e-Noor' ) ) ) {
			return $meta;
		}
	}
	return null;
}

/**
 * Give categories and brands the design's colours, order, notes and origin.
 * Only fills settings that are still empty, so your own changes are kept.
 *
 * @param array $design Design data.
 */
function nexus_apply_term_design( $design ) {
	foreach ( $design['categories'] as $name => $meta ) {
		$term = get_term_by( 'name', $name, 'product_cat' );
		if ( ! $term ) {
			continue;
		}
		foreach ( array( 'shape', 'tone', 'tile', 'short', 'note' ) as $k ) {
			if ( '' === (string) get_term_meta( $term->term_id, 'nx_' . $k, true ) ) {
				update_term_meta( $term->term_id, 'nx_' . $k, $meta[ $k ] );
			}
		}
		if ( ! get_term_meta( $term->term_id, 'order', true ) ) {
			update_term_meta( $term->term_id, 'order', (int) $meta['order'] );
		}
	}
	if ( ! taxonomy_exists( 'product_brand' ) ) {
		return;
	}
	$brands = get_terms( array( 'taxonomy' => 'product_brand', 'hide_empty' => false ) );
	foreach ( is_wp_error( $brands ) ? array() : $brands as $term ) {
		$meta = nexus_design_brand( $design, $term->name );
		if ( ! $meta ) {
			continue;
		}
		foreach ( array( 'origin', 'note', 'derm' ) as $k ) {
			if ( isset( $meta[ $k ] ) && '' === (string) get_term_meta( $term->term_id, 'nx_' . $k, true ) ) {
				update_term_meta( $term->term_id, 'nx_' . $k, $meta[ $k ] );
			}
		}
		if ( ! empty( $meta['desc'] ) && '' === $term->description ) {
			wp_update_term( $term->term_id, 'product_brand', array( 'description' => $meta['desc'] ) );
		}
	}
}
