<?php
/**
 * API_Manager handles registering actions and hooks with the
 * WordPress Plugin API.
 */

namespace Wpcl\Be;

final class Loader {

	/**
	 * Instances
	 * @since 1.0.0
	 * @access protected
	 * @var (array) $instances : Collection of instantiated classes
	 */
	protected static $instance = null;

	/**
	 * Gets an instance of our class.
	 */
	public static function get_instance( $caller = null ) {
		/**
		 * Check if in instance exists
		 * Create one if not
		 */
		if( self::$instance === null ) {
			self::$instance = new self();
		}
		/**
		 * Register the caller
		 */
		self::$instance->register( $caller );
		/**
		 * Return the intantiated instance
		 */
		return self::$instance;
	}

	/**
	 * Constructor
	 * Though shall not construct that which cannot be constructed
	 * @param  (object|string|null) $caller : instance / name of class that called this
	 * @access private
	 */
	private function __construct() {

	}

	/**
	 * Registers an object with the WordPress Plugin API.
	 * @param mixed $object
	 */
	public function register( $object ) {
		// Register Actions
		if ( $object instanceof \Wpcl\Be\Interfaces\Action_Hook_Subscriber ) {
			$this->register_actions( $object );
		}
		// Register Filters
		if ( $object instanceof \Wpcl\Be\Interfaces\Filter_Hook_Subscriber ) {
			$this->register_filters( $object );
		}
		// Register Shortcodes
		if ( $object instanceof \Wpcl\Be\Interfaces\Shortcode_Hook_Subscriber ) {
			$this->register_shortcodes( $object );
		}
	}

	/**
	 * Register an object with a specific action hook.
	 * @param Action_Hook_Subscriber $object
	 * @param string $name
	 * @param mixed $parameters
	 */
	private function register_action( \Wpcl\Be\Interfaces\Action_Hook_Subscriber $object, $name, $parameters ) {
		// For string params
		if( is_string( $parameters ) ) {
			// If a class method
			if( method_exists( $object, $parameters ) ) {
				add_action( $name, array( $object, $parameters ) );
			}
			// Else if a standard wordpress function
			else if( function_exists( $parameters ) ) {
				add_action( $name, $parameters );
			}
		}
		// For array of params (name, priority, args)
		elseif( is_array( $parameters ) && isset( $parameters[0] ) ) {
			// If a class method
			if( method_exists( $object, $parameters[0] ) ) {
				add_action( $name, array( $object, $parameters[0] ), isset( $parameters[1] ) ? $parameters[1] : 10, isset( $parameters[2] ) ? $parameters[2] : 1 );
			}
			// Else if a standard wordpress function
			else if( function_exists( $parameters[0] ) ) {
				add_action( $name, $parameters[0], isset( $parameters[1] ) ? $parameters[1] : 10, isset( $parameters[2] ) ? $parameters[2] : 1 );
			}
		}
	}

	/**
	 * Regiters an object with all its action hooks.
	 *
	 * @param Action_Hook_SubscriberInterface $object
	 */
	private function register_actions( \Wpcl\Be\Interfaces\Action_Hook_Subscriber $object ) {
		foreach( $object->get_actions() as $action ) {
			$this->register_action( $object, key( $action ), current( $action ) );
		}
	}

	/**
	 * Register an object with a specific filter hook.
	 *
	 * @param Filter_Hook_SubscriberInterface $object
	 * @param string                          $name
	 * @param mixed                           $parameters
	 */
	private function register_filter( \Wpcl\Be\Interfaces\Filter_Hook_Subscriber $object, $name, $parameters ) {

		// For string params
		if( is_string( $parameters ) ) {
			// If a class method
			if( method_exists( $object, $parameters ) ) {
				add_filter( $name, array( $object, $parameters ) );
			}
			// Else if a standard wordpress function
			else if( function_exists( $parameters ) ) {
				add_filter( $name, $parameters );
			}
		}
		// For array of params (name, priority, args)
		elseif( is_array( $parameters ) && isset( $parameters[0] ) ) {
			// If a class method
			if( method_exists( $object, $parameters[0] ) ) {
				add_filter( $name, array( $object, $parameters[0] ), isset( $parameters[1] ) ? $parameters[1] : 10, isset( $parameters[2] ) ? $parameters[2] : 1 );
			}
			// Else if a standard wordpress function
			else if( function_exists( $parameters[0] ) ) {
				add_filter( $name, $parameters[0], isset( $parameters[1] ) ? $parameters[1] : 10, isset( $parameters[2] ) ? $parameters[2] : 1 );
			}
		}
	}

	/**
	 * Regiters an object with all its filter hooks.
	 *
	 * @param Filter_Hook_SubscriberInterface $object
	 */
	private function register_filters( \Wpcl\Be\Interfaces\Filter_Hook_Subscriber $object) {

		foreach( $object->get_filters() as $filter ) {
			$this->register_filter( $object, key( $filter ), current( $filter ) );
		}
	}

	/**
	 * Register an object with a specific shortcode hook.
	 *
	 * @param Shortcode_Hook_SubscriberInterface $object
	 * @param string                          $name
	 * @param mixed                           $parameters
	 */
	private function register_shortcode( \Wpcl\Be\Interfaces\Shortcode_Hook_Subscriber $object, $name, $parameters ) {
		if( is_string( $parameters ) ) {
			// If a class method
			if( method_exists( $object, $parameters ) ) {
				add_shortcode( $name, array( $object, $parameters ) );
			}
			// Else if a standard wordpress function
			else if( function_exists( $parameters ) ) {
				add_shortcode( $name, $parameters );
			}
		}
	}

	/**
	 * Regiters an object with all its shortcode hooks.
	 *
	 * @param Shortcode_Hook_SubscriberInterface $object
	 */
	private function register_shortcodes( \Wpcl\Be\Interfaces\Shortcode_Hook_Subscriber $object) {
		foreach( $object->get_shortcodes() as $shortcode ) {
			$this->register_shortcode( $object, key( $shortcode ), current( $shortcode ) );
		}
	}
}