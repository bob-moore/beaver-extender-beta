<?php

/**
 * The plugin file that controls the admin functions
 * @link    https://www.wpcodelabs.com
 * @since   1.0.0
 * @package wpcl_beaver_extender
 */

namespace Wpcl\Be\Classes;

use \Wpcl\Be\Classes\Utilities as Utilities;

class Admin extends \Wpcl\Be\Plugin implements \Wpcl\Be\Interfaces\Action_Hook_Subscriber, \Wpcl\Be\Interfaces\Filter_Hook_Subscriber {

	/**
	 * Get the action hooks this class subscribes to.
	 * @return array
	 */
	public function get_actions() {
		// Return our custom actions
		return array(
			array( 'fl_builder_admin_settings_render_forms' => 'render_settings_form' ),
			array( 'admin_init' => 'register_settings' ),
		);
	}

	/**
	 * Get the filter hooks this class subscribes to.
	 * @return array
	 */
	public function get_filters() {
		return array(
			array( 'fl_builder_admin_settings_nav_items' => 'add_bb_settings_nav' ),
		);
	}

	public function add_bb_settings_nav( $nav_items ) {
		$nav_items[ self::$name ] = array(
			'title' 	=> __( 'Beaver Extender', 'wpcl_beaver_extender' ),
			'show'		=> true,
			'priority'	=> 700,
		);
		return $nav_items;
	}

	public function register_settings() {
		$fields = array(
			'google_maps_api' => array(
				'type'  => 'text',
				'label' => __( 'Google Maps API Key', 'wpcl_beaver_extender' ),
			),
			'codeblock_theme' => array(
				'type'  => 'select',
				'label' => __( 'Code Block CSS Theme', 'wpcl_beaver_extender' ),
				'options' => array(
					'default' => __( 'Default', 'wpcl_beaver_extender' ),
					'dark' => __( 'Dark', 'wpcl_beaver_extender' ),
					'funky' => __( 'Funky', 'wpcl_beaver_extender' ),
					'okaidia' => __( 'Okaidia', 'wpcl_beaver_extender' ),
					'twilight' => __( 'Twilight', 'wpcl_beaver_extender' ),
					'coy' => __( 'Coy', 'wpcl_beaver_extender' ),
					'solorized-light' => __( 'solorized light', 'wpcl_beaver_extender' ),
					'tomorrow-night' => __( 'Tomorrow Night', 'wpcl_beaver_extender' ),
				),
			),

		);
		// Add Setting
	    register_setting( self::$name, self::$name );
	    // Add Section
	    add_settings_section( 'general', __( 'Settings', self::$name ), null, self::$name );
	    // Add Fields
	    foreach( $fields as $key => $field ) {
	    	add_settings_field(
	    		$key,
	    		$field['label'],
	    		array( $this, 'display_settings_field' ),
	    		self::$name, 'general',
	    		array(
	    			'key'   => $key,
	    			'field' => $field,
	    			'value' => Utilities::get_settings( $key, '' ),
	    		)
	    	);
	    }

	}

	public function display_settings_field( $args ) {

		/**
		 * Select field output
		 */

		if( $args['field']['type'] === 'select' ) {
			Utilities::markup( array(
				'open'    => '<select %s/>',
				'context' => $args['key'],
				'params'  => array(
					'class' => 'widefat',
					'name'  => sprintf( '%s[%s]', self::$name, $args['key'] ),
					'id'    => sprintf( '%s[%s]', self::$name, $args['key'] ),
				),
			) );

			foreach( $args['field']['options'] as $value => $label ) {
				Utilities::markup( array(
					'open'    => '<option %s/>',
					'close'   => '</option>',
					'context' => 'opt',
					'content' => $label,
					'params'  => array(
						'value'  => $value,
						'selected' => selected( $value, Utilities::get_settings( $args['key'], '' ), false ),
					),
				) );
			}

			Utilities::markup( array(
				'close'    => '</select>',
				'context' => $args['key'],
			) );
		}

		else {
			Utilities::markup( array(
				'open'    => '<input %s/>',
				'close'   => '',
				'context' => $args['key'],
				'params'  => array(
					'class' => 'widefat',
					'name'  => sprintf( '%s[%s]', self::$name, $args['key'] ),
					'id'    => sprintf( '%s[%s]', self::$name, $args['key'] ),
					'value' => $args['value'],
				),
			) );
		}

	}

	public function render_settings_form( $form ) {

		/**
		 * Open the wrapper div
		 */
		Utilities::markup( array(
			'open' => '<div %s>',
			'context' => 'fl-settings-form',
			'params'  => array(
				'id' => sprintf( 'fl-%s-form', self::$name ),
			)
		) );

		/**
		 * Open the setting form
		 */
		Utilities::markup( array(
			'open' => '<form %s>',
			'context' => 'be-settings',
			'params' => array(
				'method' => 'post',
				'action' => 'options.php'
			),
		) );

		wp_nonce_field( 'update-options' );

		settings_fields( self::$name );

		do_settings_sections( self::$name );

		submit_button( __( 'Save Beaver Extender Settings', 'wpcl_beaver_extender', 'primary', 'update' ) );

		/**
		 * Close the settings form
		 */
		Utilities::markup( array(
			'close' => '</form>',
			'context' => 'be-settings',
		) );

		/**
		 * Close the wrapper
		 */
		Utilities::markup( array(
			'close' => '</div>',
			'context' => 'fl-settings-form',
		) );
	}

} // end class