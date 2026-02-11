/**
 * LesnaMax - Search Autocomplete
 *
 * Live product search with debounced input.
 *
 * @package LesnaMax
 */

(function () {
	'use strict';

	function initSearchAutocomplete() {
		var searchInput = document.querySelector('.header-search__input');
		var resultsContainer = document.getElementById('search-results');

		if (!searchInput || !resultsContainer) return;

		var debounceTimer = null;
		var minChars = 3;
		var currentRequest = null;

		searchInput.addEventListener('input', function () {
			var query = this.value.trim();

			clearTimeout(debounceTimer);

			if (query.length < minChars) {
				resultsContainer.classList.remove('is-active');
				resultsContainer.innerHTML = '';
				return;
			}

			debounceTimer = setTimeout(function () {
				performSearch(query);
			}, 300);
		});

		// Close on click outside
		document.addEventListener('click', function (e) {
			if (!e.target.closest('.header-search')) {
				resultsContainer.classList.remove('is-active');
			}
		});

		// Close on Escape
		searchInput.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				resultsContainer.classList.remove('is-active');
			}
		});

		// Re-open on focus if has results
		searchInput.addEventListener('focus', function () {
			if (resultsContainer.innerHTML.trim() !== '') {
				resultsContainer.classList.add('is-active');
			}
		});

		function performSearch(query) {
			// Abort previous request
			if (currentRequest) {
				currentRequest.abort();
			}

			var controller = new AbortController();
			currentRequest = controller;

			var formData = new FormData();
			formData.append('action', 'lesnamax_search_products');
			formData.append('query', query);
			formData.append('nonce', lesnamaxSearch.nonce);

			fetch(lesnamaxSearch.ajaxUrl, {
				method: 'POST',
				body: formData,
				signal: controller.signal,
			})
				.then(function (response) {
					return response.json();
				})
				.then(function (data) {
					if (data.success && data.data.html) {
						resultsContainer.innerHTML = data.data.html;
						resultsContainer.classList.add('is-active');
					} else {
						resultsContainer.innerHTML =
							'<div class="search-no-results">' +
							'<p>Asnje rezultat per "' + escapeHtml(query) + '"</p>' +
							'</div>';
						resultsContainer.classList.add('is-active');
					}
				})
				.catch(function (err) {
					if (err.name !== 'AbortError') {
						resultsContainer.classList.remove('is-active');
					}
				});
		}

		function escapeHtml(str) {
			var div = document.createElement('div');
			div.appendChild(document.createTextNode(str));
			return div.innerHTML;
		}
	}

	document.addEventListener('DOMContentLoaded', initSearchAutocomplete);
})();
