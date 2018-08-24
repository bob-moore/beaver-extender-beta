<?php

namespace Wpcl\Be\Modules;

use \Wpcl\Be\Classes\Utilities as Utilities;

class BEBusinessHours extends \FLBuilderModule implements \Wpcl\Be\Interfaces\Action_Hook_Subscriber, \Wpcl\Be\Interfaces\Filter_Hook_Subscriber {

	/**
	 * API Manager / Loader to interact with the other parts of the plugin
	 * @since 1.0.0
	 * @var (object) $api : The instance of the api manager class
	 */
	protected $api;

	/**
	 * Hook Name
	 * @since 1.0.0
	 * @var [string] : hook name, same as the slug created later by FLBuilderModule
	 */
	protected $hook_name;

	/**
	 * @method __construct
	 */
	public function __construct() {

		/**
		 * Set the hook name. Same as the slug, but created here so we can access it
		 */
		$this->hook_name = basename( __FILE__, '.php' );

		/**
		 * Get the API instance to interact with the other parts of our plugin
		 */
		$this->api = \Wpcl\Be\Loader::get_instance( $this );

		/**
		 * Construct our parent class (FLBuilderModule);
		 */
		parent::__construct( array(
			'name'          	=> __( 'Business Hours', 'wpcl_beaver_extender' ),
			'description'   	=> __( 'A simple table to display business hours', 'wpcl_beaver_extender' ),
			'category'      	=> __( 'Beaver Extender', 'wpcl_beaver_extender' ),
			'editor_export' 	=> true,
			'partial_refresh'	=> true,
		));
	}

	/**
	 * Get the action hooks this class subscribes to.
	 * @return array
	 */
	public function get_actions() {
		return array(
			array( "beaver_extender_frontend_{$this->hook_name}" => array( 'do_frontend' , 10, 3 ) ),
			array( "beaver_extender_css_{$this->hook_name}" => array( 'do_css' , 10, 3 ) ),
			array( "beaver_extender_js_{$this->hook_name}" => array( 'do_js' , 10, 3 ) ),
		);
	}

	/**
	 * Get the filter hooks this class subscribes to.
	 * @return array
	 */
	public function get_filters() {
		return array(

		);
	}

	/**
	 * Organize the front end output
	 * @param  [object] $module  : The instance of this module
	 * @param  [array] $settings : The array of settings for this instance
	 * @param  [string] $id : the unique ID of the module
	 */
	public function do_frontend( $module, $settings, $id ) {
		// Bail if it's not this specific instance
		if( $module !== $this || !is_object( $settings ) ) {
			return;
		}

		echo '<div class="be-business-hours-wrapper">';

		printf( '<h4 class="be-business-hours-heading">%s</h4>', $settings->heading );

		echo '<table class="be-business-hours"><tbody>';

		foreach( $settings->days as $day ) {
			echo '<tr>';

				printf( '<th class="day">%s</th>', $day->heading );

				printf( '<td class="time">%s</td>', $day->time );

			echo '</tr>';
		}

		echo '<tbody></table>';

		if( !empty( $settings->content ) ) {
			printf( '<div class="be-business-hours-additional-content">%s</div>', apply_filters( 'the_content', $settings->content ) );
		}

		echo '</div>';
	}

	/**
	 * Organize the css output
	 * @param  [object] $module  : The instance of this module
	 * @param  [array] $settings : The array of settings for this instance
	 * @param  [string] $id : the unique ID of the module
	 */
	public function do_css( $module, $settings, $id ) {
		/**
		 * Bail if not this instance
		 */
		if( $module !== $this || !is_object( $settings ) ) {
			return;
		}
	}

	/**
	 * Organize the js output
	 * @param  [object] $module  : The instance of this module
	 * @param  [array] $settings : The array of settings for this instance
	 * @param  [string] $id : the unique ID of the module
	 */
	public function do_js( $module, $settings, $id ) {
		/**
		 * Bail if not this instance
		 */
		if( $module !== $this || !is_object( $settings ) ) {
			return;
		}
	}

	/**
	 * Register the module and its form settings.
	 */
	public function register_module() {
		/**
		 * Register the module and its form settings.
		 */
		\FLBuilder::register_module( __CLASS__, array(
			'general'       => array(
				'title'         => __( 'General', 'wpcl_beaver_extender' ),
				'sections'      => array(
					'general'       => array(
						'title'         => '',
						'fields'        => array(
							'heading'       => array(
								'type'          => 'text',
								'label'         => __( 'Heading', 'wpcl_beaver_extender' ),
								'preview'       => array(
									'type'            => 'refresh',
								),
							),
							'days'         => array(
								'type'          => 'form',
								'label'         => __( 'Days', 'wpcl_beaver_extender' ),
								'form'          => 'be_business_hours_form', // ID from registered form below
								'preview_text'  => 'heading', // Name of a field to use for the preview text
								'multiple'      => true,
							),
							'content'       => array(
								'type'          => 'editor',
								'label'         => __( 'Additional Content', 'wpcl_beaver_extender' ),
								'preview'       => array(
									'type'            => 'refresh',
								),
							),
						),
					),
				),
			),
		));
		/**
		 * Register a settings form to use in the "form" field type above.
		 */
		\FLBuilder::register_settings_form( 'be_business_hours_form', array(
			'title' => __( 'Hours', 'wpcl_beaver_extender' ),
			'tabs'  => array(
				'general'       => array( // Tab
					'title'         => __( 'General', 'wpcl_beaver_extender' ), // Tab title
					'sections'      => array( // Tab Sections
						'general'       => array( // Section
							'title'         => '', // Section Title
							'fields'        => array( // Section Fields
								'heading'       => array(
									'type'          => 'text',
									'label'         => __( 'Heading', 'wpcl_beaver_extender' ),
									'preview'       => array(
										'type'            => 'refresh',
									),
								),
								'time'       => array(
									'type'          => 'text',
									'label'         => __( 'Time', 'wpcl_beaver_extender' ),
									'preview'       => array(
										'type'            => 'refresh',
									),
								),
							),
						),
					),
				),
			),
		));
	}
}