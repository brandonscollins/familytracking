(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This file handles the AJAX interactions for the trackers.
	 */
	$(function() {

		/**
		 * Handle "Advance Turn" button click.
		 */
		$(document).on('click', '.sftr-advance-turn-button', function(e) {
			e.preventDefault();
			const button = $(this);
			const wrapper = button.closest('.sftr-tracker-wrapper');
			const postId = wrapper.data('postid');

			// Add a loading state to prevent multiple clicks
			button.prop('disabled', true).text('Advancing...');

			$.post(sftr_ajax_object.ajax_url, {
				action: 'sftr_advance_turn',
				nonce: sftr_ajax_object.nonce,
				post_id: postId
			})
			.done(function(response) {
				if (response.success) {
					// Update the name on the screen
					wrapper.find('.sftr-current-turn-name').text(response.data.new_person_name + "'s turn.");
				} else {
					console.error('Error advancing turn:', response.data);
					alert('An error occurred. Please try again.'); // A simple fallback alert
				}
			})
			.fail(function() {
				console.error('AJAX request failed.');
				alert('A server error occurred. Please try again.');
			})
			.always(function() {
				// Restore the button
				button.prop('disabled', false).text('Advance Turn');
			});
		});

		/**
		 * Handle "+/-" buttons for points.
		 */
		$(document).on('click', '.sftr-update-points-button', function(e) {
			e.preventDefault();
			const button = $(this);
			const wrapper = button.closest('.sftr-tracker-wrapper');
			const listItem = button.closest('li');
			const postId = wrapper.data('postid');
			const participant = listItem.data('participant');
			const pointAction = button.data('action');

			// Disable both buttons for this participant
			listItem.find('.sftr-update-points-button').prop('disabled', true);

			$.post(sftr_ajax_object.ajax_url, {
				action: 'sftr_update_points',
				nonce: sftr_ajax_object.nonce,
				post_id: postId,
				participant: participant,
				point_action: pointAction
			})
			.done(function(response) {
				if (response.success) {
					// Update the score on the screen
					listItem.find('.sftr-participant-score').text(response.data.new_score);
				} else {
					console.error('Error updating points:', response.data);
					alert('An error occurred. Please try again.');
				}
			})
			.fail(function() {
				console.error('AJAX request failed.');
				alert('A server error occurred. Please try again.');
			})
			.always(function() {
				// Re-enable the buttons
				listItem.find('.sftr-update-points-button').prop('disabled', false);
			});
		});

		/**
		 * Handle "Choose for Me" button click for random picker.
		 */
		$(document).on('click', '.sftr-random-pick-button', function(e) {
			e.preventDefault();
			const button = $(this);
			const wrapper = button.closest('.sftr-tracker-wrapper');
			const postId = wrapper.data('postid');

			button.prop('disabled', true).text('Choosing...');

			$.post(sftr_ajax_object.ajax_url, {
				action: 'sftr_random_pick',
				nonce: sftr_ajax_object.nonce,
				post_id: postId
			})
			.done(function(response) {
				if (response.success) {
					// Update the result text
					wrapper.find('.sftr-random-result').html('The winner is... <strong>' + response.data.winner_name + '!</strong>');
				} else {
					console.error('Error picking random winner:', response.data);
					alert('An error occurred. Please try again.');
				}
			})
			.fail(function() {
				console.error('AJAX request failed.');
				alert('A server error occurred. Please try again.');
			})
			.always(function() {
				button.prop('disabled', false).text('Choose for Me');
			});
		});
	});

})( jQuery );
