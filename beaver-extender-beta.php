<?php
/**
 * The plugin bootstrap file
 * This file is read by WordPress to generate the plugin information in the plugin admin area.
 * This file also defines plugin parameters, registers the activation and deactivation functions, and defines a function that starts the plugin.
 * @link    https://bitbucket.org/midwestdigitalmarketing/cornerstone/
 * @since   1.0.0
 * @package wpcl_beaver_extender
 *
 * @wordpress-plugin
 * Plugin Name: Beaver Extender Beta
 * Plugin URI:  https://www.wpcodelabs.com/
 * Description: Extend the Beaver Builder page builder with additional modules
 * Version:     1.0.0
 * Author:      WP Code Labs
 * Author URI:  https://www.wpcodelabs.com/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wpcl_beaver_extender
 */

define( 'BEAVER_EXTENDER_ROOT', __FILE__ );

// If this file is called directly, abort
if ( !defined( 'WPINC' ) ) {
    die( 'Bugger Off Script Kiddies!' );
}

/**
 * Class autoloader
 * Do some error checking and string manipulation to accomodate our namespace
 * and autoload the class based on path
 * @since 1.0.0
 * @see http://php.net/manual/en/function.spl-autoload-register.php
 * @param (string) $className : fully qualified classname to load
 */
function wpcl_beaver_extender_autoload_register( $className ) {
	// Reject it if not a string
	if( !is_string( $className ) ) {
		return false;
	}
	// Check and make damned sure we're only loading things from this namespace
	if( strpos( $className, 'Wpcl\Be' ) === false ) {
		return false;
	}
	// Replace backslashes
	$className = strtolower( str_replace( '\\', '/', $className ) );
	// Ensure there is no slash at the beginning of the classname
	$className = ltrim( $className, '/' );
	// Replace some known constants
	$className = str_ireplace( 'Wpcl/Be/', '', $className );
	// Append full path to class
	$path  = sprintf( '%1$sincludes/%2$s.php', plugin_dir_path( BEAVER_EXTENDER_ROOT ), $className );
	// include the class...
	if( file_exists( $path ) ) {
		include_once( $path );
	}
}

/**
 * Code to run during plugin activation
 */
function wpcl_beaver_extender_activate() {
	\Wpcl\Be\Activator::activate();
}

/**
 * Kick off the plugin
 * Check PHP version and make sure our other funcitons will be supported
 * Register autoloader function
 * Register activation & deactivation hooks
 * Create an install of our controller
 * Finally, Burn Baby Burn...
 */
function wpcl_beaver_extender_run() {
	// If version is less than minimum, register notice
	if( version_compare( '5.3.0', phpversion(), '>=' ) ) {
		// Deactivate plugin
		deactivate_plugins( plugin_basename( __FILE__ ) );
		// Print message to user
		wp_die( 'Irks! This plugin requires minimum PHP v5.3.0 to run. Please update your version of PHP.' );
	}
	// Register Autoloader
	spl_autoload_register( 'wpcl_beaver_extender_autoload_register' );
	// Add activation hook
	register_activation_hook( BEAVER_EXTENDER_ROOT, 'wpcl_beaver_extender_activate' );
	// Instantiate our plugin
	$plugin = \Wpcl\Be\Plugin::get_instance();
	// Run our plugin
	$plugin->burn_baby_burn();
}
wpcl_beaver_extender_run();