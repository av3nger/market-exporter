(function( $ ) {
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
		/* Enable Select2 for all select fields */
		$('select').select2({
			minimumResultsForSearch: Infinity
		});

		$('#rate-notice').on('click', function() {
			$.post( ajax_strings.ajax_url, {
				_ajax_nonce: ajax_strings.nonce,
				action: 'dismiss_rate_notice'
			});
		});

		// Disable multiselect if user wants to export all params.
		$('input#params_all').on('click', function() {
			var paramSelect = $('select#params');
			var paramCheckbox = $(this).prop( 'checked' );
			paramSelect.prop( 'disabled', paramCheckbox );
		});

		// Export process.
		$('#market-exporter-generate').on('click', function(e) {
			e.preventDefault();

			$('#me-export-form').hide();
			$('.me-progress-export').show();

			process_step( 0, 0, 0 );
		});

		/**
		 * Ajax process export process
		 *
		 * @param step
		 * @param steps
		 * @param percent
		 */
		function process_step( step, steps, percent ) {
			// Send ajax to do the step.
			$.ajax({
				type: 'POST',
				url: ajax_strings.ajax_url,
				data: {
					_ajax_nonce: ajax_strings.export_nonce,
					action: 'export_step',
					step: step,
					steps: steps
				},
				success: function ( response ) {
					// Update progress bar.
					update_progress( percent );

					var data = response.data;
					if ( data.step !== data.steps ) {
						process_step( data.step, data.steps, data.percent );
					} else {
						update_progress( 100 );
						$( '.me-progress-export .status').html( ajax_strings.msg_created + '<a href=' + data.file + ' target="_blank">' + data.file + '</a>' );
					}
				}
			});
		}

		/**
		 * Update progress bar for the export process.
		 *
		 * @param percent
		 */
		function update_progress( percent ) {
			$( '.me-progress-export .status').text( ajax_strings.msg_progress );
			$('.me-progress-export .me-prgoress-container').css( 'width', percent + '%' );
			$('.me-progress-export .me-prgoress-container').text( percent + '%' );
		}
    });

})( jQuery );

function toggle(source) {
  var checkboxes = document.getElementsByName('files[]');
  for ( var i=0, n=checkboxes.length; i<n; i++ ) {
    checkboxes[i].checked = source.checked;
  }
}