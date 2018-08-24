<?php

/**
 * The main plugin file definition
 * This file isn't instatiated directly, it acts as a shared parent for other classes
 * @link    https://www.wpcodelabs.com
 * @since   1.0.0
 * @package wpcl_beaver_extender
 */

namespace Wpcl\Be;

class Plugin {

	/**
	 * Plugin Name
	 * @since 1.0.0
	 * @access protected
	 * @var (string) $name : The unique identifier for this plugin
	 */
	protected static $name = 'wpcl_beaver_extender_beta';

	/**
	 * Plugin Version
	 * @since 1.0.0
	 * @access protected
	 * @var (string) $version : The version number of the plugin, used to version scripts / styles
	 */
	protected static $version = '1.0.0';

	/**
	 * Plugin Path
	 * @since 1.0.0
	 * @access protected
	 * @var (string) $path : The path to the plugins location on the server, is inherited by child classes
	 */
	protected static $path;

	/**
	 * Plugin URL
	 * @since 1.0.0
	 * @access protected
	 * @var (string) $url : The URL path to the location on the web, accessible by a browser
	 */
	protected static $url;

	/**
	 * Plugin Slug
	 * @since 1.0.0
	 * @access protected
	 * @var (string) $slug : Basename of the plugin, needed for Wordpress to set transients, and udpates
	 */
	protected static $slug;

	/**
	 * Plugin Options
	 * @since 1.0.0
	 * @access protected
	 * @var (array) $settings : The array that holds plugin options
	 */
	protected $loader;

	/**
	 * Instances
	 * @since 1.0.0
	 * @access protected
	 * @var (array) $instances : Collection of instantiated classes
	 */
	protected static $instances = array();

	/**
	 * Registers our plugin with WordPress.
	 */
	public static function register( $class_name = null ) {
		// Get called class
		$class_name = !is_null( $class_name ) ? $class_name : get_called_class();
		// Instantiate class
		$class = $class_name::get_instance( $class_name );
		// Create API manager
		$class->loader = \Wpcl\Be\Loader::get_instance();
		// Register stuff
		$class->loader->register( $class );
		// Return instance
		return $class;
	}

	/**
	 * Gets an instance of our class.
	 */
	public static function get_instance( $class_name = null ) {
		// Use late static binding to get called class
		$class = !is_null( $class_name ) ? $class_name : get_called_class();
		// Get instance of class
		if( !isset(self::$instances[$class] ) ) {
			self::$instances[$class] = new $class();
		}
		return self::$instances[$class];
	}

	/**
	 * Constructor
	 * @since 1.0.0
	 * @access protected
	 */
	protected function __construct() {
		self::$path = plugin_dir_path( BEAVER_EXTENDER_ROOT );
		self::$url  = plugin_dir_url( BEAVER_EXTENDER_ROOT );
		self::$slug = plugin_basename( BEAVER_EXTENDER_ROOT );
	}

	/**
	 * Helper function to use relative URLs
	 * @since 1.0.0
	 * @access protected
	 */
	public static function url( $url = '' ) {
		return self::$url . ltrim( $url, '/' );
	}

	/**
	 * Helper function to use relative paths
	 * @since 1.0.0
	 * @access protected
	 */
	public static function path( $path = '' ) {
		return self::$path . ltrim( $path, '/' );
	}

	public function burn_baby_burn() {
		/**
		 * Register core plugin modules
		 */
		foreach( array( 'Admin', 'FrontEnd', 'ContentBlock', 'Widgets' ) as $class ) {
			// Append namespace
			$class = '\\Wpcl\\Be\\Classes\\' . $class;
			// Register
			$class::register();
		}
		/**
		 * Add a registration hook for beaver builder modules
		 *
		 * We need to delay registration until after we know all other
		 * plugins are fully loaded, in order to prevent errors
		 */
		add_action( 'init', array( $this, 'register_modules' ) );
	}

	public function register_modules() {

		// We can bail if beaver builder isn't present
		if( !class_exists( 'FLBuilder' ) ) {
			return;
		}

		$modules = array(
			'BEHeading',
			'BEButton',
			'BEGmaps',
			'BESeparator',
			'BEGravityForms',
			'BEBusinessHours',
		);

		foreach( $modules as $slug ) {
			/**
			 * Append namespace
			 */
			$class = "\\Wpcl\\Be\\Modules\\{$slug}";
			/**
			 * Create the module
			 */
			$modules[$slug] = new $class();
			/**
			 * Register the module
			 */
			$modules[$slug]->register_module();
		}

	}

} // end class