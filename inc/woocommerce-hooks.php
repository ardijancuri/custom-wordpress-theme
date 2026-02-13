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

	// Cart drawer body
	ob_start();
	lesnamax_render_cart_drawer_items();
	$fragments['#cart-drawer-body'] = '<div class="flyout-drawer__body" id="cart-drawer-body">' . ob_get_clean() . '</div>';

	// Cart drawer footer
	ob_start();
	lesnamax_render_cart_drawer_footer();
	$fragments['#cart-drawer-footer'] = '<div class="flyout-drawer__footer" id="cart-drawer-footer">' . ob_get_clean() . '</div>';

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

			var outlinedHeart = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>';
			var filledHeart = '<svg width="24" height="24" viewBox="0 0 27 26" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.5088 0C21.6165 6.46555e-05 23.3764 0.727471 24.7891 2.18164C26.2017 3.63581 26.9082 5.44751 26.9082 7.61719C26.9082 8.67894 26.7288 9.71781 26.3701 10.7334C26.0338 11.749 25.4281 12.8685 24.5537 14.0918C23.6792 15.292 22.5023 16.6544 21.0225 18.1777C19.5425 19.7012 17.67 21.5137 15.4053 23.6143L13.4541 25.4141L11.5029 23.6143C9.23818 21.5137 7.36568 19.7012 5.88574 18.1777C4.40593 16.6544 3.22895 15.292 2.35449 14.0918C1.48008 12.8685 0.863633 11.749 0.504883 10.7334C0.168553 9.71778 0 8.67897 0 7.61719C5.01623e-05 5.44753 0.706565 3.63581 2.11914 2.18164C3.53174 0.72749 5.29176 8.39621e-05 7.39941 0C8.56538 0 9.67563 0.253942 10.7295 0.761719C11.7833 1.26951 12.6917 1.98497 13.4541 2.9082C14.2165 1.98495 15.1249 1.26952 16.1787 0.761719C17.2326 0.253921 18.3428 0 19.5088 0Z" fill="#3DB0B4"/></svg>';

			// Update heart icons
			document.querySelectorAll('.product-card__wishlist').forEach(function(btn) {
				var id = btn.getAttribute('data-product-id');
				var iconWrap = btn.querySelector('.icon');
				if (list.indexOf(parseInt(id)) > -1) {
					btn.classList.add('is-active');
					if (iconWrap) iconWrap.innerHTML = filledHeart;
				} else {
					btn.classList.remove('is-active');
					if (iconWrap) iconWrap.innerHTML = outlinedHeart;
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

/**
 * Auto-open coupon form on block-based cart page.
 */
function lesnamax_cart_coupon_auto_open_js() {
	if ( ! is_cart() ) {
		return;
	}
	?>
	<script>
	(function() {
		'use strict';

		function openCouponForm() {
			var panel = document.querySelector('.wc-block-components-totals-coupon');
			if (!panel) return false;

			// Find the panel toggle (the accordion trigger, not the Apply button)
			var toggle = panel.querySelector(':scope > .wc-block-components-panel__button');
			if (!toggle) return false;

			// If the panel content is not rendered yet, click toggle to open it
			var content = panel.querySelector('.wc-block-components-panel__content');
			if (!content || !content.children.length) {
				toggle.click();
				// Remove focus from the coupon input after React renders it
				setTimeout(function() {
					if (document.activeElement) document.activeElement.blur();
				}, 50);
			} else {
				toggle.setAttribute('aria-expanded', 'true');
			}

			return true;
		}

		// Try on DOMContentLoaded, then retry with observer for React hydration
		document.addEventListener('DOMContentLoaded', function() {
			if (openCouponForm()) return;

			// WooCommerce blocks render async via React — observe for the panel
			var observer = new MutationObserver(function(mutations, obs) {
				if (openCouponForm()) {
					obs.disconnect();
				}
			});
			observer.observe(document.body, { childList: true, subtree: true });

			// Safety timeout to disconnect observer
			setTimeout(function() { observer.disconnect(); }, 10000);
		});
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'lesnamax_cart_coupon_auto_open_js' );

/**
 * Route product search queries to the WooCommerce archive template.
 *
 * WooCommerce's template loader does not intercept ?s= searches,
 * so we handle it here to reuse archive-product.php's search UI.
 */
function lesnamax_product_search_template( $template ) {
	if ( is_search() && 'product' === get_query_var( 'post_type' ) ) {
		$new_template = locate_template( 'woocommerce/archive-product.php' );
		if ( $new_template ) {
			return $new_template;
		}
	}
	return $template;
}
add_filter( 'template_include', 'lesnamax_product_search_template' );
