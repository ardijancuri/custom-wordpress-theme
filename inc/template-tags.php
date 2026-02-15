<?php
/**
 * Template Tags - Helper functions for templates.
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Display the site logo or site name.
 */
function lesnamax_site_logo() {
	if ( has_custom_logo() ) {
		the_custom_logo();
	} else {
		printf(
			'<a href="%s" class="site-logo-text" rel="home">%s</a>',
			esc_url( home_url( '/' ) ),
			esc_html( get_bloginfo( 'name' ) )
		);
	}
}

/**
 * Get WooCommerce cart count.
 */
function lesnamax_cart_count() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return 0;
	}
	return WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
}

/**
 * Get wishlist count from localStorage (rendered via JS) or user meta.
 */
function lesnamax_wishlist_count() {
	if ( is_user_logged_in() ) {
		$wishlist = get_user_meta( get_current_user_id(), '_lesnamax_wishlist', true );
		return is_array( $wishlist ) ? count( $wishlist ) : 0;
	}
	return 0; // JS will handle guest count from localStorage
}

/**
 * Display product badge (New or Sale).
 */
function lesnamax_product_badge() {
	global $product;

	if ( ! $product ) {
		return;
	}

	if ( $product->is_on_sale() ) {
		$regular  = (float) $product->get_regular_price();
		$sale     = (float) $product->get_sale_price();
		if ( $regular > 0 ) {
			$percent = round( ( ( $regular - $sale ) / $regular ) * 100 );
			printf( '<span class="product-badge product-badge--sale">-%s%%</span>', esc_html( $percent ) );
		}
	} elseif ( lesnamax_is_new_product( $product ) ) {
		echo '<span class="product-badge product-badge--new">' . esc_html__( 'RISI', 'lesnamax' ) . '</span>';
	}
}

/**
 * Check if product is new (published within last 30 days).
 */
function lesnamax_is_new_product( $product, $days = 30 ) {
	$created = $product->get_date_created();
	if ( ! $created ) {
		return false;
	}
	$now  = new DateTime();
	$diff = $now->diff( $created );
	return $diff->days <= $days;
}

/**
 * Display product color swatches.
 */
function lesnamax_color_swatches() {
	global $product;

	if ( ! $product ) {
		return;
	}

	$colors = array();

	if ( $product->is_type( 'variable' ) ) {
		$attributes = $product->get_variation_attributes();
		foreach ( $attributes as $attr_name => $options ) {
			$taxonomy = str_replace( 'attribute_', '', $attr_name );
			if ( stripos( $taxonomy, 'ngjyr' ) !== false || stripos( $taxonomy, 'color' ) !== false ) {
				foreach ( $options as $option ) {
					$term = get_term_by( 'slug', $option, $taxonomy );
					if ( $term ) {
						$color_hex = get_term_meta( $term->term_id, 'color_hex', true );
						$colors[]  = array(
							'name'  => $term->name,
							'hex'   => $color_hex ? $color_hex : '#ccc',
						);
					}
				}
			}
		}
	}

	if ( ! empty( $colors ) ) {
		echo '<div class="product-swatches">';
		foreach ( $colors as $color ) {
			printf(
				'<span class="color-swatch" style="background-color:%s" title="%s"></span>',
				esc_attr( $color['hex'] ),
				esc_attr( $color['name'] )
			);
		}
		echo '</div>';
	}
}

/**
 * Display product availability.
 */
function lesnamax_product_availability() {
	global $product;

	if ( ! $product ) {
		return;
	}

	$stock_status = $product->get_stock_status();
	$class        = 'instock' === $stock_status ? 'available' : 'unavailable';
	$text         = 'instock' === $stock_status
		? esc_html__( 'Në stok', 'lesnamax' )
		: esc_html__( 'Nuk ka stok', 'lesnamax' );

	printf(
		'<span class="product-availability product-availability--%s"><span class="availability-dot"></span>%s</span>',
		esc_attr( $class ),
		$text
	);
}

/**
 * Get theme asset URL.
 */
function lesnamax_asset( $path ) {
	return LESNAMAX_URI . '/assets/' . ltrim( $path, '/' );
}

/**
 * Output inline SVG icon.
 */
function lesnamax_icon( $name, $class = '' ) {
	$icons = array(
		'search' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
		'account' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
		'heart' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>',
		'heart-filled' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>',
		'cart' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>',
		'menu' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>',
		'close' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>',
		'grid' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>',
		'list' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>',
		'chevron-down' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>',
		'chevron-left' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>',
		'chevron-right' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>',
		'phone' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>',
		'mail' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
		'map-pin' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>',
		'catalog' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>',
	);

	if ( isset( $icons[ $name ] ) ) {
		$extra_class = $class ? ' ' . esc_attr( $class ) : '';
		printf( '<span class="icon icon--%s%s">%s</span>', esc_attr( $name ), $extra_class, $icons[ $name ] );
	}
}

/**
 * Return inline SVG icon markup (non-echoing version).
 */
function lesnamax_get_icon( $name, $class = '' ) {
	ob_start();
	lesnamax_icon( $name, $class );
	return ob_get_clean();
}

/**
 * Breadcrumb wrapper for WooCommerce.
 */
function lesnamax_breadcrumbs() {
	if ( class_exists( 'WooCommerce' ) ) {
		woocommerce_breadcrumb( array(
			'wrap_before' => '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'lesnamax' ) . '">',
			'wrap_after'  => '</nav>',
			'before'      => '<span class="breadcrumb-item">',
			'after'       => '</span>',
			'delimiter'   => '<span class="breadcrumb-sep">/</span>',
		) );
	}
}
