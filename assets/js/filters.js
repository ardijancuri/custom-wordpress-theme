/**
 * LesnaMax - Shop Filters
 *
 * AJAX product filtering for shop sidebar.
 *
 * @package LesnaMax
 */

(function () {
	'use strict';

	var RECENTLY_VIEWED_KEY = 'lesnamax_recently_viewed';

	function initFilters() {
		var sidebar = document.querySelector('.shop-sidebar');
		var productsContainer = document.querySelector('.shop-products');

		if (!sidebar || !productsContainer) return;

		// Filter group collapse/expand
		var filterTitles = sidebar.querySelectorAll('.filter-group__title');
		filterTitles.forEach(function (title) {
			title.addEventListener('click', function () {
				var group = this.closest('.filter-group');
				group.classList.toggle('is-collapsed');
			});
		});

		// Checkbox change handlers
		sidebar.addEventListener('change', function (e) {
			if (e.target.classList.contains('filter-item__checkbox')) {
				applyFilters();
			}
		});

		// View toggle
		var viewBtns = document.querySelectorAll('.view-toggle__btn');
		viewBtns.forEach(function (btn) {
			btn.addEventListener('click', function () {
				viewBtns.forEach(function (b) { b.classList.remove('is-active'); });
				this.classList.add('is-active');

				var view = this.getAttribute('data-view');
				productsContainer.classList.remove('view-grid', 'view-list');
				productsContainer.classList.add('view-' + view);
			});
		});

		// Sort change
		var sortSelect = document.querySelector('.shop-sort-select');
		if (sortSelect) {
			sortSelect.addEventListener('change', applyFilters);
		}

		// Per-page change
		var perPageSelect = document.querySelector('.shop-perpage-select');
		if (perPageSelect) {
			perPageSelect.addEventListener('change', applyFilters);
		}

		// ---- Price Range Filter ----
		var priceMin = document.getElementById('price-min');
		var priceMax = document.getElementById('price-max');
		var priceMinDisplay = document.getElementById('price-min-display');
		var priceMaxDisplay = document.getElementById('price-max-display');
		var priceRangeFill = document.getElementById('price-range-fill');
		var priceTimeout = null;

		function updatePriceRangeUI() {
			if (!priceMin || !priceMax) return;

			var minVal = parseInt(priceMin.value, 10);
			var maxVal = parseInt(priceMax.value, 10);
			var totalRange = parseInt(priceMin.max, 10) - parseInt(priceMin.min, 10);
			var minOffset = parseInt(priceMin.min, 10);

			// Prevent overlap
			if (minVal > maxVal) {
				priceMin.value = maxVal;
				minVal = maxVal;
			}

			// Update display
			if (priceMinDisplay) priceMinDisplay.textContent = minVal + '\u20AC';
			if (priceMaxDisplay) priceMaxDisplay.textContent = maxVal + '\u20AC';

			// Update range fill bar
			if (priceRangeFill && totalRange > 0) {
				var leftPercent = ((minVal - minOffset) / totalRange) * 100;
				var rightPercent = ((maxVal - minOffset) / totalRange) * 100;
				priceRangeFill.style.left = leftPercent + '%';
				priceRangeFill.style.width = (rightPercent - leftPercent) + '%';
			}
		}

		function onPriceChange() {
			updatePriceRangeUI();
			clearTimeout(priceTimeout);
			priceTimeout = setTimeout(applyFilters, 400);
		}

		if (priceMin && priceMax) {
			priceMin.addEventListener('input', onPriceChange);
			priceMax.addEventListener('input', onPriceChange);
			updatePriceRangeUI();

			// Re-sync UI when page is restored from browser back/forward cache
			window.addEventListener('pageshow', function (e) {
				if (e.persisted) {
					updatePriceRangeUI();
				}
			});
		}

		// ---- Recently Visited Products ----
		renderRecentlyViewed();

		function applyFilters() {
			var filters = {};

			// Collect checked filters
			var checkboxes = sidebar.querySelectorAll('.filter-item__checkbox:checked');
			checkboxes.forEach(function (cb) {
				var group = cb.getAttribute('data-filter-group');
				var value = cb.value;

				if (!filters[group]) {
					filters[group] = [];
				}
				filters[group].push(value);
			});

			// Price filter
			if (priceMin && priceMax) {
				var minVal = parseInt(priceMin.value, 10);
				var maxVal = parseInt(priceMax.value, 10);
				var globalMin = parseInt(priceMin.min, 10);
				var globalMax = parseInt(priceMin.max, 10);

				if (minVal > globalMin || maxVal < globalMax) {
					filters.price_min = minVal;
					filters.price_max = maxVal;
				}
			}

			// Sort
			if (sortSelect) {
				filters.orderby = sortSelect.value;
			}

			// Per page
			if (perPageSelect) {
				filters.per_page = perPageSelect.value;
			}

			// Current category
			var currentCategory = productsContainer.getAttribute('data-category');
			if (currentCategory) {
				filters.category = currentCategory;
			}

			// Show loading state
			productsContainer.classList.add('is-loading');

			var formData = new FormData();
			formData.append('action', 'lesnamax_filter_products');
			formData.append('nonce', lesnamaxFilters.nonce);
			formData.append('filters', JSON.stringify(filters));

			fetch(lesnamaxFilters.ajaxUrl, {
				method: 'POST',
				body: formData,
			})
				.then(function (response) {
					return response.json();
				})
				.then(function (data) {
					productsContainer.classList.remove('is-loading');

					if (data.success) {
						productsContainer.innerHTML = data.data.html;

						// Update product count
						var countEl = document.querySelector('.shop-product-count');
						if (countEl && data.data.count !== undefined) {
							countEl.textContent = data.data.count;
						}

						// Update pagination
						var paginationEl = document.querySelector('.shop-pagination');
						if (paginationEl && data.data.pagination) {
							paginationEl.innerHTML = data.data.pagination;
						}

						// Update URL without reload
						if (data.data.url) {
							window.history.pushState({}, '', data.data.url);
						}
					}
				})
				.catch(function () {
					productsContainer.classList.remove('is-loading');
				});
		}
	}

	/**
	 * Track product views on single product pages.
	 */
	function trackProductView() {
		var productData = document.querySelector('[data-recently-viewed-product]');
		if (!productData) return;

		var product = {
			id: productData.getAttribute('data-product-id'),
			name: productData.getAttribute('data-product-name'),
			price: productData.getAttribute('data-product-price'),
			image: productData.getAttribute('data-product-image'),
			url: productData.getAttribute('data-product-url')
		};

		if (!product.id) return;

		var viewed = getRecentlyViewed();

		// Remove if already exists
		viewed = viewed.filter(function (p) { return p.id !== product.id; });

		// Add to beginning
		viewed.unshift(product);

		// Keep max 3
		viewed = viewed.slice(0, 3);

		try {
			localStorage.setItem(RECENTLY_VIEWED_KEY, JSON.stringify(viewed));
		} catch (e) {
			// localStorage not available
		}
	}

	function getRecentlyViewed() {
		try {
			var data = localStorage.getItem(RECENTLY_VIEWED_KEY);
			return data ? JSON.parse(data) : [];
		} catch (e) {
			return [];
		}
	}

	/**
	 * Render recently viewed products in the sidebar.
	 */
	function renderRecentlyViewed() {
		var container = document.getElementById('recently-visited-products');
		var section = document.getElementById('recently-visited-section');
		if (!container || !section) return;

		var viewed = getRecentlyViewed();
		if (viewed.length === 0) return;

		container.innerHTML = '';
		viewed.forEach(function (product) {
			var link = document.createElement('a');
			link.href = product.url;
			link.className = 'recently-visited-item';

			var imageDiv = document.createElement('div');
			imageDiv.className = 'recently-visited-item__image';
			var img = document.createElement('img');
			img.src = product.image;
			img.alt = product.name;
			imageDiv.appendChild(img);

			var infoDiv = document.createElement('div');
			infoDiv.className = 'recently-visited-item__info';
			var nameSpan = document.createElement('span');
			nameSpan.className = 'recently-visited-item__name';
			nameSpan.textContent = product.name;
			var priceSpan = document.createElement('span');
			priceSpan.className = 'recently-visited-item__price';
			priceSpan.textContent = product.price;
			infoDiv.appendChild(nameSpan);
			infoDiv.appendChild(priceSpan);

			link.appendChild(imageDiv);
			link.appendChild(infoDiv);
			container.appendChild(link);
		});
		section.style.display = '';
	}

	document.addEventListener('DOMContentLoaded', function () {
		initFilters();
		trackProductView();
	});
})();
