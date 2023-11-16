<?php

/**
 *
 * @link              http://example.com
 * @since             1.0.2
 * @package           Atoll Ferry
 *
 * @wordpress-plugin
 * Plugin Name:       Atoll Ferry
 * Plugin URI:        https://github.com/mondrey/atoll-ferry
 * Description:       Maldives transport information plugin
 * Version:           1.0.2
 * Author:            Mohamed Musthafa
 * Author URI:        https://github.com/mondrey/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       atoll-ferry
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
define( 'ATOLL_FERRY_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-atoll-ferry-activator.php
 */
function activate_atoll_ferry() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-atoll-ferry-activator.php';
	Atoll_Ferry_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-atoll-ferry-deactivator.php
 */
function deactivate_atoll_ferry() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-atoll-ferry-deactivator.php';
	Atoll_Ferry_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_atoll_ferry' );
register_deactivation_hook( __FILE__, 'deactivate_atoll_ferry' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-atoll-ferry.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_atoll_ferry() {

	$plugin = new Atoll_Ferry();
	$plugin->run();

}
run_atoll_ferry();
