<?php
/**
 * WooCommerce Hooks & Filters
 *
 * Customizes WooCommerce behavior to match our theme's design.
 *
 * @package LesnaMax
 */

defined( 'ABSPATH' ) || exit;

/**
 * Override products per page.
 */
function lesnamax_products_per_page( $cols ) {
	if ( isset( $_GET['per_page'] ) ) {
		return absint( $_GET['per_page'] );
	}
	return 12;
}
add_filter( 'loop_shop_per_page', 'lesnamax_products_per_page' );

/**
 * Override product columns.
 */
function lesnamax_loop_columns() {
	return 4;
}
add_filter( 'loop_shop_columns', 'lesnamax_loop_columns' );

/**
 * Remove default WooCommerce wrapper.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

/**
 * Remove default WooCommerce sidebar.
 */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Remove default product loop actions (we use our own card template).
 */
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

/**
 * Remove default single product actions (we use our own template).
 */
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

/**
 * Related products args.
 */
function lesnamax_related_products_args( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'lesnamax_related_products_args' );

/**
 * Customize breadcrumb defaults.
 */
function lesnamax_wc_breadcrumb_defaults( $defaults ) {
	$defaults['delimiter']   = '<span class="breadcrumb-sep">/</span>';
	$defaults['wrap_before'] = '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'lesnamax' ) . '">';
	$defaults['wrap_after']  = '</nav>';
	$defaults['before']      = '<span class="breadcrumb-item">';
	$defaults['after']       = '</span>';
	return $defaults;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'lesnamax_wc_breadcrumb_defaults' );

/**
 * Cart fragments for header cart count.
 */
function lesnamax_cart_fragments( $fragments ) {
	$count = WC()->cart->get_cart_contents_count();
	$fragments['.cart-count'] = '<span class="header-icon-count cart-count">' . esc_html( $count ) . '</span>';
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'lesnamax_cart_fragments' );

/**
 * Change currency symbol position for Euro.
 */
function lesnamax_currency_symbol_after( $format, $currency_pos ) {
	switch ( $currency_pos ) {
		case 'right':
			return '%2$s %1$s';
		case 'right_space':
			return '%2$s&nbsp;%1$s';
		default:
			return $format;
	}
}
add_filter( 'woocommerce_price_format', 'lesnamax_currency_symbol_after', 10, 2 );

/**
 * Add product tabs via JS.
 */
function lesnamax_single_product_tab_js() {
	if ( ! is_product() ) {
		return;
	}
	?>
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		var triggers = document.querySelectorAll('.product-tab__trigger');
		var panels = document.querySelectorAll('.product-tab__panel');

		triggers.forEach(function(trigger) {
			trigger.addEventListener('click', function() {
				var tabId = this.getAttribute('data-tab');

				triggers.forEach(function(t) { t.classList.remove('is-active'); });
				panels.forEach(function(p) { p.classList.remove('is-active'); });

				this.classList.add('is-active');
				var panel = document.querySelector('.product-tab__panel[data-tab="' + tabId + '"]');
				if (panel) panel.classList.add('is-active');
			});
		});
	});
	</script>
	<?php
}
add_action( 'wp_footer', 'lesnamax_single_product_tab_js' );

/**
 * Wishlist functionality (simple custom implementation).
 */
function lesnamax_wishlist_js() {
	?>
	<script>
	(function() {
		'use strict';

		var STORAGE_KEY = 'lesnamax_wishlist';

		function getWishlist() {
			try {
				var data = localStorage.getItem(STORAGE_KEY);
				return data ? JSON.parse(data) : [];
			} catch(e) {
				return [];
			}
		}

		function saveWishlist(list) {
			try {
				localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
			} catch(e) {}
		}

		function updateUI() {
			var list = getWishlist();

			// Update count
			var counts = document.querySelectorAll('.wishlist-count');
			counts.forEach(function(el) {
				el.textContent = list.length;
				el.setAttribute('data-count', list.length);
			});

			// Update heart icons
			document.querySelectorAll('.product-card__wishlist').forEach(function(btn) {
				var id = btn.getAttribute('data-product-id');
				if (list.indexOf(parseInt(id)) > -1) {
					btn.classList.add('is-active');
				} else {
					btn.classList.remove('is-active');
				}
			});
		}

		document.addEventListener('click', function(e) {
			var btn = e.target.closest('.product-card__wishlist');
			if (!btn) return;

			e.preventDefault();
			var id = parseInt(btn.getAttribute('data-product-id'));
			if (!id) return;

			var list = getWishlist();
			var index = list.indexOf(id);

			if (index > -1) {
				list.splice(index, 1);
			} else {
				list.push(id);
			}

			saveWishlist(list);
			updateUI();
		});

		document.addEventListener('DOMContentLoaded', updateUI);
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'lesnamax_wishlist_js' );
