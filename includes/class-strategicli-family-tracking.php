<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://strategicli.com
 * @since      1.0.0
 *
 * @package    Strategicli_Family_Tracking
 * @subpackage Strategicli_Family_Tracking/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Strategicli_Family_Tracking
 * @subpackage Strategicli_Family_Tracking/includes
 * @author     Strategicli <info@strategicli.com>
 */
class Strategicli_Family_Tracking {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Strategicli_Family_Tracking_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->version = defined( 'SFTR_VERSION' ) ? SFTR_VERSION : '1.0.0';
		$this->plugin_name = 'strategicli-family-tracking';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once SFTR_PLUGIN_DIR . 'includes/class-strategicli-family-tracking-loader.php';

		/**
		 * The class responsible for defining the custom post type.
		 */
		require_once SFTR_PLUGIN_DIR . 'admin/class-strategicli-family-tracking-cpt.php';
		
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once SFTR_PLUGIN_DIR . 'admin/class-strategicli-family-tracking-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once SFTR_PLUGIN_DIR . 'public/class-strategicli-family-tracking-public.php';

		/**
		 * The class responsible for handling AJAX requests.
		 */
		require_once SFTR_PLUGIN_DIR . 'includes/class-strategicli-family-tracking-ajax.php';


		$this->loader = new Strategicli_Family_Tracking_Loader();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Strategicli_Family_Tracking_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_cpt = new Strategicli_Family_Tracking_CPT();

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Register the custom post type
		$this->loader->add_action( 'init', $plugin_cpt, 'register_cpt' );

		// Add meta box and save functionality for the CPT
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_tracker_meta_box' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_tracker_meta_data' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Strategicli_Family_Tracking_Public( $this->get_plugin_name(), $this->get_version() );
		$plugin_ajax = new Strategicli_Family_Tracking_Ajax();

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Register the shortcode
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcode' );

		// Register AJAX action hooks
	$this->loader->add_action( 'wp_ajax_sftr_advance_turn', $plugin_ajax, 'advance_turn' );
	$this->loader->add_action( 'wp_ajax_nopriv_sftr_advance_turn', $plugin_ajax, 'advance_turn' );

	$this->loader->add_action( 'wp_ajax_sftr_update_points', $plugin_ajax, 'update_points' );
	$this->loader->add_action( 'wp_ajax_nopriv_sftr_update_points', $plugin_ajax, 'update_points' );

	$this->loader->add_action( 'wp_ajax_sftr_random_pick', $plugin_ajax, 'random_pick' );
	$this->loader->add_action( 'wp_ajax_nopriv_sftr_random_pick', $plugin_ajax, 'random_pick' );

	// Register AJAX actions for manual overrides
	$this->loader->add_action( 'wp_ajax_sftr_override_turn', $plugin_ajax, 'override_turn' );
	$this->loader->add_action( 'wp_ajax_nopriv_sftr_override_turn', $plugin_ajax, 'override_turn' );

	$this->loader->add_action( 'wp_ajax_sftr_override_points', $plugin_ajax, 'override_points' );
	$this->loader->add_action( 'wp_ajax_nopriv_sftr_override_points', $plugin_ajax, 'override_points' );

	$this->loader->add_action( 'wp_ajax_sftr_override_random', $plugin_ajax, 'override_random' );
	$this->loader->add_action( 'wp_ajax_nopriv_sftr_override_random', $plugin_ajax, 'override_random' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Strategicli_Family_Tracking_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
