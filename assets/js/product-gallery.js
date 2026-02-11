/**
 * LesnaMax - Product Gallery
 *
 * Thumbnail click to swap main image on single product page.
 *
 * @package LesnaMax
 */

(function () {
	'use strict';

	function initProductGallery() {
		var gallery = document.querySelector('.product-gallery');
		if (!gallery) return;

		var mainImage = gallery.querySelector('.product-gallery__main img');
		var thumbs = gallery.querySelectorAll('.product-gallery__thumb');

		if (!mainImage || thumbs.length === 0) return;

		thumbs.forEach(function (thumb) {
			thumb.addEventListener('click', function () {
				var fullSrc = this.getAttribute('data-full');
				if (!fullSrc) {
					var img = this.querySelector('img');
					fullSrc = img ? img.getAttribute('data-full') || img.src : null;
				}

				if (!fullSrc) return;

				// Update main image with fade transition
				mainImage.style.opacity = '0';
				setTimeout(function () {
					mainImage.src = fullSrc;
					mainImage.style.opacity = '1';
				}, 200);

				// Update active state
				thumbs.forEach(function (t) {
					t.classList.remove('is-active');
				});
				this.classList.add('is-active');
			});
		});

		// Set first thumb as active
		if (thumbs.length > 0) {
			thumbs[0].classList.add('is-active');
		}

		// Main image transition
		mainImage.style.transition = 'opacity 0.2s ease';
	}

	document.addEventListener('DOMContentLoaded', initProductGallery);
})();
