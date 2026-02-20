/**
 * LesnaMax - Main JavaScript
 *
 * Sticky header, announcement bar dismiss, mobile menu, smooth scroll.
 *
 * @package LesnaMax
 */

(function () {
	'use strict';

	/**
	 * Sticky Header
	 *
	 * Uses CSS position:sticky for the actual sticking.
	 * JS only adds a shadow class once the header starts sticking.
	 */
	function initStickyHeader() {
		var header = document.getElementById('site-header');
		if (!header) return;

		// Create a sentinel right before the header to detect when it sticks
		var sentinel = document.createElement('div');
		sentinel.setAttribute('aria-hidden', 'true');
		sentinel.style.height = '0';
		sentinel.style.width = '0';
		sentinel.style.overflow = 'hidden';
		header.parentNode.insertBefore(sentinel, header);

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						header.classList.add('is-sticky');
					} else {
						header.classList.remove('is-sticky');
					}
				});
			},
			{ threshold: 0 }
		);

		observer.observe(sentinel);
	}

	/**
	 * Announcement Bar Dismiss
	 */
	function initAnnouncementDismiss() {
		const bar = document.getElementById('announcement-bar');
		if (!bar) return;

		const closeBtn = bar.querySelector('.announcement-bar__close');
		if (!closeBtn) return;

		// Check if already dismissed
		if (localStorage.getItem('lesnamax_announcement_dismissed') === '1') {
			bar.style.display = 'none';
			return;
		}

		closeBtn.addEventListener('click', function () {
			bar.style.display = 'none';
			localStorage.setItem('lesnamax_announcement_dismissed', '1');
		});
	}

	/**
	 * Mobile Menu
	 */
	function initMobileMenu() {
		const toggle = document.getElementById('mobile-menu-toggle');
		const overlay = document.getElementById('mobile-nav-overlay');
		const closeBtn = document.getElementById('mobile-nav-close');
		const nav = document.getElementById('mobile-nav');

		if (!toggle || !overlay) return;

		function openMenu() {
			overlay.classList.add('is-active');
			document.body.style.overflow = 'hidden';
			toggle.setAttribute('aria-expanded', 'true');
		}

		function closeMenu() {
			overlay.classList.remove('is-active');
			document.body.style.overflow = '';
			toggle.setAttribute('aria-expanded', 'false');
		}

		toggle.addEventListener('click', openMenu);

		if (closeBtn) {
			closeBtn.addEventListener('click', closeMenu);
		}

		overlay.addEventListener('click', function (e) {
			if (e.target === overlay) {
				closeMenu();
			}
		});

		// Close on Escape
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && overlay.classList.contains('is-active')) {
				closeMenu();
			}
		});

		// Mobile submenu toggles
		var menuItems = overlay.querySelectorAll('.menu-item-has-children > a');
		menuItems.forEach(function (item) {
			var arrow = document.createElement('button');
			arrow.className = 'mobile-submenu-toggle';
			arrow.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>';
			arrow.setAttribute('aria-label', 'Toggle submenu');
			item.parentNode.insertBefore(arrow, item.nextSibling);

			arrow.addEventListener('click', function (e) {
				e.preventDefault();
				var submenu = item.parentNode.querySelector('.sub-menu');
				if (submenu) {
					submenu.classList.toggle('is-open');
					arrow.classList.toggle('is-rotated');
				}
			});
		});
	}

	/**
	 * Hero Slider
	 */
	function initHeroSlider() {
		const slider = document.querySelector('.hero-slider');
		if (!slider) return;

		const track = slider.querySelector('.hero-slider__track');
		const slides = slider.querySelectorAll('.hero-slide');
		const dots = slider.querySelectorAll('.hero-slider__dot');
		var slideCount = slides.length;

		if (slideCount <= 1) return;

		// Clone first and last slides for seamless infinite loop
		var firstClone = slides[0].cloneNode(true);
		var lastClone = slides[slideCount - 1].cloneNode(true);
		firstClone.classList.add('hero-slide--clone');
		lastClone.classList.add('hero-slide--clone');
		track.appendChild(firstClone);
		track.insertBefore(lastClone, slides[0]);

		// Position starts at 1 (real first slide, after the prepended clone)
		var currentIndex = 1;
		var autoPlayInterval = null;
		var userInteracted = false;
		var isTransitioning = false;

		// Set initial position without transition
		track.style.transition = 'none';
		track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';
		// Force reflow
		track.offsetHeight;
		track.style.transition = '';

		function updateDots() {
			var realIndex = currentIndex - 1;
			if (realIndex < 0) realIndex = slideCount - 1;
			if (realIndex >= slideCount) realIndex = 0;
			dots.forEach(function (dot, i) {
				dot.classList.toggle('is-active', i === realIndex);
			});
		}

		function goToSlide(index) {
			if (isTransitioning) return;
			isTransitioning = true;
			currentIndex = index;
			track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';
			updateDots();
		}

		// After transition ends, snap to real slide if on a clone
		track.addEventListener('transitionend', function () {
			isTransitioning = false;
			// Went past last real slide into first clone
			if (currentIndex >= slideCount + 1) {
				track.style.transition = 'none';
				currentIndex = 1;
				track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';
				track.offsetHeight;
				track.style.transition = '';
			}
			// Went before first real slide into last clone
			if (currentIndex <= 0) {
				track.style.transition = 'none';
				currentIndex = slideCount;
				track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';
				track.offsetHeight;
				track.style.transition = '';
			}
		});

		function stopAutoPlay() {
			clearInterval(autoPlayInterval);
			userInteracted = true;
		}

		// Dot click handlers
		dots.forEach(function (dot, index) {
			dot.addEventListener('click', function () {
				stopAutoPlay();
				goToSlide(index + 1);
			});
		});

		// Auto-play
		function startAutoPlay() {
			if (userInteracted) return;
			autoPlayInterval = setInterval(function () {
				goToSlide(currentIndex + 1);
			}, 5000);
		}

		startAutoPlay();

		// Pause on hover (only if auto-play still active)
		slider.addEventListener('mouseenter', function () {
			if (!userInteracted) clearInterval(autoPlayInterval);
		});

		slider.addEventListener('mouseleave', function () {
			if (!userInteracted) startAutoPlay();
		});

		// Touch swipe support
		var touchStartX = 0;
		var swipeThreshold = 50;

		slider.addEventListener('touchstart', function (e) {
			touchStartX = e.changedTouches[0].clientX;
			stopAutoPlay();
		}, { passive: true });

		slider.addEventListener('touchend', function (e) {
			var diff = touchStartX - e.changedTouches[0].clientX;

			if (Math.abs(diff) > swipeThreshold) {
				if (diff > 0) {
					goToSlide(currentIndex + 1);
				} else {
					goToSlide(currentIndex - 1);
				}
			}
		}, { passive: true });

		// Mouse drag swipe support (desktop)
		var mouseStartX = 0;
		var isDragging = false;
		var didDrag = false;

		slider.addEventListener('dragstart', function (e) {
			e.preventDefault();
		});

		slider.addEventListener('mousedown', function (e) {
			isDragging = true;
			didDrag = false;
			mouseStartX = e.clientX;
			stopAutoPlay();
			slider.style.cursor = 'grabbing';
			e.preventDefault();
		});

		slider.addEventListener('mousemove', function (e) {
			if (!isDragging) return;
			if (Math.abs(mouseStartX - e.clientX) > 5) {
				didDrag = true;
			}
			e.preventDefault();
		});

		slider.addEventListener('mouseup', function (e) {
			if (!isDragging) return;
			isDragging = false;
			slider.style.cursor = '';
			var diff = mouseStartX - e.clientX;

			if (Math.abs(diff) > swipeThreshold) {
				if (diff > 0) {
					goToSlide(currentIndex + 1);
				} else {
					goToSlide(currentIndex - 1);
				}
			}
		});

		slider.addEventListener('mouseleave', function () {
			if (isDragging) {
				isDragging = false;
				slider.style.cursor = '';
			}
		});

		slider.addEventListener('click', function (e) {
			if (didDrag) {
				e.preventDefault();
				didDrag = false;
			}
		});
	}

	/**
	 * Categories Carousel
	 */
	function initCategoriesCarousel() {
		document.querySelectorAll('.categories-carousel').forEach(function (carousel) {
			var track = carousel.querySelector('.categories-carousel__track');
			var prevBtn = carousel.querySelector('.carousel-arrow--prev');
			var nextBtn = carousel.querySelector('.carousel-arrow--next');

			if (!track) return;

			function getCardWidth() {
				var card = track.querySelector('.category-card');
				return card ? card.offsetWidth : 300;
			}

			if (prevBtn) {
				prevBtn.addEventListener('click', function () {
					track.scrollBy({ left: -getCardWidth(), behavior: 'smooth' });
				});
			}

			if (nextBtn) {
				nextBtn.addEventListener('click', function () {
					track.scrollBy({ left: getCardWidth(), behavior: 'smooth' });
				});
			}
		});
	}

	/**
	 * Category Tabs (Homepage featured products)
	 */
	function initCategoryTabs() {
		var tabs = document.querySelectorAll('.category-tab');
		var grid = document.querySelector('.featured-products .products-grid');
		var viewAllBtn = document.querySelector('.featured-products__view-all');

		if (tabs.length === 0 || !grid) return;

		tabs.forEach(function (tab) {
			tab.addEventListener('click', function () {
				var category = this.getAttribute('data-category');
				var url = this.getAttribute('data-url');

				// Update active tab
				tabs.forEach(function (t) { t.classList.remove('is-active'); });
				this.classList.add('is-active');

				// Update view all button URL
				if (viewAllBtn && url) {
					viewAllBtn.setAttribute('href', url);
				}

				// Fetch products via AJAX
				grid.classList.add('is-loading');

				var formData = new FormData();
				formData.append('action', 'lesnamax_homepage_tab_products');
				formData.append('nonce', lesnamaxAjax.nonce);
				formData.append('category', category);

				fetch(lesnamaxAjax.ajaxUrl, {
					method: 'POST',
					body: formData,
				})
					.then(function (r) { return r.json(); })
					.then(function (data) {
						grid.classList.remove('is-loading');
						if (data.success) {
							grid.innerHTML = data.data.html;
						}
					})
					.catch(function () {
						grid.classList.remove('is-loading');
					});
			});
		});
	}

	/**
	 * Cart Drawer
	 */
	function initCartDrawer() {
		var overlay = document.getElementById('cart-drawer-overlay');
		var drawer = document.getElementById('cart-drawer');
		var cartBtn = document.getElementById('header-cart');

		if (!overlay || !drawer) return;

		function openCartDrawer() {
			overlay.classList.add('is-active');
			document.body.style.overflow = 'hidden';
		}

		function closeCartDrawer() {
			overlay.classList.remove('is-active');
			document.body.style.overflow = '';
		}

		// Open on header cart click
		if (cartBtn) {
			cartBtn.addEventListener('click', function (e) {
				e.preventDefault();
				openCartDrawer();
			});
		}

		// Close button
		var closeBtn = drawer.querySelector('.flyout-drawer__close');
		if (closeBtn) {
			closeBtn.addEventListener('click', closeCartDrawer);
		}

		// Overlay click
		overlay.addEventListener('click', function (e) {
			if (e.target === overlay) {
				closeCartDrawer();
			}
		});

		// Escape key
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && overlay.classList.contains('is-active')) {
				closeCartDrawer();
			}
		});

		// Quantity +/- and remove (delegated)
		drawer.addEventListener('click', function (e) {
			var minusBtn = e.target.closest('.flyout-qty-btn--minus');
			var plusBtn = e.target.closest('.flyout-qty-btn--plus');
			var removeBtn = e.target.closest('.flyout-item__remove');

			if (!minusBtn && !plusBtn && !removeBtn) return;

			var cartKey, newQty;

			if (minusBtn) {
				cartKey = minusBtn.getAttribute('data-cart-key');
				var qtyEl = minusBtn.parentNode.querySelector('.flyout-qty-value');
				newQty = Math.max(0, parseInt(qtyEl.textContent, 10) - 1);
			} else if (plusBtn) {
				cartKey = plusBtn.getAttribute('data-cart-key');
				var qtyEl2 = plusBtn.parentNode.querySelector('.flyout-qty-value');
				newQty = parseInt(qtyEl2.textContent, 10) + 1;
			} else if (removeBtn) {
				cartKey = removeBtn.getAttribute('data-cart-key');
				newQty = 0;
			}

			if (!cartKey || typeof lesnamaxAjax === 'undefined') return;

			var formData = new FormData();
			formData.append('action', 'lesnamax_update_cart_item');
			formData.append('cart_key', cartKey);
			formData.append('quantity', newQty);
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

						// Update header count
						var counters = document.querySelectorAll('.cart-count');
						counters.forEach(function (c) {
							c.textContent = data.data.cart_count;
							c.setAttribute('data-count', data.data.cart_count);
						});
					}
				});
		});

		// Expose open function globally so ajax-cart.js can call it
		window.lesnamaxOpenCartDrawer = openCartDrawer;

		// Recommendation slider arrows (delegated for dynamic content)
		drawer.addEventListener('click', function (e) {
			var arrow = e.target.closest('.flyout-recs__arrow');
			if (!arrow) return;

			var track = drawer.querySelector('.flyout-recs__track');
			if (!track) return;

			var card = track.querySelector('.flyout-rec-card');
			if (!card) return;

			var scrollAmount = card.offsetWidth + 8; // card width + gap
			if (arrow.classList.contains('flyout-recs__arrow--prev')) {
				track.scrollLeft -= scrollAmount;
			} else {
				track.scrollLeft += scrollAmount;
			}
		});
	}

	/**
	 * Wishlist Drawer
	 */
	function initWishlistDrawer() {
		var overlay = document.getElementById('wishlist-drawer-overlay');
		var drawer = document.getElementById('wishlist-drawer');
		var wishlistBtn = document.getElementById('header-wishlist');

		if (!overlay || !drawer) return;

		var STORAGE_KEY = 'lesnamax_wishlist';

		function getWishlist() {
			try {
				var data = localStorage.getItem(STORAGE_KEY);
				return data ? JSON.parse(data) : [];
			} catch (e) {
				return [];
			}
		}

		function saveWishlist(list) {
			try {
				localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
			} catch (e) {}
		}

		function openWishlistDrawer() {
			overlay.classList.add('is-active');
			document.body.style.overflow = 'hidden';
			loadWishlistItems();
		}

		function closeWishlistDrawer() {
			overlay.classList.remove('is-active');
			document.body.style.overflow = '';
		}

		function loadWishlistItems() {
			var list = getWishlist();
			var body = document.getElementById('wishlist-drawer-body');
			if (!body) return;

			if (list.length === 0 || typeof lesnamaxAjax === 'undefined') {
				body.innerHTML = '<div class="flyout-drawer__empty"><p>' + (lesnamaxAjax && lesnamaxAjax.i18n && lesnamaxAjax.i18n.wishlistEmpty ? lesnamaxAjax.i18n.wishlistEmpty : 'Lista e Dëshirave është bosh.') + '</p></div>';
				return;
			}

			body.innerHTML = '<div class="flyout-drawer__empty"><span class="btn-spinner"></span></div>';

			var formData = new FormData();
			formData.append('action', 'lesnamax_get_wishlist_products');
			formData.append('product_ids', JSON.stringify(list));
			formData.append('nonce', lesnamaxAjax.nonce);

			fetch(lesnamaxAjax.ajaxUrl, {
				method: 'POST',
				body: formData
			})
				.then(function (r) { return r.json(); })
				.then(function (data) {
					if (data.success) {
						body.innerHTML = data.data.html;
					}
				});
		}

		// Open on header wishlist click
		if (wishlistBtn) {
			wishlistBtn.addEventListener('click', function (e) {
				e.preventDefault();
				openWishlistDrawer();
			});
		}

		// Close button
		var closeBtn = drawer.querySelector('.flyout-drawer__close');
		if (closeBtn) {
			closeBtn.addEventListener('click', closeWishlistDrawer);
		}

		// Overlay click
		overlay.addEventListener('click', function (e) {
			if (e.target === overlay) {
				closeWishlistDrawer();
			}
		});

		// Escape key
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && overlay.classList.contains('is-active')) {
				closeWishlistDrawer();
			}
		});

		// Remove + Add to Cart (delegated)
		drawer.addEventListener('click', function (e) {
			var removeBtn = e.target.closest('.flyout-wishlist-remove');
			var addToCartBtn = e.target.closest('.flyout-wishlist-add-to-cart');

			if (removeBtn) {
				var productId = parseInt(removeBtn.getAttribute('data-product-id'), 10);
				var list = getWishlist();
				var index = list.indexOf(productId);
				if (index > -1) {
					list.splice(index, 1);
					saveWishlist(list);
				}

				// Remove from DOM
				var item = removeBtn.closest('.flyout-item');
				if (item) item.remove();

				// Update wishlist counts
				var counts = document.querySelectorAll('.wishlist-count');
				counts.forEach(function (el) {
					el.textContent = list.length;
					el.setAttribute('data-count', list.length);
				});

				// Update heart icons on product cards
				document.querySelectorAll('.product-card__wishlist[data-product-id="' + productId + '"]').forEach(function (btn) {
					btn.classList.remove('is-active');
				});

				// Show empty state if no items left
				if (list.length === 0) {
					var body = document.getElementById('wishlist-drawer-body');
					if (body) {
						body.innerHTML = '<div class="flyout-drawer__empty"><p>' + (lesnamaxAjax && lesnamaxAjax.i18n && lesnamaxAjax.i18n.wishlistEmpty ? lesnamaxAjax.i18n.wishlistEmpty : 'Lista e Dëshirave është bosh.') + '</p></div>';
					}
				}
			}

			if (addToCartBtn) {
				var pid = addToCartBtn.getAttribute('data-product-id');
				if (!pid || typeof lesnamaxAjax === 'undefined') return;

				addToCartBtn.classList.add('is-loading');
				var originalText = addToCartBtn.innerHTML;
				addToCartBtn.innerHTML = '<span class="btn-spinner"></span>';

				var formData = new FormData();
				formData.append('action', 'lesnamax_add_to_cart');
				formData.append('product_id', pid);
				formData.append('quantity', 1);
				formData.append('nonce', lesnamaxAjax.nonce);

				fetch(lesnamaxAjax.ajaxUrl, {
					method: 'POST',
					body: formData
				})
					.then(function (r) { return r.json(); })
					.then(function (data) {
						addToCartBtn.classList.remove('is-loading');
						addToCartBtn.innerHTML = originalText;

						if (data.success) {
							// Update cart count
							var counters = document.querySelectorAll('.cart-count');
							counters.forEach(function (c) {
								c.textContent = data.data.cart_count;
								c.setAttribute('data-count', data.data.cart_count);
							});

							// Silently refresh cart drawer content (don't open it)
							var fd = new FormData();
							fd.append('action', 'lesnamax_get_cart_drawer');
							fd.append('nonce', lesnamaxAjax.nonce);
							fetch(lesnamaxAjax.ajaxUrl, { method: 'POST', body: fd })
								.then(function (r) { return r.json(); })
								.then(function (res) {
									if (res.success) {
										var cartBody = document.getElementById('cart-drawer-body');
										var cartFooter = document.getElementById('cart-drawer-footer');
										if (cartBody) cartBody.innerHTML = res.data.body;
										if (cartFooter) cartFooter.innerHTML = res.data.footer;
									}
								});

							// Refresh cart fragments
							if (typeof jQuery !== 'undefined') {
								jQuery(document.body).trigger('wc_fragment_refresh');
							}
						}
					})
					.catch(function () {
						addToCartBtn.classList.remove('is-loading');
						addToCartBtn.innerHTML = originalText;
					});
			}
		});
	}

	/**
	 * Product Sliders
	 */
	function initProductSliders() {
		document.querySelectorAll('.product-slider__carousel').forEach(function (carousel) {
			var track = carousel.querySelector('.product-slider__track');
			var prevBtn = carousel.querySelector('.product-slider__arrow--prev');
			var nextBtn = carousel.querySelector('.product-slider__arrow--next');

			if (!track) return;

			function getScrollAmount() {
				var card = track.querySelector('.product-slider__card');
				if (!card) return 300;
				var gap = parseInt(getComputedStyle(track).gap) || 16;
				return card.offsetWidth + gap;
			}

			if (prevBtn) {
				prevBtn.addEventListener('click', function () {
					track.scrollBy({ left: -getScrollAmount(), behavior: 'smooth' });
				});
			}

			if (nextBtn) {
				nextBtn.addEventListener('click', function () {
					track.scrollBy({ left: getScrollAmount(), behavior: 'smooth' });
				});
			}
		});
	}

	/**
	 * Initialize all
	 */
	document.addEventListener('DOMContentLoaded', function () {
		initStickyHeader();
		initAnnouncementDismiss();
		initMobileMenu();
		initHeroSlider();
		initCategoriesCarousel();
		initCategoryTabs();
		initProductSliders();
		initCartDrawer();
		initWishlistDrawer();
	});
})();
