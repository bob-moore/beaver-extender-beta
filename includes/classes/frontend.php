<?php

/**
 * The plugin file that defines the front end functionality
 * @link    https://www.wpcodelabs.com
 * @since   1.0.0
 * @package wpcl_beaver_extender
 */

namespace Wpcl\Be\Classes;

use \Wpcl\Be\Classes\Utilities as Utilities;

class FrontEnd extends \Wpcl\Be\Plugin implements \Wpcl\Be\Interfaces\Filter_Hook_Subscriber, \Wpcl\Be\Interfaces\Action_Hook_Subscriber {

	/**
	 * Get the filter hooks this class subscribes to.
	 * @return array
	 */
	public function get_filters() {
		return array(
			array( 'fl_builder_register_settings_form' => array( 'extend_row_settings' , 10, 2 ) ),
			array( 'fl_builder_register_settings_form' => array( 'extend_column_settings' , 10, 2 ) ),
			array( 'fl_builder_register_settings_form' => array( 'extend_module_settings' , 10, 2 ) ),
			array( 'fl_builder_column_attributes' => array( 'extend_column_attributes' , 10, 2 ) ),
			array( 'fl_builder_column_attributes' => array( 'extend_module_animation_atts' , 10, 2 ) ),
			array( 'fl_builder_row_attributes' => array( 'extend_row_attributes' , 10, 2 ) ),
			array( 'fl_builder_module_attributes' => array( 'extend_module_animation_atts' , 10, 2 ) ),
			array( 'beaver_extender_animation_options' => 'extend_animations' ),
		);
	}

	/**
	 * Get the action hooks this class subscribes to.
	 * @return array
	 */
	public function get_actions() {
		return array(
			array( 'wp_enqueue_scripts' => 'enqueue_scripts' ),
			array( 'wp_enqueue_scripts' => 'enqueue_styles' ),
		);
	}

	public function enqueue_scripts() {
		wp_register_script( 'prism-js', self::url( 'assets/js/prism.js' ), array(), '1.15.0', false );

		wp_register_script( 'begmap', self::url( 'assets/js/begmap.js' ), array(), self::$version, false );
	}

	public function enqueue_styles() {
		wp_register_style( 'prism', self::url( sprintf( 'assets/css/prism-%s.css', Utilities::get_settings( 'codeblock_theme', 'default' ) ) ), array( ), '1.15.0', 'all' );

		wp_register_style( sprintf( '%s_public', self::$name ), self::url( 'assets/css/public.css' ), array( ), self::$version, 'all' );

		wp_enqueue_style( sprintf( '%s_public', self::$name ) );
	}

	/**
	 * Extend Row Settings
	 * @param  [array] $form : The settings array for the row settings form
	 * @param  [string] $id  : The id of the form
	 * @return [array]       : The (maybe) modified form
	 */
	public function extend_row_settings( $form, $id ) {
		if( $id === 'row' ) {
			$size_settings = array(
				'type'          => 'select',
				'label'         => __( 'Gutter', 'wpcl_beaver_extender' ),
				'default'       => 'default',
				'options'       => array(
					'default'          => __( 'Default', 'wpcl_beaver_extender' ),
					'collapse'         => __( 'Collapsed', 'wpcl_beaver_extender' ),
				),
			);
			$form['tabs']['style']['sections']['general']['fields']['gutter'] = $size_settings;
		}
		return $form;
	}

	/**
	 * Extend row attributes
	 * @param  [array] $atts : Row attributes
	 * @param  [object] $row : The row object, including settings
	 * @return [array]       : Maybe modified row atts
	 */
	function extend_row_attributes( $atts, $row ) {
		if( isset( $row->settings->gutter ) && $row->settings->gutter === 'collapse' ) {
			$atts['class'][] = $row->settings->gutter;
		}
		return $atts;
	}

	/**
	 * Extend Columns Settings
	 * @param  [array] $form : The settings array for the column settings form
	 * @param  [string] $id  : The id of the form
	 * @return [array]       : The (maybe) modified form
	 */
	public function extend_column_settings( $form, $id ) {
		if( $id === 'col' ) {
			$base_animations = array(
				''              => _x( 'None', 'Animation style.', 'wpcl_beaver_extender' ),
				'fade-in'       => _x( 'Fade In', 'Animation style.', 'wpcl_beaver_extender' ),
				'slide-left'    => _x( 'Slide Left', 'Animation style.', 'wpcl_beaver_extender' ),
				'slide-right'   => _x( 'Slide Right', 'Animation style.', 'wpcl_beaver_extender' ),
				'slide-up'      => _x( 'Slide Up', 'Animation style.', 'wpcl_beaver_extender' ),
				'slide-down'    => _x( 'Slide Down', 'Animation style.', 'wpcl_beaver_extender' ),
			);
			// animation
			$animation_section = array(
				'title'         => __( 'Animation', 'wpcl_beaver_extender' ),
				'fields'        => array(
					'animation'     => array(
						'type'          => 'select',
						'label'         => __( 'Style', 'wpcl_beaver_extender' ),
						'options'       => apply_filters( 'beaver_extender_animation_options', $base_animations ),
						'preview'         => array(
							'type'            => 'none',
						),
					),
					'animation_delay' => array(
						'type'          => 'text',
						'label'         => __( 'Delay', 'wpcl_beaver_extender' ),
						'default'       => '0.0',
						'maxlength'     => '4',
						'size'          => '5',
						'description'   => _x( 'seconds', 'Value unit for form field of time in seconds. Such as: "5 seconds"', 'wpcl_beaver_extender' ),
						'help'          => __( 'The amount of time in seconds before this animation starts.', 'wpcl_beaver_extender' ),
						'preview'         => array(
							'type'            => 'none',
						),
					),
					'animation_speed' => array(
						'type'          => 'text',
						'label'         => __( 'Speed', 'wpcl_beaver_extender' ),
						'default'       => '1.0',
						'maxlength'     => '4',
						'size'          => '5',
						'description'   => _x( 'seconds', 'Value unit for form field of time in seconds. Such as: "5 seconds"', 'wpcl_beaver_extender' ),
						'help'          => __( 'The animation speed', 'wpcl_beaver_extender' ),
						'preview'         => array(
							'type'            => 'none',
						),
					),
				),
			);
			// Add the animation field
			$form['tabs']['advanced']['sections']['animation'] = $animation_section;
		}
		return $form;
	}

	public function extend_column_attributes( $atts, $col ) {
		if( isset( $col->settings->animation ) && !empty( $col->settings->animation ) ) {
			$atts['class'][] = ' fl-animation fl-' . $col->settings->animation;
			$atts['data-animation-delay'][] = $col->settings->animation_delay;
		}
		if( isset( $col->settings->gutter ) && $col->settings->gutter === 'collapse' ) {
			$atts['class'][] = $col->settings->gutter;
		}
		return $atts;
	}

	public function extend_module_settings( $form, $id ) {
		if ( 'module_advanced' === $id ) {
	        $form[ 'sections' ][ 'animation' ][ 'fields' ][ 'animation' ][ 'options' ] = apply_filters( 'beaver_extender_animation_options', $form[ 'sections' ][ 'animation' ][ 'fields' ][ 'animation' ][ 'options' ] );
	        $form[ 'sections' ][ 'animation' ][ 'fields' ]['animation_speed'] = array(
	        	'type'          => 'text',
	        	'label'         => __( 'Speed', 'wpcl_beaver_extender' ),
	        	'default'       => '1.0',
	        	'maxlength'     => '4',
	        	'size'          => '5',
	        	'description'   => _x( 'seconds', 'Value unit for form field of time in seconds. Such as: "5 seconds"', 'wpcl_beaver_extender' ),
	        	'help'          => __( 'The animation speed', 'wpcl_beaver_extender' ),
	        	'preview'         => array(
	        		'type'            => 'none',
	        	),
	        );
	    }
	    return $form;
	}

	public function extend_module_animation_atts( $atts, $module ) {
		if( isset( $module->settings->animation ) && !empty( $module->settings->animation_speed ) && $module->settings->animation_speed !== '1.0' ) {
			$atts['style'][] = sprintf( '-webkit-animation-duration: %1$ss;-moz-animation-duration: %1$ss;animation-duration: %1$ss;', $module->settings->animation_speed );
		}
		return $atts;
	}

	public function extend_animations( $animations ) {
		$new_animations = array(
			'lightSpeedIn' => __( 'Lightspeed', 'wpcl_beaver_extender' ),
			'rotateIn' => __( 'Rotate In', 'wpcl_beaver_extender' ),
		);
		return array_merge( $animations, $new_animations );
	}

} // end class