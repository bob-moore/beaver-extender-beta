<?php

/**
 * The plugin file that defines the content block post type
 * @link    https://www.wpcodelabs.com
 * @since   1.0.0
 * @package wpcl_beaver_extender
 */

namespace Wpcl\Be\Classes;

use \Wpcl\Be\Classes\Utilities as Utilities;

class ContentBlock extends \Wpcl\Be\Plugin implements \Wpcl\Be\Interfaces\Action_Hook_Subscriber, \Wpcl\Be\Interfaces\Shortcode_Hook_Subscriber {

	/**
	 * Get the action hooks this class subscribes to.
	 * @return array
	 */
	public function get_actions() {
		return array(
			array( 'init' => 'register_post_type' ),
			array( 'contentblock' => 'do_content_block' ),
		);
	}

	/**
	 * Get the shortcode hooks this class subscribes to.
	 * @return array
	 */
	public static function get_shortcodes() {
		return array(
			array( 'contentblock' => 'shortcode' ),
		);
	}

	/**
	 * Register the post type
	 * @since 1.0.0
	 */
	public static function register_post_type() {
		$labels = array(
		    'name'                  => _x( 'Content Blocks', 'Post Type General Name', self::$name ),
		    'singular_name'         => _x( 'Content Block', 'Post Type Singular Name', self::$name ),
		    'menu_name'             => __( 'Content Blocks', self::$name ),
		    'name_admin_bar'        => __( 'Content Blocks', self::$name ),
		    'parent_item_colon'     => __( 'Parent Block:', self::$name ),
		    'all_items'             => __( 'All Blocks', self::$name ),
		    'add_new_item'          => __( 'Add New Block', self::$name ),
		    'add_new'               => __( 'Add New', self::$name ),
		    'new_item'              => __( 'New Block', self::$name ),
		    'edit_item'             => __( 'Edit Block', self::$name ),
		    'update_item'           => __( 'Update Block', self::$name ),
		    'view_item'             => __( 'View Block', self::$name ),
		    'search_items'          => __( 'Search Blocks', self::$name ),
		    'not_found'             => __( 'Not found', self::$name ),
		    'not_found_in_trash'    => __( 'Not found in Trash', self::$name ),
		    'items_list'            => __( 'Block list', self::$name ),
		    'items_list_navigation' => __( 'Block list navigation', self::$name ),
		    'filter_items_list'     => __( 'Filter block list', self::$name ),
		);
		$rewrite = array(
			'slug'                  => 'contentblocks',
			'with_front'            => true,
			'pages'                 => true,
			'feeds'                 => true,
		);
		$args = array(
		    'label'                 => __( 'Content Block', self::$name ),
		    'description'           => __( 'Content Blocks', self::$name ),
		    'labels'                => $labels,
		    'supports'              => array( 'title', 'editor', 'revisions', ),
		    'hierarchical'          => true,
		    'public'                => true,
		    'show_ui'               => true,
		    'show_in_menu'          => true,
		    'menu_position'         => 20,
		    'menu_icon'             => 'dashicons-text',
		    'show_in_admin_bar'     => false,
		    'show_in_nav_menus'     => false,
		    'can_export'            => true,
		    'has_archive'           => false,
		    'exclude_from_search'   => true,
		    'publicly_queryable'    => true, // isset( $_GET['fl_builder'] )
		    'capability_type'       => 'page',
		    'rewrite'               => $rewrite,
		);

		register_post_type( 'contentblock', $args );
	}

	public function shortcode( $atts ) {
		$atts = shortcode_atts( array( 'id' => null ), $atts, 'contentblock' );

		if( empty( $atts['id'] ) ) {
			return;
		}
		ob_start();
		do_action( 'contentblock', $atts['id'] );
		return ob_get_clean();
	}

	public function do_content_block( $id ) {
		if( get_post_status( $id ) !== 'publish' ) {
			return;
		}
		// Get the post data
		$block = get_post( $id );

		// Maybe use beaver builder...
		if( get_post_meta( $id, '_fl_builder_enabled', true ) === '1' && class_exists( 'FLBuilder' ) ) {
			\FLBuilder::render_query( array(
			    'post_type' => 'contentblock',
			    'p'         => $id,
			) );
		}

		// Else default behavior
		else {
			echo apply_filters( 'the_content', $block->post_content );
		}
	}
}