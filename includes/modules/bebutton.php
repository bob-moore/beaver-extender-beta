<?php

namespace Wpcl\Be\Modules;

use \Wpcl\Be\Classes\Utilities as Utilities;

class BEButton extends \FLBuilderModule implements \Wpcl\Be\Interfaces\Action_Hook_Subscriber, \Wpcl\Be\Interfaces\Filter_Hook_Subscriber {

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
			'name'          	=> __( 'Button', 'wpcl_beaver_extender' ),
			'description'   	=> __( 'Insert a button link', 'wpcl_beaver_extender' ),
			'category'      	=> __( 'Beaver Extender', 'wpcl_beaver_extender' ),
			'partial_refresh'	=> true,
			'editor_export' 	=> true,
			'icon'				=> 'button.svg',
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
			array( 'be_markup_attr_be-button' => array( 'button_attr', 10, 3 ) ),
			array( 'be_markup_be-button_content' => array( 'button_content', 10, 2 ) ),
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
			'open'  => '<a %s>',
			'close' => '</a>',
			'content' => $settings->text,
			'context' => "be-button",
			'instance' => $module,
		) );
	}

	public function button_attr( $atts, $context, $args ) {
		if( $args['instance'] !== $this ) {
			return $atts;
		}
		// Add the button class
		$atts['class'] .= ' button';
		// Do the type settings
		$atts['class'] .= sprintf( ' button-%s', $this->settings->style );
		$atts['class'] .= !empty( $this->settings->icon ) ? ' has-icon' : '';
		// Do the width settings
		$atts['class'] .= $this->settings->width === 'full' ? ' full-width' : '';
		$atts['style']  = $this->settings->width === 'custom' ? sprintf( 'width: %dpx;', $this->settings->custom_width ) : '';
		// Do the icon settings
		$atts['class'] .= $this->settings->icon_animation === 'enable' ? ' animated-icon' : '';
		// Do the href
		$atts['href']   = !empty( $this->settings->link ) ? $this->settings->link : "#";
		// Do the target
		$atts['target'] = $this->settings->link_target;
		// Do the rel
		$atts['rel']    = $this->settings->link_nofollow === 'yes' ? 'nofollow' : '';
		$atts['rel']   .= $this->settings->link_target === '_blank' ? ' noreferrer noopener' : '';

		return $atts;
	}

	public function button_content( $content, $args ) {

		if( $args['instance'] !== $this ) {
			return $content;
		}
		// Append a span around the content
		$content = sprintf( '<span class="be-button-content">%s</span>', $this->settings->text );
		// Maybe append icon
		if( !empty( $this->settings->icon ) ) {

			$icon = sprintf( '<span class="be-button-icon be-button-icon-%s %s"></span>', $this->settings->icon_position, $this->settings->icon );

			$icon = '<span class="be-icon-wrapper">' . $icon . '</span>';

			$content = $this->settings->icon_position === 'after' ? $content . $icon : $icon . $content;
		}
		return "<span class='be-button-inner'>{$content}</span>";
	}

	/**
	 * Organize the css end output
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
		$base = array(
			'font-size'      => !empty( $settings->font_size )      ? "{$settings->font_size}px"       : '',
			'padding-top'    => !empty( $settings->padding_top )    ? "{$settings->padding_top}px"     : '',
			'padding-right'  => !empty( $settings->padding_right )  ? "{$settings->padding_right}px"   : '',
			'padding-bottom' => !empty( $settings->padding_bottom ) ? "{$settings->padding_bottom}px"  : '',
			'padding-left'   => !empty( $settings->padding_left )   ? "{$settings->padding_left}px"    : '',
			'color'          => !empty( $settings->text_color )     ? "#{$settings->text_color}"       : '',
			'border-color'   => !empty( $settings->border_color )   ? "#{$settings->border_color}"     : '',
			'border-radius'  => !empty( $settings->border_radius )  ? "{$settings->border_radius}px"   : '',
			'border-width'   => !empty( $settings->border_size )    ? "{$settings->border_size}px"     : '',
		);
		/**
		 * Hover styles
		 */
		$hover = array(
			'color'        => !empty( $settings->text_hover_color )   ? "#{$settings->text_hover_color}"   : '',
			'border-color' => !empty( $settings->border_hover_color ) ? "#{$settings->border_hover_color}" : '',
		);
		/**
		 * Flat button styles
		 */
		if( $settings->style === 'flat' ) {
			$base = array_merge( $base, array(
				'background-color' => !empty( $settings->bg_color ) ? "#{$settings->bg_color}" : '',
			) );
			$hover = array_merge( $hover, array(
				'background-color' => !empty( $settings->bg_hover_color ) ? "#{$settings->bg_hover_color}" : '',
			) );
		}
		/**
		 * Gradient button styles
		 */
		else if( $settings->style === 'gradient' ) {
			// Set up our start color
			$start_color = !empty( $settings->bg_color ) ? $settings->bg_color : '1779ba';
			// Set up our end color
			$end_color = !empty( $settings->bg_gradient_color ) ? $settings->bg_gradient_color : $start_color;

			$base = array_merge( $base, array(
				'background' => array(
					$start_color, // Older browsers
					sprintf( '-moz-linear-gradient( left, #%s 0%%, #%s 100%% )', $start_color, $end_color ),
					sprintf( '-webkit-linear-gradient( left, #%s 0%%, #%s 100%% )', $start_color, $end_color ),
					sprintf( 'linear-gradient( to right, #%s 0%%, #%s 100%% )', $start_color, $end_color ),
				),
			) );
		}
		/**
		 * Flat button styles
		 */
		else if( $settings->style === 'transparent' ) {

			$rgb = !empty( $settings->bg_color ) ? implode( ',', \FLBuilderColor::hex_to_rgb( $settings->bg_color ) ) : '23, 121, 186';

			$hover_rgb = !empty( $settings->bg_hover_color ) ? implode( ',', \FLBuilderColor::hex_to_rgb( $settings->bg_hover_color ) ) : $rgb;

			$opacity = intval( $settings->bg_opacity ) >= 100 ? $settings->bg_opacity : ".{$settings->bg_opacity}";

			$hover_opacity = intval( $settings->bg_hover_opacity ) >= 100 ? $settings->bg_hover_opacity : ".{$settings->bg_hover_opacity}";

			$base = array_merge( $base, array(
				'background-color' => !empty( $settings->bg_color ) ? sprintf( 'rgba( %s, %s )', $rgb, $opacity ) : '',
			) );
			$hover = array_merge( $hover, array(
				'background-color' => !empty( $settings->bg_hover_color ) ? sprintf( 'rgba( %s, %s )', $hover_rgb, $hover_opacity ) : '',
			) );
		}

		/**
		 * Output the CSS
		 */
		$css = Utilities::be_css( array(
			".fl-node-{$id} .fl-module-content .be-button"       => $base,
			".fl-node-{$id} .fl-module-content .be-button:hover" => $hover,
			".fl-node-{$id} .fl-module-content .be-button:focus" => $hover,
		) );

		echo $css;
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
			'general'       => array(
				'title'         => __( 'General', 'wpcl_beaver_extender' ),
				'sections'      => array(
					'general'       => array(
						'title'         => '',
						'fields'        => array(
							'text'          => array(
								'type'          => 'text',
								'label'         => __( 'Text', 'wpcl_beaver_extender' ),
								'default'       => __( 'Click Here', 'wpcl_beaver_extender' ),
								'preview'         => array(
									'type'            => 'text',
									'selector'        => '.be-button-text',
								),
								'connections'         => array( 'string' ),
							),
							'icon'          => array(
								'type'          => 'icon',
								'label'         => __( 'Icon', 'wpcl_beaver_extender' ),
								'show_remove'   => true,
							),
							'icon_position' => array(
								'type'          => 'select',
								'label'         => __( 'Icon Position', 'wpcl_beaver_extender' ),
								'default'       => 'before',
								'options'       => array(
									'before'        => __( 'Before Text', 'wpcl_beaver_extender' ),
									'after'         => __( 'After Text', 'wpcl_beaver_extender' ),
								),
							),
							'icon_animation' => array(
								'type'          => 'select',
								'label'         => __( 'Icon Visibility', 'wpcl_beaver_extender' ),
								'default'       => 'disable',
								'options'       => array(
									'disable'        => __( 'Always Visible', 'wpcl_beaver_extender' ),
									'enable'         => __( 'Fade In On Hover', 'wpcl_beaver_extender' ),
								),
							),
						),
					),
					'link'          => array(
						'title'         => __( 'Link', 'wpcl_beaver_extender' ),
						'fields'        => array(
							'link'          => array(
								'type'          => 'link',
								'label'         => __( 'Link', 'wpcl_beaver_extender' ),
								'placeholder'   => __( 'http://www.example.com', 'wpcl_beaver_extender' ),
								'preview'       => array(
									'type'          => 'none',
								),
								'connections'         => array( 'url' ),
							),
							'link_target'   => array(
								'type'          => 'select',
								'label'         => __( 'Link Target', 'wpcl_beaver_extender' ),
								'default'       => '_self',
								'options'       => array(
									'_self'         => __( 'Same Window', 'wpcl_beaver_extender' ),
									'_blank'        => __( 'New Window', 'wpcl_beaver_extender' ),
								),
								'preview'       => array(
									'type'          => 'none',
								),
							),
							'link_nofollow'          => array(
								'type'          => 'select',
								'label'         => __( 'Link No Follow', 'wpcl_beaver_extender' ),
								'default'       => 'no',
								'options' 		=> array(
									'yes' 			=> __( 'Yes', 'wpcl_beaver_extender' ),
									'no' 			=> __( 'No', 'wpcl_beaver_extender' ),
								),
								'preview'       => array(
									'type'          => 'none',
								),
							),
						),
					),
				),
			),
			'style'         => array(
				'title'         => __( 'Style', 'wpcl_beaver_extender' ),
				'sections'      => array(
					'colors'        => array(
						'title'         => __( 'Colors', 'wpcl_beaver_extender' ),
						'fields'        => array(
							'bg_color'      => array(
								'type'          => 'color',
								'label'         => __( 'Background Color', 'wpcl_beaver_extender' ),
								'default'       => '',
								'show_reset'    => true,
								'preview'       => array(
									'type'          => 'refresh',
								),
							),
							'bg_gradient_color'      => array(
								'type'          => 'color',
								'label'         => __( 'Background Gradient Color', 'wpcl_beaver_extender' ),
								'default'       => '',
								'show_reset'    => true,
								'preview'       => array(
									'type'          => 'refresh',
								),
							),
							'bg_hover_color' => array(
								'type'          => 'color',
								'label'         => __( 'Background Hover Color', 'wpcl_beaver_extender' ),
								'default'       => '',
								'show_reset'    => true,
								'preview'       => array(
									'type'          => 'refresh',
								),
							),
							'text_color'    => array(
								'type'          => 'color',
								'label'         => __( 'Text Color', 'wpcl_beaver_extender' ),
								'default'       => '',
								'show_reset'    => true,
								'preview'       => array(
									'type'          => 'refresh',
								),
							),
							'text_hover_color' => array(
								'type'          => 'color',
								'label'         => __( 'Text Hover Color', 'wpcl_beaver_extender' ),
								'default'       => '',
								'show_reset'    => true,
								'preview'       => array(
									'type'          => 'refresh',
								),
							),
							'border_color'    => array(
								'type'          => 'color',
								'label'         => __( 'Border Color', 'wpcl_beaver_extender' ),
								'default'       => '',
								'show_reset'    => true,
								'preview'       => array(
									'type'          => 'refresh',
								),
							),
							'border_hover_color' => array(
								'type'          => 'color',
								'label'         => __( 'Border Hover Color', 'wpcl_beaver_extender' ),
								'default'       => '',
								'show_reset'    => true,
								'preview'       => array(
									'type'          => 'refresh',
								),
							),
						),
					),
					'style'         => array(
						'title'         => __( 'Style', 'wpcl_beaver_extender' ),
						'fields'        => array(
							'style'         => array(
								'type'          => 'select',
								'label'         => __( 'Background Style', 'wpcl_beaver_extender' ),
								'default'       => 'flat',
								'options'       => array(
									'flat'          => __( 'Flat', 'wpcl_beaver_extender' ),
									'gradient'      => __( 'Gradient', 'wpcl_beaver_extender' ),
									'transparent'   => __( 'Transparent', 'wpcl_beaver_extender' ),
								),
								'toggle'        => array(
									'transparent'   => array(
										'fields'        => array( 'bg_opacity', 'bg_hover_opacity' ),
									),
									'gradient' => array(
										'fields' => array( 'bg_gradient_color' ),
									),
								),
							),
							'fullstyle'         => array(
								'type'          => 'select',
								'label'         => __( 'fullstyle', 'wpcl_beaver_extender' ),
								'default'       => 'flat',
								'options'       => array(
									'3d'          => __( '3D', 'wpcl_beaver_extender' ),
									'gradient'      => __( 'Gradient', 'wpcl_beaver_extender' ),
									'transparent'   => __( 'Transparent', 'wpcl_beaver_extender' ),
								),
							),
							'border_size'   => array(
								'type'          => 'text',
								'label'         => __( 'Border Size', 'wpcl_beaver_extender' ),
								'default'       => '',
								'description'   => 'px',
								'maxlength'     => '3',
								'size'          => '5',
								'placeholder'   => '0',
							),
							'bg_opacity'    => array(
								'type'          => 'text',
								'label'         => __( 'Background Opacity', 'wpcl_beaver_extender' ),
								'default'       => '0',
								'description'   => '%',
								'maxlength'     => '3',
								'size'          => '5',
								'placeholder'   => '0',
								'sanitize'		=> 'absint',
							),
							'bg_hover_opacity'    => array(
								'type'          => 'text',
								'label'         => __( 'Background Hover Opacity', 'wpcl_beaver_extender' ),
								'default'       => '0',
								'description'   => '%',
								'maxlength'     => '3',
								'size'          => '5',
								'placeholder'   => '0',
								'sanitize'		=> 'absint',
							),
						),
					),
					'formatting'    => array(
						'title'         => __( 'Structure', 'wpcl_beaver_extender' ),
						'fields'        => array(
							'width'         => array(
								'type'          => 'select',
								'label'         => __( 'Width', 'wpcl_beaver_extender' ),
								'default'       => 'auto',
								'options'       => array(
									'auto'          => _x( 'Auto', 'Width.', 'wpcl_beaver_extender' ),
									'full'          => __( 'Full Width', 'wpcl_beaver_extender' ),
									'custom'        => __( 'Custom', 'wpcl_beaver_extender' ),
								),
								'toggle'        => array(
									'auto'          => array(
										'fields'        => array( 'align' ),
									),
									'full'          => array(),
									'custom'        => array(
										'fields'        => array( 'align', 'custom_width' ),
									),
								),
							),
							'custom_width'  => array(
								'type'          => 'text',
								'label'         => __( 'Custom Width', 'wpcl_beaver_extender' ),
								'default'       => '200',
								'maxlength'     => '3',
								'size'          => '4',
								'description'   => 'px',
							),
							'align'         => array(
								'type'          => 'select',
								'label'         => __( 'Alignment', 'wpcl_beaver_extender' ),
								'default'       => 'left',
								'options'       => array(
									'center'        => __( 'Center', 'wpcl_beaver_extender' ),
									'left'          => __( 'Left', 'wpcl_beaver_extender' ),
									'right'         => __( 'Right', 'wpcl_beaver_extender' ),
								),
							),
							'font_size'     => array(
								'type'          => 'text',
								'label'         => __( 'Font Size', 'wpcl_beaver_extender' ),
								'default'       => '',
								'maxlength'     => '3',
								'size'          => '4',
								'description'   => 'px',
							),
							'padding'       => array(
								'type'        => 'dimension',
								'label'         => __( 'Padding', 'wpcl_beaver_extender' ),
								'description' => 'px',
							),
							'border_radius' => array(
								'type'          => 'text',
								'label'         => __( 'Round Corners', 'wpcl_beaver_extender' ),
								'default'       => '',
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