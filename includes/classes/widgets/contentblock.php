<?php

/**
 * The plugin file that defines the content block plugin
 * @link    https://www.wpcodelabs.com
 * @since   1.0.0
 * @package wpcl_beaver_extender
 */

namespace Wpcl\Be\Classes\Widgets;

use \Wpcl\Be\Classes\Utilities as Utilities;

class ContentBlock extends \WP_Widget {

	public $widget_id_base;
	public $widget_name;
	public $widget_options;
	public $control_options;

	/**
	 * Constructor, initialize the widget
	 * @param $id_base, $name, $widget_options, $control_options ( ALL optional )
	 * @since 1.0.0
	 */
	public function __construct() {
		// Construct some options
		$this->widget_id_base = 'content_block_widget';
		$this->widget_name    = 'Content Block';
		$this->widget_options = array(
			'classname'   => 'content_block',
			'description' => 'Display Saved Content Block' );
		// Construct parent
		parent::__construct( $this->widget_id_base, $this->widget_name, $this->widget_options );
	}

	/**
	 * Create back end form for specifying image and content
	 * @param $instance
	 * @see https://codex.wordpress.org/Function_Reference/wp_parse_args
	 * @since 1.0.0
	 */
	public function form( $instance ) {
		// define our default values
		$defaults = array(
			'title' => null,
			'block' => null,
			'display_title' => 1,
		);
		// merge instance with default values
		$instance = wp_parse_args( (array)$instance, $defaults );
		// Get all content blocks
		$blocks = get_posts( array(
			'numberposts' => -1,
			'post_type' => 'contentblock',
			'suppress_filters' => true,
			'status' => 'publish'
		) );

		printf( '<p><label for="%s">%s</label>',
			$this->get_field_name( 'title' ),
			__( 'Title', 'wpcl_beaver_extender' )
		);

		printf( '<input type="text" class="widefat" id="%s" name="%s" value="%s"></p>',
			$this->get_field_id( 'title' ),
			$this->get_field_name( 'title' ),
			esc_attr( $instance['title'] )
		);

		printf( '<p><input type="checkbox" class="widefat" id="%s" name="%s" value="1"%s>',
			$this->get_field_id( 'display_title' ),
			$this->get_field_name( 'display_title' ),
			checked( intval( $instance['display_title'] ), 1, false )
		);

		printf( '<label for="%s">%s</label>',
			$this->get_field_name( 'display_title' ),
			__( 'Display Title?', 'wpcl_beaver_extender' )
		);

		printf( '<p><label for="%s">%s</label>',
			$this->get_field_name( 'block' ),
			__( 'Choose Block', 'wpcl_beaver_extender' )
		);

		printf( '<select class="widefat" id="%s" name="%s">',
			$this->get_field_id( 'block' ),
			$this->get_field_name( 'block' )
		);

		foreach( $blocks as $block ) {
			printf( '<option value="%s"%s>%s</option>',
				$block->ID,
				selected( $instance['block'], $block->ID, false ),
				$block->post_title
			);
		}

		echo '</select></p>';

	}

	/**
	 * Update form values
	 * @param $new_instance, $old_instance
	 * @since 1.0.0
	 */
	public function update( $new_instance, $old_instance ) {
		// Sanitize / clean values
		$instance = array(
			'title' => sanitize_text_field( $new_instance['title'] ),
			'block' => intval( $new_instance['block'] ),
			'display_title' => intval( $new_instance['display_title'] ),
		);
		// Merge values
		$instance = wp_parse_args( $instance, $old_instance );
		// Return values
		return $instance;
	}

	/**
	 * Output widget on the front end
	 * @param $args, $instance
	 * @since 1.0.0
	 */
	public function widget( $args, $instance ) {
		// Extract the widget arguments ( before_widget, after_widget, description, etc )
		extract( $args );
		// Display before widget args
		echo $before_widget;
		// Display Title
		if( !empty( $instance['title'] ) && intval( $instance['display_title'] ) === 1 ) {
			$instance['title']  = apply_filters( 'widget_title', $instance['title'], $instance, $this->widget_id_base );
			// Again check if filters cleared name, in the case of 'dont show titles' filter or something
			$instance['title']  = ( !empty( $instance['title']  ) ) ? $args['before_title'] . $instance['title']  . $args['after_title'] : '';
			// Display Title
			echo $instance['title'];
		}


		do_action( 'contentblock', $instance['block'] );

		// Display after widgets args
		echo $after_widget;
	} // end widget()

} // end class