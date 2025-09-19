<?php
/**
 * Plugin Name:       Strategicli Family Tracking
 * Plugin URI:        https://strategicli.com/family-dashboard
 * Description:       A simple plugin to track turns and responsibilities for common household topics, ensuring fairness and reducing family disputes.
 * Version:           1.0.0
 * Author:            Strategicli
 * Author URI:        https://strategicli.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       strategicli-family-tracking
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define constants for the plugin.
 */
define( 'SFTR_VERSION', '1.0.0' );
define( 'SFTR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SFTR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SFTR_PLUGIN_FILE', __FILE__ );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require SFTR_PLUGIN_DIR . 'includes/class-strategicli-family-tracking.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_strategicli_family_tracking() {

	$plugin = new Strategicli_Family_Tracking();
	$plugin->run();

}
run_strategicli_family_tracking();
