import $ from 'jquery';
import Tooltip from 'tooltip.js';
import Sortable from 'sortablejs';

$(() => {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(document).ready(function() {

		const help_tooltips = document.getElementsByClassName('dashicons-editor-help');

		for (let i = 0; i < help_tooltips.length; i++) {
			new Tooltip(help_tooltips[i], {
				placement: 'top', // or bottom, left, right, and variations
				title: () => {
					const text = help_tooltips[i].parentElement.getElementsByClassName('me-tooltip-text');
					return text[0].innerHTML;
				},
				html: true
			});
		}




		const store_element = document.getElementById('me_yml_store');
		Sortable.create(store_element, {
			handle: '.dashicons-move',
			draggable: '.me-list-group-item',
			animation: 150
		});

		/*
		const offer_element = document.getElementById('me_yml_offer');
		Sortable.create(offer_element, {
			handle: '.dashicons-move',
			animation: 150
		});
		*/


		/*
		$('#rate-notice').on('click', function() {
			$.post( ajax_strings.ajax_url, {
				_ajax_nonce: ajax_strings.nonce,
				action: 'dismiss_rate_notice'
			});
		});

		// Disable multiselect if user wants to export all params.
		$('input#params_all').on('click', function() {
			const paramSelect = $('select#params');
			const paramCheckbox = $(this).prop( 'checked' );
			paramSelect.prop( 'disabled', paramCheckbox );
		});
		*/
	});
});
