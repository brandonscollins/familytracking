<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://strategicli.com
 * @since      1.0.0
 *
 * @package    Strategicli_Family_Tracking
 * @subpackage Strategicli_Family_Tracking/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for creating the meta boxes
 * for the "Tracker" custom post type.
 *
 * @package    Strategicli_Family_Tracking
 * @subpackage Strategicli_Family_Tracking/admin
 * @author     Strategicli <info@strategicli.com>
 */
class Strategicli_Family_Tracking_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/strategicli-family-tracking-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook The current admin page.
	 */
	public function enqueue_scripts( $hook ) {
		// Only load our script on the CPT edit screen to avoid conflicts.
		global $post;
		if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && isset( $post->post_type ) && 'sftr_tracker' === $post->post_type ) {
			wp_enqueue_script( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'js/strategicli-family-tracking-admin.js', array( 'jquery' ), $this->version, true );
		}
	}

	/**
	 * Adds the meta box to the "Tracker" post type edit screen.
	 *
	 * @since 1.0.0
	 */
	public function add_tracker_meta_box() {
		add_meta_box(
			'sftr_tracker_settings_meta_box',
			'Tracker Settings',
			array( $this, 'render_tracker_meta_box' ),
			'sftr_tracker',
			'normal',
			'high'
		);
	}

	/**
	 * Renders the HTML for the "Tracker Settings" meta box.
	 *
	 * @since 1.0.0
	 * @param WP_Post $post The post object.
	 */
	public function render_tracker_meta_box( $post ) {
		// Add a nonce field so we can check for it later.
		wp_nonce_field( 'sftr_tracker_save_meta_box_data', 'sftr_tracker_meta_box_nonce' );

		// Retrieve existing values from the database.
		$participants = get_post_meta( $post->ID, '_sftr_participants', true );
		$mode         = get_post_meta( $post->ID, '_sftr_mode', true );
		$interval     = get_post_meta( $post->ID, '_sftr_rotation_interval', true );

		?>
		<style>
			.sftr-meta-box-field { margin-bottom: 20px; }
			.sftr-meta-box-field label { font-weight: bold; display: block; margin-bottom: 5px; }
			.sftr-meta-box-field textarea, .sftr-meta-box-field select { width: 100%; max-width: 500px; }
			.sftr-meta-box-field p.description { font-style: italic; color: #666; }
		</style>

		<div class="sftr-meta-box-field">
			<label for="sftr_participants">Participants</label>
			<textarea id="sftr_participants" name="sftr_participants" rows="4"><?php echo esc_textarea( $participants ); ?></textarea>
			<p class="description">Enter a comma-separated list of participant names (e.g., Jacey, Simon, Mom, Dad).</p>
		</div>

		<div class="sftr-meta-box-field">
			<label for="sftr_mode">Tracker Mode</label>
			<select id="sftr_mode" name="sftr_mode">
				<option value="">Select a Mode</option>
				<option value="rotation" <?php selected( $mode, 'rotation' ); ?>>Chronological Rotation</option>
				<option value="manual" <?php selected( $mode, 'manual' ); ?>>Manual Tap-to-Advance</option>
				<option value="points" <?php selected( $mode, 'points' ); ?>>Score/Point Tracking</option>
				<option value="random" <?php selected( $mode, 'random' ); ?>>Random Picker</option>
			</select>
			<p class="description">Choose how this tracker will advance.</p>
		</div>

		<div class="sftr-meta-box-field" id="sftr_rotation_interval_wrapper" style="display: none;">
			<label for="sftr_rotation_interval">Rotation Interval</label>
			<select id="sftr_rotation_interval" name="sftr_rotation_interval">
				<option value="daily" <?php selected( $interval, 'daily' ); ?>>Daily</option>
				<option value="weekly" <?php selected( $interval, 'weekly' ); ?>>Weekly</option>
				<option value="monthly" <?php selected( $interval, 'monthly' ); ?>>Monthly</option>
			</select>
			<p class="description">How often the turn should automatically rotate to the next participant.</p>
		</div>
		<?php
	}

	/**
	 * Saves the custom meta data when the post is saved.
	 *
	 * @since 1.0.0
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save_tracker_meta_data( $post_id ) {
		// Check if our nonce is set.
		if ( ! isset( $_POST['sftr_tracker_meta_box_nonce'] ) ) {
			return;
		}
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['sftr_tracker_meta_box_nonce'], 'sftr_tracker_save_meta_box_data' ) ) {
			return;
		}
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// --- Sanitize and Save Participants ---
		if ( isset( $_POST['sftr_participants'] ) ) {
			$participants_raw = sanitize_textarea_field( $_POST['sftr_participants'] );
			update_post_meta( $post_id, '_sftr_participants', $participants_raw );

			// Set the initial turn index to the first person.
			update_post_meta( $post_id, '_sftr_current_turn_index', 0 );
		}

		// --- Sanitize and Save Mode ---
		if ( isset( $_POST['sftr_mode'] ) ) {
			$mode = sanitize_text_field( $_POST['sftr_mode'] );
			update_post_meta( $post_id, '_sftr_mode', $mode );

			// If mode is "points", initialize the points data for all participants.
			if ( 'points' === $mode && ! empty( $participants_raw ) ) {
				$participants_array = array_map( 'trim', explode( ',', $participants_raw ) );
				$points_data = array();
				foreach ( $participants_array as $participant ) {
					if ( ! empty( $participant ) ) {
						// Initialize each participant with 0 points.
						$points_data[ $participant ] = 0;
					}
				}
				update_post_meta( $post_id, '_sftr_points_data', $points_data );
			}
		}

		// --- Sanitize and Save Rotation Interval ---
		if ( isset( $_POST['sftr_rotation_interval'] ) ) {
			$interval = sanitize_text_field( $_POST['sftr_rotation_interval'] );
			update_post_meta( $post_id, '_sftr_rotation_interval', $interval );
		}
	}
}
