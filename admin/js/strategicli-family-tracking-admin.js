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
	 * This file is loaded on the "Tracker" CPT edit screen.
	 */
	$(function() {

		const modeSelect = $('#sftr_mode');
		const intervalWrapper = $('#sftr_rotation_interval_wrapper');

		// Function to check the mode and show/hide the interval field.
		function checkMode() {
			if ( modeSelect.val() === 'rotation' ) {
				// Use slideDown for a smooth visual effect.
				intervalWrapper.slideDown();
			} else {
				intervalWrapper.slideUp();
			}
		}

		// Run the check when the page first loads.
		checkMode();

		// Run the check again every time the mode selection changes.
		modeSelect.on('change', function() {
			checkMode();
		});

	});

})( jQuery );
