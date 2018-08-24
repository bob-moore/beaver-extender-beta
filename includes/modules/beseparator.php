<?php

namespace Wpcl\Be\Modules;

use \Wpcl\Be\Classes\Utilities as Utilities;

class BESeparator extends \FLBuilderModule implements \Wpcl\Be\Interfaces\Action_Hook_Subscriber {

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
			'name'          	=> __( 'Separator', 'wpcl_beaver_extender' ),
			'description'   	=> __( 'A divider line to separate content.', 'wpcl_beaver_extender' ),
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
		Utilities::markup( array(
			'open'  => '<hr %s/>',
			'context' => "be-separator",
			'instance' => $module,
		) );
	}

	/**
	 * Organize the front end output
	 * @param  [object] $module  : The instance of this module
	 * @param  [array] $settings : The array of settings for this instance
	 * @param  [string] $id : the unique ID of the module
	 */
	public function do_css( $module, $settings, $id ) {
		// Bail if it's not this specific instance
		if( $module !== $this || !is_object( $settings ) ) {
			return;
		}
		/**
		 * Base styles
		 */
		$styles = array(
			'border-top-width'      => !empty( $settings->height )      ? "{$settings->height}px"       : '',
			'opacity'        => !empty( $settings->opacity )    ? $settings->opacity / 100     : '',
			'border-top-color'  => !empty( $settings->color ) ? "#{$settings->color}"     : '',
			'border-top-style'  => !empty( $settings->color ) ? $settings->style : '',
		);

		if( $settings->width === 'custom' ) {
			$styles = array_merge( $styles, array(
				'width' => !empty( $settings->custom_width ) ? "{$settings->custom_width}%" : '',
				'max-width' => '100%',
			) );
			switch( $settings->align ) {
				case 'center':
					$styles = array_merge( $styles, array(
						'margin-left'  => 'auto',
						'margin-right' => 'auto',
					) );
					break;
				case 'left':
					$styles = array_merge( $styles, array(
						'margin-left'  => 0,
						'margin-right' => 0,
					) );
					break;
				case 'right':
					$styles = array_merge( $styles, array(
						'margin-left'  => 0,
						'margin-right' => 'auto',
					) );
					break;
				default:
					// Do nothing...
					break;
			}
		}

		echo Utilities::be_css( array(
			".fl-node-{$id} .fl-module-content .be-separator"  => $styles,
		) );
	}

	/**
	 * Organize the front end output
	 * @param  [object] $module  : The instance of this module
	 * @param  [array] $settings : The array of settings for this instance
	 * @param  [string] $id : the unique ID of the module
	 */
	public function do_js( $module, $settings, $id ) {

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
							'color'         => array(
								'type'          => 'color',
								'label'         => __( 'Color', 'wpcl_beaver_extender' ),
								'preview'       => array(
									'type'          => 'css',
									'selector'      => '.be-separator',
									'property'      => 'border-top-color',
								),
							),
							'opacity'    => array(
								'type'          => 'text',
								'label'         => __( 'Opacity', 'wpcl_beaver_extender' ),
								'default'       => '100',
								'description'   => '%',
								'maxlength'     => '3',
								'size'          => '5',
								'preview'       => array(
									'type'          => 'css',
									'selector'      => '.be-separator',
									'property'      => 'opacity',
									'unit'          => '%',
								),
							),
							'height'        => array(
								'type'          => 'text',
								'label'         => __( 'Height', 'wpcl_beaver_extender' ),
								'default'       => '1',
								'maxlength'     => '2',
								'size'          => '3',
								'description'   => 'px',
								'sanitize'		=> 'absint',
								'preview'       => array(
									'type'          => 'css',
									'selector'      => '.be-separator',
									'property'      => 'border-top-width',
									'unit'          => 'px',
								),
							),
							'width'        => array(
								'type'          => 'select',
								'label'         => __( 'Width', 'wpcl_beaver_extender' ),
								'default'       => 'full',
								'options'       => array(
									'full'          => __( 'Full Width', 'wpcl_beaver_extender' ),
									'custom'        => __( 'Custom', 'wpcl_beaver_extender' ),
								),
								'toggle'        => array(
									'full'          => array(),
									'custom'        => array(
										'fields'        => array( 'align', 'custom_width' ),
									),
								),
							),
							'custom_width'  => array(
								'type'          => 'text',
								'label'         => __( 'Custom Width', 'wpcl_beaver_extender' ),
								'default'       => '10',
								'maxlength'     => '3',
								'size'          => '4',
								'description'   => '%',
							),
							'align'         => array(
								'type'          => 'select',
								'label'         => __( 'Align', 'wpcl_beaver_extender' ),
								'default'       => 'center',
								'options'       => array(
									'center'      => _x( 'Center', 'Alignment.', 'wpcl_beaver_extender' ),
									'left'        => _x( 'Left', 'Alignment.', 'wpcl_beaver_extender' ),
									'right'       => _x( 'Right', 'Alignment.', 'wpcl_beaver_extender' ),
								),
							),
							'style'         => array(
								'type'          => 'select',
								'label'         => __( 'Style', 'wpcl_beaver_extender' ),
								'default'       => 'solid',
								'options'       => array(
									'solid'         => _x( 'Solid', 'Border type.', 'wpcl_beaver_extender' ),
									'dashed'        => _x( 'Dashed', 'Border type.', 'wpcl_beaver_extender' ),
									'dotted'        => _x( 'Dotted', 'Border type.', 'wpcl_beaver_extender' ),
									'double'        => _x( 'Double', 'Border type.', 'wpcl_beaver_extender' ),
								),
								'preview'       => array(
									'type'          => 'css',
									'selector'      => '.be-separator',
									'property'      => 'border-top-style',
								),
								'help'          => __( 'The type of border to use. Double borders must have a height of at least 3px to render properly.', 'wpcl_beaver_extender' ),
							),
						),
					),
				),
			),
		));
	}
}