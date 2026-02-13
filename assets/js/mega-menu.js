/**
 * LesnaMax - Mega Menu
 *
 * Hover events with delay, show/hide mega menu panels.
 *
 * @package LesnaMax
 */

(function () {
	'use strict';

	function initMegaMenu() {
		var menuItems = document.querySelectorAll('.has-mega-menu');
		var categoriesTrigger = document.getElementById('nav-categories-trigger');
		var categoriesPanel = document.getElementById('categories-dropdown-panel');
		var showDelay = 200;
		var hideDelay = 300;

		function closeCategoriesPanel() {
			if (categoriesPanel && categoriesPanel.classList.contains('is-active')) {
				categoriesPanel.classList.remove('is-active');
				if (categoriesTrigger) {
					categoriesTrigger.setAttribute('aria-expanded', 'false');
					categoriesTrigger.classList.remove('is-active');
				}
			}
		}

		menuItems.forEach(function (item) {
			var dropdown = item.querySelector('.mega-menu-dropdown');
			var showTimer = null;
			var hideTimer = null;

			if (!dropdown) return;

			function show() {
				clearTimeout(hideTimer);
				showTimer = setTimeout(function () {
					closeCategoriesPanel();
					dropdown.classList.add('is-active');
				}, showDelay);
			}

			function hide() {
				clearTimeout(showTimer);
				hideTimer = setTimeout(function () {
					dropdown.classList.remove('is-active');
				}, hideDelay);
			}

			item.addEventListener('mouseenter', show);
			item.addEventListener('mouseleave', hide);
			dropdown.addEventListener('mouseenter', function () {
				clearTimeout(hideTimer);
			});
			dropdown.addEventListener('mouseleave', hide);

			// Keyboard accessibility
			var link = item.querySelector('a');
			if (link) {
				link.addEventListener('focus', function () {
					dropdown.classList.add('is-active');
				});
			}

			// Close when focus leaves the mega menu
			item.addEventListener('focusout', function (e) {
				setTimeout(function () {
					if (!item.contains(document.activeElement)) {
						dropdown.classList.remove('is-active');
					}
				}, 100);
			});
		});

		// Categories trigger (left dropdown button)
		var nav = document.getElementById('primary-nav');

		function updatePanelPosition() {
			if (nav && categoriesPanel) {
				categoriesPanel.style.top = nav.getBoundingClientRect().bottom + 'px';
			}
		}

		if (categoriesTrigger && categoriesPanel) {
			categoriesTrigger.addEventListener('click', function (e) {
				e.stopPropagation();
				var isExpanded = this.getAttribute('aria-expanded') === 'true';
				this.setAttribute('aria-expanded', !isExpanded);
				this.classList.toggle('is-active');

				updatePanelPosition();
				categoriesPanel.classList.toggle('is-active');

				// Close any open mega menu dropdowns
				document.querySelectorAll('.mega-menu-dropdown.is-active').forEach(function (dd) {
					dd.classList.remove('is-active');
				});
			});

			// Close panel instantly on scroll
			window.addEventListener('scroll', function () {
				if (categoriesPanel.classList.contains('is-active')) {
					categoriesPanel.style.transition = 'none';
					categoriesPanel.style.opacity = '0';
					categoriesPanel.style.visibility = 'hidden';
					categoriesPanel.classList.remove('is-active');
					if (categoriesTrigger) {
						categoriesTrigger.setAttribute('aria-expanded', 'false');
						categoriesTrigger.classList.remove('is-active');
					}
					// Restore CSS transition after browser paints
					requestAnimationFrame(function () {
						requestAnimationFrame(function () {
							categoriesPanel.style.transition = '';
							categoriesPanel.style.opacity = '';
							categoriesPanel.style.visibility = '';
						});
					});
				}
			}, { passive: true });
		}

		// Close mega menu on Escape
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				closeCategoriesPanel();
				document.querySelectorAll('.mega-menu-dropdown.is-active').forEach(function (dd) {
					dd.classList.remove('is-active');
				});
			}
		});

		// Close on click outside
		document.addEventListener('click', function (e) {
			if (!e.target.closest('.has-mega-menu')) {
				document.querySelectorAll('.mega-menu-dropdown.is-active').forEach(function (dd) {
					dd.classList.remove('is-active');
				});
			}
			if (!e.target.closest('.nav-categories-trigger') && !e.target.closest('.categories-dropdown-panel')) {
				closeCategoriesPanel();
			}
		});
	}

	document.addEventListener('DOMContentLoaded', initMegaMenu);
})();
