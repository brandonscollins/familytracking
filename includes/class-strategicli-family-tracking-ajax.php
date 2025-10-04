<?php
/**
 * Handles AJAX functionality for the Strategicli Family Tracking plugin.
 *
 * @package    Strategicli_Family_Tracking
 * @subpackage Strategicli_Family_Tracking/includes
 * @author     Strategicli <info@strategicli.com>
 * @license    GPL-2.0-or-later
 */

class Strategicli_Family_Tracking_Ajax {

	public function __construct() {
		// The constructor is not used in this version.
	}

	/**
	 * Handles the request to advance the turn for Manual or Rotation trackers.
	 */
	public function advance_turn() {
		check_ajax_referer( 'sftr_ajax_nonce', 'nonce' );

		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
		if ( ! $post_id ) {
			wp_send_json_error( 'Invalid Tracker ID.' );
		}

		$participants_raw = get_post_meta( $post_id, '_sftr_participants', true );
		$participants     = ! empty( $participants_raw ) ? array_map( 'trim', explode( ',', $participants_raw ) ) : array();
		$participant_count = count( $participants );

		if ( $participant_count < 1 ) {
			wp_send_json_error( 'No participants found.' );
		}

		$current_index = intval( get_post_meta( $post_id, '_sftr_current_turn_index', true ) );
		$next_index    = ( $current_index + 1 ) % $participant_count;

		update_post_meta( $post_id, '_sftr_current_turn_index', $next_index );

		$next_person = isset( $participants[ $next_index ] ) ? $participants[ $next_index ] : 'N/A';

		// --- History Log ---
		$this->_add_history_entry( $post_id, 'advance_turn', $next_person, 'Advanced to ' . $next_person );

		wp_send_json_success(
			array(
				'new_person_name' => $next_person,
			)
		);
	}

	/**
	 * Handles the request to update points for a participant.
	 */
	public function update_points() {
		check_ajax_referer( 'sftr_ajax_nonce', 'nonce' );

		$post_id     = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
		$participant = isset( $_POST['participant'] ) ? sanitize_text_field( $_POST['participant'] ) : '';
		$action      = isset( $_POST['point_action'] ) ? sanitize_text_field( $_POST['point_action'] ) : '';

		if ( ! $post_id || empty( $participant ) || empty( $action ) ) {
			wp_send_json_error( 'Missing required data.' );
		}

		$points_data = get_post_meta( $post_id, '_sftr_points_data', true );
		if ( ! is_array( $points_data ) || ! isset( $points_data[ $participant ] ) ) {
			wp_send_json_error( 'Participant not found in tracker data.' );
		}

		if ( 'add' === $action ) {
			$points_data[ $participant ]++;
		} elseif ( 'subtract' === $action ) {
			$points_data[ $participant ]--;
		}

		update_post_meta( $post_id, '_sftr_points_data', $points_data );

		// --- History Log ---
		$this->_add_history_entry( $post_id, 'update_points', $participant, ucfirst( $action ) . ' point for ' . $participant );

		wp_send_json_success(
			array(
				'participant' => $participant,
				'new_score'   => $points_data[ $participant ],
			)
		);
	}

	/**
	 * Handles the request to pick a random participant.
	 */
	public function random_pick() {
		check_ajax_referer( 'sftr_ajax_nonce', 'nonce' );

		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
		if ( ! $post_id ) {
			wp_send_json_error( 'Invalid Tracker ID.' );
		}

		$participants_raw = get_post_meta( $post_id, '_sftr_participants', true );
		$participants     = ! empty( $participants_raw ) ? array_map( 'trim', explode( ',', $participants_raw ) ) : array();
        
		if ( empty( $participants ) ) {
			wp_send_json_error( 'No participants to choose from.' );
		}

		$winner_index = array_rand( $participants );
		$winner_name  = $participants[ $winner_index ];

		// --- History Log ---
		$this->_add_history_entry( $post_id, 'random_pick', $winner_name, 'Random pick: ' . $winner_name );

		wp_send_json_success(
			array(
				'winner_name' => $winner_name,
			)
		);
	}

	/**
	 * Handles manual override for turn advancement.
	 */
	public function override_turn() {
		check_ajax_referer( 'sftr_ajax_nonce', 'nonce' );

		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
		$participant = isset( $_POST['participant'] ) ? sanitize_text_field( $_POST['participant'] ) : '';
		$reason = isset( $_POST['reason'] ) ? sanitize_text_field( $_POST['reason'] ) : '';
		if ( ! $post_id || empty( $participant ) ) {
			wp_send_json_error( 'Missing required data.' );
		}

		$participants_raw = get_post_meta( $post_id, '_sftr_participants', true );
		$participants = ! empty( $participants_raw ) ? array_map( 'trim', explode( ',', $participants_raw ) ) : array();
		
		// Make the search case-insensitive
		$index = array_search( strtolower( $participant ), array_map( 'strtolower', $participants ) );

		if ( $index === false ) {
			wp_send_json_error( 'Participant not found.' );
		}
		update_post_meta( $post_id, '_sftr_current_turn_index', $index );

		// Log override
		$this->_add_history_entry( $post_id, 'override_turn', $participant, 'Manual override. Reason: ' . $reason );

		wp_send_json_success(
			array(
				'new_person_name' => $participant,
			)
		);
	}

	/**
	 * Handles manual override for points.
	 */
	public function override_points() {
		check_ajax_referer( 'sftr_ajax_nonce', 'nonce' );

		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
		$participant = isset( $_POST['participant'] ) ? sanitize_text_field( $_POST['participant'] ) : '';
		$new_score = isset( $_POST['new_score'] ) ? intval( $_POST['new_score'] ) : 0;
		$reason = isset( $_POST['reason'] ) ? sanitize_text_field( $_POST['reason'] ) : '';
		if ( ! $post_id || empty( $participant ) ) {
			wp_send_json_error( 'Missing required data.' );
		}

		$points_data = get_post_meta( $post_id, '_sftr_points_data', true );
		if ( ! is_array( $points_data ) || ! isset( $points_data[ $participant ] ) ) {
			wp_send_json_error( 'Participant not found in tracker data.' );
		}
		$points_data[ $participant ] = $new_score;
		update_post_meta( $post_id, '_sftr_points_data', $points_data );

		// Log override
		$this->_add_history_entry( $post_id, 'override_points', $participant, 'Manual override. New score: ' . $new_score . '. Reason: ' . $reason );

		wp_send_json_success(
			array(
				'participant' => $participant,
				'new_score'   => $new_score,
			)
		);
	}

	/**
	 * Handles manual override for random picker.
	 */
	public function override_random() {
		check_ajax_referer( 'sftr_ajax_nonce', 'nonce' );

		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
		$winner = isset( $_POST['winner'] ) ? sanitize_text_field( $_POST['winner'] ) : '';
		$reason = isset( $_POST['reason'] ) ? sanitize_text_field( $_POST['reason'] ) : '';
		if ( ! $post_id || empty( $winner ) ) {
			wp_send_json_error( 'Missing required data.' );
		}

		// Log override
		$this->_add_history_entry( $post_id, 'override_random', $winner, 'Manual override. Winner: ' . $winner . '. Reason: ' . $reason );

		wp_send_json_success(
			array(
				'winner_name' => $winner,
			)
		);
	}

	/**
	 * Adds a new entry to the history log for a tracker.
	 *
	 * @param int    $post_id     The ID of the tracker post.
	 * @param string $action      The action being performed.
	 * @param string $participant The participant involved.
	 * @param string $details     The details of the event.
	 */
	private function _add_history_entry( $post_id, $action, $participant, $details ) {
		$history = get_post_meta( $post_id, '_sftr_history_log', true );
		if ( ! is_array( $history ) ) {
			$history = array();
		}
		$current_user = is_user_logged_in() ? wp_get_current_user()->user_login : 'guest';
		$history[] = array(
			'timestamp'   => time(),
			'action'      => $action,
			'user'        => $current_user,
			'participant' => $participant,
			'details'     => $details,
		);
		update_post_meta( $post_id, '_sftr_history_log', $history );
	}

}
