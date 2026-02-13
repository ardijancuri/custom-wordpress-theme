/**
 * LesnaMax - AJAX Add to Cart
 *
 * Handles AJAX add-to-cart for product cards and single product pages.
 *
 * @package LesnaMax
 */

(function () {
	'use strict';

	function initAjaxCart() {
		// Delegate click events for add-to-cart buttons
		document.addEventListener('click', function (e) {
			var btn = e.target.closest('.product-card__add-to-cart, .ajax-add-to-cart');
			if (!btn) return;

			var productId = btn.getAttribute('data-product-id');
			var quantity = btn.getAttribute('data-quantity') || 1;

			if (!productId) return;

			e.preventDefault();

			// Prevent double-click
			if (btn.classList.contains('is-loading')) return;

			btn.classList.add('is-loading');
			var originalText = btn.innerHTML;
			btn.innerHTML = '<span class="btn-spinner"></span>';

			var formData = new FormData();
			formData.append('action', 'lesnamax_add_to_cart');
			formData.append('product_id', productId);
			formData.append('quantity', quantity);
			formData.append('nonce', lesnamaxAjax.nonce);

			fetch(lesnamaxAjax.ajaxUrl, {
				method: 'POST',
				body: formData,
			})
				.then(function (response) {
					return response.json();
				})
				.then(function (data) {
					btn.classList.remove('is-loading');
					btn.innerHTML = originalText;

					if (data.success) {
						// Update cart count
						updateCartCount(data.data.cart_count);

						// Refresh cart drawer content and open it
						refreshAndOpenCartDrawer();

						// Trigger WC cart fragments refresh
						if (typeof jQuery !== 'undefined') {
							jQuery(document.body).trigger('wc_fragment_refresh');
						}
					} else {
						showCartNotification(data.data.message || lesnamaxAjax.i18n.error);
					}
				})
				.catch(function () {
					btn.classList.remove('is-loading');
					btn.innerHTML = originalText;
					showCartNotification(lesnamaxAjax.i18n.error);
				});
		});
	}

	/**
	 * Update cart count in header.
	 */
	function updateCartCount(count) {
		var counters = document.querySelectorAll('.cart-count');
		counters.forEach(function (counter) {
			counter.textContent = count;
			counter.setAttribute('data-count', count);

			// Animate bounce
			counter.classList.add('is-updated');
			setTimeout(function () {
				counter.classList.remove('is-updated');
			}, 600);
		});
	}

	/**
	 * Show cart notification toast (fallback).
	 */
	function showCartNotification(message) {
		var notification = document.getElementById('cart-notification');
		if (!notification) return;

		var messageEl = notification.querySelector('.cart-notification__message');
		if (messageEl) {
			messageEl.textContent = message;
		}

		notification.classList.add('is-visible');

		setTimeout(function () {
			notification.classList.remove('is-visible');
		}, 4000);
	}

	/**
	 * Refresh cart drawer content via AJAX, then open the drawer.
	 */
	function refreshAndOpenCartDrawer() {
		var formData = new FormData();
		formData.append('action', 'lesnamax_get_cart_drawer');
		formData.append('nonce', lesnamaxAjax.nonce);

		fetch(lesnamaxAjax.ajaxUrl, {
			method: 'POST',
			body: formData
		})
			.then(function (r) { return r.json(); })
			.then(function (data) {
				if (data.success) {
					var body = document.getElementById('cart-drawer-body');
					var footer = document.getElementById('cart-drawer-footer');
					if (body) body.innerHTML = data.data.body;
					if (footer) footer.innerHTML = data.data.footer;
				}

				// Open the drawer
				if (typeof window.lesnamaxOpenCartDrawer === 'function') {
					window.lesnamaxOpenCartDrawer();
				}
			});
	}

	// Expose globally so other scripts (e.g. wishlist drawer) can refresh the cart drawer
	window.lesnamaxRefreshAndOpenCartDrawer = refreshAndOpenCartDrawer;

	document.addEventListener('DOMContentLoaded', initAjaxCart);
})();
