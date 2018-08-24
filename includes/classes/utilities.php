<?php

/**
 * The plugin file that defines commonly used utility functions
 * @link    https://www.wpcodelabs.com
 * @since   1.0.0
 * @package wpcl_beaver_extender
 */

namespace Wpcl\Be\Classes;

class Utilities extends \Wpcl\Be\Plugin {

	public static function get_settings( $setting = false, $default = false ) {

		$settings = get_option( self::$name, array() );

		// If a specific setting was requested...
		if( $setting !== false ) {
			return isset( $settings[$setting] ) ? $settings[$setting] : $default;
		}
		// Or just return everything we've got
		else {
			return $settings;
		}
	}

	/**
	 * Take an array and encode as a json string
	 * @param  [array/string] $json Array if editor saved json string as array, which it does sometimes
	 * @return [string] string value
	 */
	public static function stringify_array( $json ) {
		return is_array( $json ) ? json_encode( $json ) : (string)$json;
	}

	public static function markup( $args = array() ) {

		$defaults = array(
			'context' => '',
			'open'    => '',
			'close'   => '',
			'content' => '',
			'echo'    => true,
			'params'  => array(),
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter to short circuit the markup API.
		 *
		 * @since 1.0.0
		 *
		 * @param bool  false Flag indicating short circuit content.
		 * @param array $args Array with markup arguments.
		 *
		 * @see be_markup $args Array.
		 */
		$override = apply_filters( "be_markup_{$args['context']}", false, $args );

		if( $override !== false ) {
			if ( !$args['echo'] ) {
				return $override;
			}
			echo $override;
		}


		if ( $args['context'] ) {

			$open = $args['open'] ? sprintf( $args['open'], self::be_markup_attr( $args['context'], $args['params'], $args ) ) : '';

			/**
			 * Contextual filter to modify 'open' markup.
			 *
			 * @since 2.4.0
			 *
			 * @param string $open HTML tag being processed by the API.
			 * @param array  $args Array with markup arguments.
			 *
			 * @see be_markup $args Array.
			 */
			$open = apply_filters( "be_markup_{$args['context']}_open", $open, $args );

			/**
			 * Contextual filter to modify 'close' markup.
			 *
			 * @since 2.4.0
			 *
			 * @param string $close HTML tag being processed by the API.
			 * @param array  $args  Array with markup arguments.
			 *
			 * @see be_markup $args Array.
			 */
			$close = apply_filters( "be_markup_{$args['context']}_close", $args['close'], $args );

			/**
			 * Contextual filter to modify 'content'.
			 *
			 * @since 2.6.0
			 *
			 * @param string $content Content being passed through Markup API.
			 * @param array  $args  Array with markup arguments.
			 *
			 * @see be_markup $args Array.
			 */
			$content = apply_filters( "be_markup_{$args['context']}_content", $args['content'], $args );

		} else {

			$open    = $args['open'];
			$close   = $args['close'];
			$content = $args['content'];

		}

		if ( $open || $args['content'] ) {
			/**
			 * Non-contextual filter to modify 'open' markup.
			 *
			 * @since 2.4.0
			 *
			 * @param string $open HTML tag being processed by the API.
			 * @param array  $args Array with markup arguments.
			 *
			 * @see be_markup $args Array.
			 */
			$open = apply_filters( 'be_markup_open', $open, $args );
		}

		if ( $close || $args['content'] ) {
			/**
			 * Non-contextual filter to modify 'close' markup.
			 *
			 * @since 2.4.0
			 *
			 * @param string $open HTML tag being processed by the API.
			 * @param array  $args Array with markup arguments.
			 *
			 * @see be_markup $args Array.
			 */
			$close = apply_filters( 'be_markup_close', $close, $args );
		}

		if ( $args['echo'] ) {
			echo $open . $content . $close;

			return null;
		} else {
			return $open . $content . $close;
		}

	}

	/**
	 * Build list of attributes into a string and apply contextual filter on string.
	 *
	 * The contextual filter is of the form `be_markup_attr_{context}_output`.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context    The context, to build filter name.
	 * @param array  $attributes Optional. Extra attributes to merge with defaults.
	 * @param array  $args       Optional. Custom data to pass to filter.
	 * @return string String of HTML attributes and values.
	 */
	public static function be_markup_attr( $context, $attributes = array(), $args = array() ) {

		$attributes = self::be_parse_attr( $context, $attributes, $args );

		$output = '';

		// Cycle through attributes, build tag attribute string.
		foreach ( $attributes as $key => $value ) {

			if ( !$value ) {
				continue;
			}

			if ( $value === true ) {
				$output .= esc_html( $key ) . ' ';
			} else {
				$output .= sprintf( '%s="%s" ', esc_html( $key ), esc_attr( $value ) );
			}

		}

		$output = apply_filters( "be_markup_attr_{$context}_output", $output, $attributes, $context, $args );

		return trim( $output );

	}

	/**
	 * Merge array of attributes with defaults, and apply contextual filter on array.
	 *
	 * The contextual filter is of the form `genesis_attr_{context}`.
	 *
	 * @since 2.0.0
	 *
	 * @param string $context    The context, to build filter name.
	 * @param array  $attributes Optional. Extra attributes to merge with defaults.
	 * @param array  $args       Optional. Custom data to pass to filter.
	 * @return array Merged and filtered attributes.
	 */
	public static function be_parse_attr( $context, $attributes = array(), $args = array() ) {

		$defaults = array(
			'class' => sanitize_html_class( $context ),
		);

		// Make sure each is a string or number
		foreach( $attributes as $att => $atts ) {
			if( is_object( $atts ) || is_array( $atts ) ) {
				unset( $attributes[$att] );
			}
		}

		if( isset( $attributes['class'] ) && !empty( trim( $attributes['class'] ) ) ) {
			$attributes['class'] = sanitize_html_class( $context ) . ' ' . trim( $attributes['class'] );
		}

		$attributes = wp_parse_args( $attributes, $defaults );
		// Generic filter
		$attributes = apply_filters( "be_markup_attr", $attributes, $context, $args );
		// Contextual filter.
		$attributes = apply_filters( "be_markup_attr_{$context}", $attributes, $context, $args );

		return $attributes;

	}

	public static function be_css( $selectors = array() ) {

		$css = '';

		foreach( $selectors as $selector => $styles ) {
			$css .= $selector . '{';
			foreach( $styles as $attribute => $style ) {
				if( is_array( $style ) ) { // For nesting fallback styles
					foreach( $style as $nested_style ) {
						$css .= sprintf( '%s : %s;', $attribute, $nested_style );
					}
				} else {
					$css .= sprintf( '%s : %s;', $attribute, $style );
				}
			}
			$css .= '}';
		}

		return $css;
	}

} // end class