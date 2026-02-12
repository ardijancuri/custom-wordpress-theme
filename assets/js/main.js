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
		var panels = document.querySelectorAll('.category-tab-panel');

		if (tabs.length === 0) return;

		tabs.forEach(function (tab) {
			tab.addEventListener('click', function () {
				var target = this.getAttribute('data-category');

				// Update active tab
				tabs.forEach(function (t) { t.classList.remove('is-active'); });
				this.classList.add('is-active');

				// Show/hide panels
				panels.forEach(function (panel) {
					if (panel.getAttribute('data-category') === target || target === 'all') {
						panel.classList.add('is-active');
					} else {
						panel.classList.remove('is-active');
					}
				});
			});
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
	});
})();
