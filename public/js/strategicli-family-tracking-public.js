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
		 * Handle "Manual Override" for turn advancement.
		 */
		$(document).on('click', '.sftr-override-turn-button', function(e) {
			e.preventDefault();
			const button = $(this);
			const wrapper = button.closest('.sftr-tracker-wrapper');
			const postId = wrapper.data('postid');
			const participants = wrapper.find('.sftr-participant-name').map(function(){ return $(this).text(); }).get();
			let participant = prompt('Enter the name of the participant to set as current turn:', participants[0] || '');
			if (!participant) return;
			let reason = prompt('Optional: Enter a reason for override (e.g., absent, swap, etc.):', '');
			button.prop('disabled', true).text('Overriding...');
			$.post(sftr_ajax_object.ajax_url, {
				action: 'sftr_override_turn',
				nonce: sftr_ajax_object.nonce,
				post_id: postId,
				participant: participant,
				reason: reason
			})
			.done(function(response) {
				if (response.success) {
					wrapper.find('.sftr-current-turn-name').text(response.data.new_person_name + "'s turn.");
				} else {
					console.error('Error overriding turn:', response.data);
					alert('An error occurred. Please try again.');
				}
			})
			.fail(function() {
				console.error('AJAX request failed.');
				alert('A server error occurred. Please try again.');
			})
			.always(function() {
				button.prop('disabled', false).text('Manual Override');
			});
		});

		/**
		 * Handle "Manual Override" for points.
		 */
		$(document).on('click', '.sftr-override-points-button', function(e) {
			e.preventDefault();
			const button = $(this);
			const wrapper = button.closest('.sftr-tracker-wrapper');
			const listItem = button.closest('li');
			const postId = wrapper.data('postid');
			const participant = button.data('participant');
			let newScore = prompt('Enter the new score for ' + participant + ':', listItem.find('.sftr-participant-score').text());
			if (newScore === null) return;
			let reason = prompt('Optional: Enter a reason for override:', '');
			button.prop('disabled', true).text('Overriding...');
			$.post(sftr_ajax_object.ajax_url, {
				action: 'sftr_override_points',
				nonce: sftr_ajax_object.nonce,
				post_id: postId,
				participant: participant,
				new_score: newScore,
				reason: reason
			})
			.done(function(response) {
				if (response.success) {
					listItem.find('.sftr-participant-score').text(response.data.new_score);
				} else {
					console.error('Error overriding points:', response.data);
					alert('An error occurred. Please try again.');
				}
			})
			.fail(function() {
				console.error('AJAX request failed.');
				alert('A server error occurred. Please try again.');
			})
			.always(function() {
				button.prop('disabled', false).text('Manual Override');
			});
		});

		/**
		 * Handle "Manual Override" for random picker.
		 */
		$(document).on('click', '.sftr-override-random-button', function(e) {
			e.preventDefault();
			const button = $(this);
			const wrapper = button.closest('.sftr-tracker-wrapper');
			const postId = wrapper.data('postid');
			let winner = prompt('Enter the name of the participant to set as winner:', '');
			if (!winner) return;
			let reason = prompt('Optional: Enter a reason for override:', '');
			button.prop('disabled', true).text('Overriding...');
			$.post(sftr_ajax_object.ajax_url, {
				action: 'sftr_override_random',
				nonce: sftr_ajax_object.nonce,
				post_id: postId,
				winner: winner,
				reason: reason
			})
			.done(function(response) {
				if (response.success) {
					wrapper.find('.sftr-random-result').html('The winner is... <strong>' + response.data.winner_name + '!</strong>');
				} else {
					console.error('Error overriding random pick:', response.data);
					alert('An error occurred. Please try again.');
				}
			})
			.fail(function() {
				console.error('AJAX request failed.');
				alert('A server error occurred. Please try again.');
			})
			.always(function() {
				button.prop('disabled', false).text('Manual Override');
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

		/**
		 * Handle "View History" button click.
		 */
		$(document).on('click', '.sftr-toggle-history-button', function(e) {
			e.preventDefault();
			const button = $(this);
			const historyLog = button.siblings('.sftr-history-log');

			// Toggle visibility and update button text
			historyLog.slideToggle(200, function() {
				if (historyLog.is(':visible')) {
					button.text('Hide History');
				} else {
					button.text('View History');
				}
			});
		});
	});

})( jQuery );
