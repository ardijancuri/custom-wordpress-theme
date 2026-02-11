/**
 * LesnaMax - Shop Filters
 *
 * AJAX product filtering for shop sidebar.
 *
 * @package LesnaMax
 */

(function () {
	'use strict';

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

	document.addEventListener('DOMContentLoaded', initFilters);
})();
