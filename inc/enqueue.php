<?php
/**
 * Enqueue scripts and styles.
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue front-end styles.
 */
function lesnamax_styles() {
	// Google Fonts - Roboto
	wp_enqueue_style(
		'lesnamax-google-fonts',
		'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&family=Roboto:wght@300;400;500;700&display=swap',
		array(),
		null
	);

	// Main theme styles
	wp_enqueue_style(
		'lesnamax-style',
		LESNAMAX_URI . '/assets/css/style.css',
		array( 'lesnamax-google-fonts' ),
		LESNAMAX_VERSION
	);

	// WooCommerce overrides
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style(
			'lesnamax-woocommerce',
			LESNAMAX_URI . '/assets/css/woocommerce.css',
			array( 'lesnamax-style' ),
			LESNAMAX_VERSION
		);
	}

	// Responsive styles
	wp_enqueue_style(
		'lesnamax-responsive',
		LESNAMAX_URI . '/assets/css/responsive.css',
		array( 'lesnamax-style' ),
		LESNAMAX_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'lesnamax_styles' );

/**
 * Enqueue front-end scripts.
 */
function lesnamax_scripts() {
	// Main script (depends on ajax-cart when WooCommerce is active for drawer functionality)
	$main_deps = array();
	if ( class_exists( 'WooCommerce' ) ) {
		$main_deps[] = 'lesnamax-ajax-cart';
	}

	wp_enqueue_script(
		'lesnamax-main',
		LESNAMAX_URI . '/assets/js/main.js',
		$main_deps,
		LESNAMAX_VERSION,
		true
	);

	// Mega menu
	wp_enqueue_script(
		'lesnamax-mega-menu',
		LESNAMAX_URI . '/assets/js/mega-menu.js',
		array(),
		LESNAMAX_VERSION,
		true
	);

	// Search autocomplete
	wp_enqueue_script(
		'lesnamax-search',
		LESNAMAX_URI . '/assets/js/search-autocomplete.js',
		array(),
		LESNAMAX_VERSION,
		true
	);

	// AJAX cart
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_script(
			'lesnamax-ajax-cart',
			LESNAMAX_URI . '/assets/js/ajax-cart.js',
			array(),
			LESNAMAX_VERSION,
			true
		);

		wp_localize_script( 'lesnamax-ajax-cart', 'lesnamaxAjax', array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'lesnamax_ajax_nonce' ),
			'cartUrl'   => wc_get_cart_url(),
			'i18n'      => array(
				'added'          => esc_html__( 'Shtuar ne shporte', 'lesnamax' ),
				'error'          => esc_html__( 'Gabim. Provoni perseri.', 'lesnamax' ),
				'viewCart'       => esc_html__( 'Shiko shporten', 'lesnamax' ),
				'wishlistEmpty'  => esc_html__( 'Lista e deshirave eshte bosh.', 'lesnamax' ),
			),
		) );
	}

	// Product gallery (single product only)
	if ( class_exists( 'WooCommerce' ) && is_product() ) {
		wp_enqueue_script(
			'lesnamax-product-gallery',
			LESNAMAX_URI . '/assets/js/product-gallery.js',
			array(),
			LESNAMAX_VERSION,
			true
		);
	}

	// Shop filters (shop/archive + single product for recently viewed tracking)
	if ( class_exists( 'WooCommerce' ) && ( is_shop() || is_product_category() || is_product_tag() || is_search() || is_product() ) ) {
		wp_enqueue_script(
			'lesnamax-filters',
			LESNAMAX_URI . '/assets/js/filters.js',
			array(),
			LESNAMAX_VERSION,
			true
		);

		wp_localize_script( 'lesnamax-filters', 'lesnamaxFilters', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'lesnamax_filter_nonce' ),
		) );
	}

	// Localize search script
	wp_localize_script( 'lesnamax-search', 'lesnamaxSearch', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'lesnamax_search_nonce' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'lesnamax_scripts' );

/**
 * Remove default WooCommerce styles.
 */
function lesnamax_dequeue_wc_styles( $enqueue_styles ) {
	unset( $enqueue_styles['woocommerce-general'] );
	unset( $enqueue_styles['woocommerce-layout'] );
	unset( $enqueue_styles['woocommerce-smallscreen'] );
	return $enqueue_styles;
}
add_filter( 'woocommerce_enqueue_styles', 'lesnamax_dequeue_wc_styles' );
