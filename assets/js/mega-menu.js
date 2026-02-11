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
		var showDelay = 200;
		var hideDelay = 300;

		menuItems.forEach(function (item) {
			var dropdown = item.querySelector('.mega-menu-dropdown');
			var showTimer = null;
			var hideTimer = null;

			if (!dropdown) return;

			function show() {
				clearTimeout(hideTimer);
				showTimer = setTimeout(function () {
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
		var categoriesTrigger = document.getElementById('nav-categories-trigger');
		if (categoriesTrigger) {
			categoriesTrigger.addEventListener('click', function () {
				var isExpanded = this.getAttribute('aria-expanded') === 'true';
				this.setAttribute('aria-expanded', !isExpanded);
				// If you have a categories dropdown panel, toggle it here
				var panel = document.querySelector('.categories-dropdown-panel');
				if (panel) {
					panel.classList.toggle('is-active');
				}
			});
		}

		// Close mega menu on Escape
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				document.querySelectorAll('.mega-menu-dropdown.is-active').forEach(function (dd) {
					dd.classList.remove('is-active');
				});
			}
		});

		// Close on click outside
		document.addEventListener('click', function (e) {
			if (!e.target.closest('.has-mega-menu') && !e.target.closest('.nav-categories-trigger')) {
				document.querySelectorAll('.mega-menu-dropdown.is-active').forEach(function (dd) {
					dd.classList.remove('is-active');
				});
			}
		});
	}

	document.addEventListener('DOMContentLoaded', initMegaMenu);
})();
