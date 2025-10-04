<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://strategicli.com
 * @since      1.0.0
 *
 * @package    Strategicli_Family_Tracking
 * @subpackage Strategicli_Family_Tracking/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Registers the shortcode and handles the display of trackers on the front end.
 *
 * @package    Strategicli_Family_Tracking
 * @subpackage Strategicli_Family_Tracking/public
 * @author     Strategicli <info@strategicli.com>
 */
class Strategicli_Family_Tracking_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/strategicli-family-tracking-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/strategicli-family-tracking-public.js', array( 'jquery' ), $this->version, true );

		// Pass data to our script, including the AJAX URL and a nonce for security.
		wp_localize_script(
			$this->plugin_name,
			'sftr_ajax_object',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'sftr_ajax_nonce' ),
			)
		);
	}

	/**
	 * Register the shortcode for displaying trackers.
	 *
	 * @since 1.0.0
	 */
	public function register_shortcode() {
		add_shortcode( 'sftr_tracker', array( $this, 'render_tracker_shortcode' ) );
		add_shortcode( 'sftr_dashboard', array( $this, 'render_dashboard_shortcode' ) );
	}

	/**
	 * Renders the dashboard display, which shows all trackers.
	 *
	 * @since 1.1.0
	 * @param array $atts The shortcode attributes.
	 * @return string The HTML output for the dashboard.
	 */
	public function render_dashboard_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'allow_override' => 'false',
			),
			$atts,
			'sftr_dashboard'
		);

		$allow_override = strtolower( $atts['allow_override'] ) === 'true';

		$args = array(
			'post_type'      => 'sftr_tracker',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		$trackers_query = new WP_Query( $args );

		if ( ! $trackers_query->have_posts() ) {
			return '<p>No trackers found.</p>';
		}

		ob_start();

		echo '<div class="sftr-dashboard-wrapper">';

		while ( $trackers_query->have_posts() ) {
			$trackers_query->the_post();
			$post_id = get_the_ID();
			echo $this->render_tracker_shortcode(
				array(
					'id'             => $post_id,
					'allow_override' => $allow_override ? 'true' : 'false',
				)
			);
		}

		wp_reset_postdata();

		echo '</div>'; // .sftr-dashboard-wrapper

		return ob_get_clean();
	}

	/**
	 * Renders the tracker display based on the shortcode attributes.
	 *
	 * @since 1.0.0
	 * @param array $atts The shortcode attributes.
	 * @return string The HTML output for the tracker.
	 */
	public function render_tracker_shortcode( $atts ) {
		// Normalize attribute keys to lowercase and extract them.
		$atts = shortcode_atts(
			array(
				'id' => 0,
				'allow_override' => 'false',
			),
			$atts,
			'sftr_tracker'
		);

		$post_id = intval( $atts['id'] );
		$allow_override = strtolower( $atts['allow_override'] ) === 'true';

		// If no ID is provided or the post doesn't exist, return nothing.
		if ( ! $post_id || 'sftr_tracker' !== get_post_type( $post_id ) ) {
			return '';
		}

		// Get tracker data.
		$title        = get_the_title( $post_id );
		$mode         = get_post_meta( $post_id, '_sftr_mode', true );
		$participants_raw = get_post_meta( $post_id, '_sftr_participants', true );
		$participants = ! empty( $participants_raw ) ? array_map( 'trim', explode( ',', $participants_raw ) ) : array();

		// Start output buffering.
		ob_start();

		echo '<div class="sftr-tracker-wrapper" id="sftr-tracker-' . esc_attr( $post_id ) . '" data-postid="' . esc_attr( $post_id ) . '">';
		echo '<h3 class="sftr-tracker-title">' . esc_html( $title ) . '</h3>';
		echo '<div class="sftr-tracker-content">';

		switch ( $mode ) {
			case 'rotation':
			case 'manual':
				$this->render_turn_based_view( $post_id, $participants, $allow_override );
				break;
			case 'points':
				$this->render_points_view( $post_id, $participants, $allow_override );
				break;
			case 'random':
				$this->render_random_view( $post_id, $allow_override );
				break;
			default:
				echo '<p>This tracker has not been configured correctly.</p>';
		}

		echo '</div>'; // .sftr-tracker-content

		// Render the history log.
		$this->render_history_log( $post_id );

		echo '</div>'; // .sftr-tracker-wrapper

		// Return the buffered content.
		return ob_get_clean();
	}

	/**
	 * Renders the view for Rotation and Manual modes.
	 *
	 * @param int   $post_id      The ID of the tracker post.
	 * @param array $participants The array of participants.
	 */
	private function render_turn_based_view( $post_id, $participants, $allow_override = false ) {
		$current_index = intval( get_post_meta( $post_id, '_sftr_current_turn_index', true ) );
		$current_person = isset( $participants[ $current_index ] ) ? $participants[ $current_index ] : 'N/A';

		echo '<div class="sftr-turn-based-view">';
		echo '<p class="sftr-current-turn-label">It is currently</p>';
		echo '<p class="sftr-current-turn-name">' . esc_html( $current_person ) . '\'s turn.</p>';
		echo '<button class="sftr-action-button sftr-advance-turn-button">Advance Turn</button>';
		if ( $allow_override ) {
			echo '<button class="sftr-action-button sftr-override-turn-button">Manual Override</button>';
		}
		echo '</div>';
	}

	/**
	 * Renders the view for Points mode.
	 *
	 * @param int   $post_id      The ID of the tracker post.
	 * @param array $participants The array of participants.
	 */
	private function render_points_view( $post_id, $participants, $allow_override = false ) {
		$points_data = get_post_meta( $post_id, '_sftr_points_data', true );
		if ( ! is_array( $points_data ) ) {
			$points_data = array();
		}

		echo '<div class="sftr-points-view">';
		echo '<ul class="sftr-participants-list">';
		foreach ( $participants as $participant ) {
			$score = isset( $points_data[ $participant ] ) ? intval( $points_data[ $participant ] ) : 0;
			echo '<li data-participant="' . esc_attr( $participant ) . '">';
			echo '<span class="sftr-participant-name">' . esc_html( $participant ) . '</span>';
			echo '<span class="sftr-participant-score">' . esc_html( $score ) . '</span>';
			echo '<div class="sftr-points-controls">';
			echo '<button class="sftr-action-button sftr-update-points-button" data-action="add">+</button>';
			echo '<button class="sftr-action-button sftr-update-points-button" data-action="subtract">-</button>';
			if ( $allow_override ) {
				echo '<button class="sftr-action-button sftr-override-points-button" data-participant="' . esc_attr( $participant ) . '">Manual Override</button>';
			}
			echo '</div>';
			echo '</li>';
		}
		echo '</ul>';
		echo '</div>';
	}

	/**
	 * Renders the view for Random mode.
	 *
	 * @param int $post_id The ID of the tracker post.
	 */
	private function render_random_view( $post_id, $allow_override = false ) {
		echo '<div class="sftr-random-view">';
		echo '<p class="sftr-random-result">Click the button to choose someone at random.</p>';
		echo '<button class="sftr-action-button sftr-random-pick-button">Choose for Me</button>';
		if ( $allow_override ) {
			echo '<button class="sftr-action-button sftr-override-random-button">Manual Override</button>';
		}
		echo '</div>';
	}

	/**
	 * Renders the history log for a tracker.
	 *
	 * @param int $post_id The ID of the tracker post.
	 */
	private function render_history_log( $post_id ) {
		$history = get_post_meta( $post_id, '_sftr_history_log', true );

		echo '<div class="sftr-history-log-wrapper">';
		echo '<button class="sftr-action-button sftr-toggle-history-button">View History</button>';
		echo '<div class="sftr-history-log" style="display:none;">';
		echo '<h4>History</h4>';

		if ( ! empty( $history ) && is_array( $history ) ) {
			echo '<ul>';
			// Reverse the array to show the latest events first.
			$reversed_history = array_reverse( $history );
			foreach ( $reversed_history as $entry ) {
				$timestamp = isset( $entry['timestamp'] ) ? ' - ' . date( 'Y-m-d H:i:s', $entry['timestamp'] ) : '';
				$details   = isset( $entry['details'] ) ? esc_html( $entry['details'] ) : 'No details';
				echo '<li>' . $details . $timestamp . '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>No history recorded yet.</p>';
		}

		echo '</div>'; // .sftr-history-log
		echo '</div>'; // .sftr-history-log-wrapper
	}
}
