<?php

namespace Wpcl\Be\Modules;

use \Wpcl\Be\Classes\Utilities as Utilities;

/**
 * @class FLSeparatorModule
 */
class BEGravityForms extends \FLBuilderModule implements \Wpcl\Be\Interfaces\Action_Hook_Subscriber {

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
			'name'          	=> __( 'Gravity Form', 'wpcl_beaver_extender' ),
			'description'   	=> __( 'Place a Gravity Forms form', 'wpcl_beaver_extender' ),
			'category'      	=> __( 'Beaver Extender', 'wpcl_beaver_extender' ),
			'editor_export' 	=> true,
			'partial_refresh'	=> true,
			'enabled' => class_exists( 'RGFormsModel' ), // disable if gravity forms are not enabled
		));
	}

	/**
	 * Get the actions hooks this class subscribes to.
	 * @return array
	 */
	public function get_actions() {
		return array(
			array( "beaver_extender_frontend_{$this->hook_name}" => array( 'do_frontend' , 10, 3 ) ),
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
		/**
		 * Make sure gravity forms is installed and enabled
		 */
		if( !class_exists( 'GFAPI' ) ) {
			return;
		}
		var_dump("gform_submit_button_{$settings->form_id}");
		add_filter( "gform_submit_button_{$settings->form_id}", array( $this, 'debug_gf_filters' ), 10, 2 );
		/**
		 * If our form exists and is active
		 */
		if( \GFAPI::get_form( $settings->form_id ) ) {
			gravity_form(
				$settings->form_id,
				filter_var( $settings->display_title, FILTER_VALIDATE_BOOLEAN ),
				filter_var( $settings->display_description, FILTER_VALIDATE_BOOLEAN ),
				false,
				null,
				filter_var( $settings->enable_ajax, FILTER_VALIDATE_BOOLEAN ),
				$settings->tab_index,
				true
			);
		}
		/**
		 * Else default message
		 */
		else {
			echo 'Choose a form to display';
		}

	}

	public function debug_gf_filters( $button, $form ) {
		var_dump( $button );
		return $button;
	}

	/**
	 * Get all of the gravity forms created on the site
	 *
	 * @return [array] Array of form ID's and Titles, for use in select field
	 */
	public function get_forms() {

		$options = array();

		if( class_exists('RGFormsModel') ) {

			$forms = \RGFormsModel::get_forms();

			foreach( $forms as $form ) {
				$options[$form->id] = $form->title;
			}
		}

		return $options;
	}

	/**
	 * Register the module and its form settings.
	 */
	public function register_module() {
		\FLBuilder::register_module( __CLASS__, array(
			'general'       => array( // Tab
				'title'         => __( 'General', 'wpcl_beaver_extender' ), // Tab title
				'sections'      => array( // Tab Sections
					'general'       => array( // Section
						'title'         => '', // Section Title
						'fields'        => array( // Section Fields
							'form_id'        => array(
								'type'          => 'select',
								'label'         => __( 'Form', 'wpcl_beaver_extender' ),
								'default'       => null,
								'options'       => $this->get_forms(),
							),
							'display_title'         => array(
								'type'          => 'select',
								'label'         => __( 'Form Title', 'wpcl_beaver_extender' ),
								'default'       => 'true',
								'options'       => array(
									'true'  => __( 'Display', 'wpcl_beaver_extender' ),
									'false' => __( 'Hide', 'wpcl_beaver_extender' ),
								),
							),
							'display_description'         => array(
								'type'          => 'select',
								'label'         => __( 'Form Description', 'wpcl_beaver_extender' ),
								'default'       => 'true',
								'options'       => array(
									'true'  => __( 'Display', 'wpcl_beaver_extender' ),
									'false' => __( 'Hide', 'wpcl_beaver_extender' ),
								),
							),
							'enable_ajax'         => array(
								'type'          => 'select',
								'label'         => __( 'Enable Ajax', 'wpcl_beaver_extender' ),
								'default'       => 'true',
								'options'       => array(
									'true'  => __( 'Enable', 'wpcl_beaver_extender' ),
									'false' => __( 'Disable', 'wpcl_beaver_extender' ),
								),
							),
							'tab_index'    => array(
								'type'          => 'text',
								'label'         => __( 'Tab Index', 'wpcl_beaver_extender' ),
								'default'       => '1',
								'maxlength'     => '5',
								'size'          => '5',
							),
						),
					),
				),
			),
		));
	}
}
