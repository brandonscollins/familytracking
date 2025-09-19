<?php
/**
 * The AJAX functionality of the plugin.
 *
 * @link       https://strategicli.com
 * @since      1.0.0
 *
 * @package    Strategicli_Family_Tracking
 * @subpackage Strategicli_Family_Tracking/includes
 */

/**
 * The AJAX functionality of the plugin.
 *
 * Handles all AJAX requests for the public-facing side of the site.
 *
 * @package    Strategicli_Family_Tracking
 * @subpackage Strategicli_Family_Tracking/includes
 * @author     Strategicli <info@strategicli.com>
 */
class Strategicli_Family_Tracking_Ajax {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
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

		wp_send_json_success(
			array(
				'winner_name' => $winner_name,
			)
		);
	}
}
