<?php

namespace Wpcl\Be\Modules;

use \Wpcl\Be\Classes\Utilities as Utilities;

class BEHeading extends \FLBuilderModule implements \Wpcl\Be\Interfaces\Action_Hook_Subscriber, \Wpcl\Be\Interfaces\Filter_Hook_Subscriber {

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
			'name'          	=> __( 'Heading', 'wpcl_beaver_extender' ),
			'description'   	=> __( 'Display a title/page heading.', 'wpcl_beaver_extender' ),
			'category'      	=> __( 'Beaver Extender', 'wpcl_beaver_extender' ),
			'icon'				=> 'text.svg',
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
		printf( '<%s class="be-heading">', $settings->tag );

		/**
		 * Output the button
		 */
		\Wpcl\Be\Classes\Utilities::markup( array(
			'open'  => '<span %s>',
			'close' => '</span>',
			'content' => $settings->heading,
			'context' => "be-heading-text",
			'params'  => array(
				'class' => 'be-heading-text'
			),
		) );

		printf( '</%s>', $settings->tag );
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

		$styles = array(
			'color' => !empty( $settings->color ) ? "#{$settings->color}" : '',
			'text-align' => !empty( $settings->alignment ) ? $settings->alignment : '',
			'font-family' => ( !empty( $settings->font ) && $settings->font !== 'Default' ) ? $settings->font : '',
			'font-size' => ( $settings->font_size === 'custom' && !empty( $settings->custom_font_size ) ) ? "{$settings->custom_font_size}px" : '',
			'line-height' => ( $settings->line_height === 'custom' && !empty( $settings->custom_line_height ) ) ? "{$settings->custom_line_height}" : '',
			'letter-spacing' => ( $settings->letter_spacing === 'custom' && !empty( $settings->custom_letter_spacing ) ) ? "{$settings->custom_letter_spacing}px" : '',
		);

		// Output the CSS
		$css = Utilities::be_css( array(
			".fl-node-{$id} .fl-module-content .be-heading"  => $styles,
		) );

		echo $css;
	}

	/**
	 * Register the module and its form settings.
	 */
	public function register_module() {
		\FLBuilder::register_module( __CLASS__, array(
			'general'       => array(
				'title'         => __( 'General', 'fl-builder' ),
				'sections'      => array(
					'general'       => array(
						'title'         => '',
						'fields'        => array(
							'heading'        => array(
								'type'            => 'text',
								'label'           => __( 'Heading', 'fl-builder' ),
								'default'         => '',
								'preview'         => array(
									'type'            => 'text',
									'selector'        => '.be-heading-text',
								),
								'connections'     => array( 'string' ),
							),
							'tag'           => array(
								'type'          => 'select',
								'label'         => __( 'HTML Tag', 'fl-builder' ),
								'default'       => 'h3',
								'options'       => array(
									'h1'            => 'h1',
									'h2'            => 'h2',
									'h3'            => 'h3',
									'h4'            => 'h4',
									'h5'            => 'h5',
									'h6'            => 'h6',
								),
							),
						),
					),
				),
			),
			'style'         => array(
				'title'         => __( 'Style', 'fl-builder' ),
				'sections'      => array(
					'colors'        => array(
						'title'         => __( 'Colors', 'fl-builder' ),
						'fields'        => array(
							'color'          => array(
								'type'          => 'color',
								'show_reset'    => true,
								'label'         => __( 'Text Color', 'fl-builder' ),
							),
						),
					),
					'structure'     => array(
						'title'         => __( 'Structure', 'fl-builder' ),
						'fields'        => array(
							'alignment'     => array(
								'type'          => 'select',
								'label'         => __( 'Alignment', 'fl-builder' ),
								'default'       => 'left',
								'options'       => array(
									'left'      => __( 'Left', 'fl-builder' ),
									'center'    => __( 'Center', 'fl-builder' ),
									'right'     => __( 'Right', 'fl-builder' ),
								),
								'preview'         => array(
									'type'            => 'css',
									'selector'        => '.be-heading',
									'property'        => 'text-align',
								),
							),

							'font'          => array(
								'type'          => 'font',
								'default'		=> array(
									'family'		=> 'Default',
									'weight'		=> 300,
								),
								'label'         => __( 'Font', 'fl-builder' ),
								'preview'         => array(
									'type'            => 'font',
									'selector'        => '.fl-heading-text',
								),
							),
							'font_size'     => array(
								'type'          => 'select',
								'label'         => __( 'Font Size', 'fl-builder' ),
								'default'       => 'default',
								'options'       => array(
									'default'       => __( 'Default', 'fl-builder' ),
									'custom'        => __( 'Custom', 'fl-builder' ),
								),
								'toggle'        => array(
									'custom'        => array(
										'fields'        => array( 'custom_font_size' ),
									),
								),
							),
							'custom_font_size' => array(
								'type'          => 'text',
								'label'         => __( 'Custom Font Size', 'fl-builder' ),
								'default'       => '24',
								'maxlength'     => '3',
								'size'          => '4',
								'description'   => 'px',
								'sanitize'		=> 'absint',
							),
							'line_height'     => array(
								'type'          => 'select',
								'label'         => __( 'Line Height', 'fl-builder' ),
								'default'       => 'default',
								'options'       => array(
									'default'       => __( 'Default', 'fl-builder' ),
									'custom'        => __( 'Custom', 'fl-builder' ),
								),
								'toggle'        => array(
									'custom'        => array(
										'fields'        => array( 'custom_line_height' ),
									),
								),
							),
							'custom_line_height' => array(
								'type'          => 'text',
								'label'         => __( 'Custom Line Height', 'fl-builder' ),
								'default'       => '1.4',
								'maxlength'     => '4',
								'size'          => '4',
								'description'   => 'em',
							),
							'letter_spacing'     => array(
								'type'          => 'select',
								'label'         => __( 'Letter Spacing', 'fl-builder' ),
								'default'       => 'default',
								'options'       => array(
									'default'       => __( 'Default', 'fl-builder' ),
									'custom'        => __( 'Custom', 'fl-builder' ),
								),
								'toggle'        => array(
									'custom'        => array(
										'fields'        => array( 'custom_letter_spacing' ),
									),
								),
							),
							'custom_letter_spacing' => array(
								'type'          => 'text',
								'label'         => __( 'Custom Letter Spacing', 'fl-builder' ),
								'default'       => '0',
								'maxlength'     => '3',
								'size'          => '4',
								'description'   => 'px',
							),
						),
					),
				),
			),
		));
	}
}