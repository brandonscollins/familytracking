<?php

/**
 * The file that defines the custom post type for the plugin.
 *
 * @link       https://strategicli.com
 * @since      1.0.0
 *
 * @package    Strategicli_Family_Tracking
 * @subpackage Strategicli_Family_Tracking/admin
 */

/**
 * The custom post type class.
 *
 * This class is responsible for registering the "Tracker" custom post type.
 *
 * @since      1.0.0
 * @package    Strategicli_Family_Tracking
 * @subpackage Strategicli_Family_Tracking/admin
 * @author     Strategicli <info@strategicli.com>
 */
class Strategicli_Family_Tracking_CPT {

	/**
	 * The unique identifier for the custom post type.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $post_type    The string used to identify the custom post type.
	 */
	public $post_type = 'sftr_tracker';

	/**
	 * Register the custom post type.
	 *
	 * @since    1.0.0
	 */
	public function register_cpt() {

		$labels = array(
			'name'                  => _x( 'Trackers', 'Post Type General Name', 'strategicli-family-tracking' ),
			'singular_name'         => _x( 'Tracker', 'Post Type Singular Name', 'strategicli-family-tracking' ),
			'menu_name'             => __( 'Trackers', 'strategicli-family-tracking' ),
			'name_admin_bar'        => __( 'Tracker', 'strategicli-family-tracking' ),
			'archives'              => __( 'Tracker Archives', 'strategicli-family-tracking' ),
			'attributes'            => __( 'Tracker Attributes', 'strategicli-family-tracking' ),
			'parent_item_colon'     => __( 'Parent Tracker:', 'strategicli-family-tracking' ),
			'all_items'             => __( 'All Trackers', 'strategicli-family-tracking' ),
			'add_new_item'          => __( 'Add New Tracker', 'strategicli-family-tracking' ),
			'add_new'               => __( 'Add New', 'strategicli-family-tracking' ),
			'new_item'              => __( 'New Tracker', 'strategicli-family-tracking' ),
			'edit_item'             => __( 'Edit Tracker', 'strategicli-family-tracking' ),
			'update_item'           => __( 'Update Tracker', 'strategicli-family-tracking' ),
			'view_item'             => __( 'View Tracker', 'strategicli-family-tracking' ),
			'view_items'            => __( 'View Trackers', 'strategicli-family-tracking' ),
			'search_items'          => __( 'Search Tracker', 'strategicli-family-tracking' ),
			'not_found'             => __( 'Not found', 'strategicli-family-tracking' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'strategicli-family-tracking' ),
			'featured_image'        => __( 'Featured Image', 'strategicli-family-tracking' ),
			'set_featured_image'    => __( 'Set featured image', 'strategicli-family-tracking' ),
			'remove_featured_image' => __( 'Remove featured image', 'strategicli-family-tracking' ),
			'use_featured_image'    => __( 'Use as featured image', 'strategicli-family-tracking' ),
			'insert_into_item'      => __( 'Insert into tracker', 'strategicli-family-tracking' ),
			'uploaded_to_this_item' => __( 'Uploaded to this tracker', 'strategicli-family-tracking' ),
			'items_list'            => __( 'Trackers list', 'strategicli-family-tracking' ),
			'items_list_navigation' => __( 'Trackers list navigation', 'strategicli-family-tracking' ),
			'filter_items_list'     => __( 'Filter trackers list', 'strategicli-family-tracking' ),
		);
		$args   = array(
			'label'                 => __( 'Tracker', 'strategicli-family-tracking' ),
			'description'           => __( 'A post type for tracking family turns and responsibilities.', 'strategicli-family-tracking' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor' ),
			'hierarchical'          => false,
			'public'                => false, // Not publicly queryable, only for admin and shortcode use.
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 20,
			'menu_icon'             => 'dashicons-randomize',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'capability_type'       => 'post',
			'show_in_rest'          => true, // Enables the block editor.
		);
		register_post_type( $this->post_type, $args );

	}

}
