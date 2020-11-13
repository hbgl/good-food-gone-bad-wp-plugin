<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/hbgl
 * @since             1.0.0
 * @package           Gfgb
 *
 * @wordpress-plugin
 * Plugin Name:       WpGoodFoodGoneBad
 * Plugin URI:        https://github.com/hbgl/good-food-gone-bad-wp-plugin
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            hbgl
 * Author URI:        https://github.com/hbgl
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gfgb
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'GFGB_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-gfgb-activator.php
 */
function activate_gfgb() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gfgb-activator.php';
	Gfgb_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-gfgb-deactivator.php
 */
function deactivate_gfgb() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-gfgb-deactivator.php';
	Gfgb_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_gfgb' );
register_deactivation_hook( __FILE__, 'deactivate_gfgb' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-gfgb.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_gfgb() {

	$plugin = new Gfgb();
	$plugin->run();

}
run_gfgb();
