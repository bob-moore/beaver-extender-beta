<?php

/**
 * Fired during plugin activation
 *
 * This class defines all code necessary to run during the plugin's activation.
 * @link    https://www.wpcodelabs.com
 * @since   1.0.0
 * @package wpcl_beaver_extender
 */

namespace Wpcl\Be;

class Activator {

	/**
	 * Activate Plugin
	 *
	 * Register Post Types, Register Taxonomies, and Flush Permalinks
	 * @since 1.0.0
	 */
	public static function activate() {
		// Register post types
		\Wpcl\Be\Classes\ContentBlock::register_post_type();
		// Flush rewrite rules
		self::flush_permalinks();
		// Add filter
		self::turn_on_contentblocks();
	}

	/**
	 * Turns on our post type by default
	 */
	public static function turn_on_contentblocks() {

		$activated_post_types = get_option( '_fl_builder_post_types', array( 'page' ) );

		if( array_search( 'contentblock', $activated_post_types ) === false && get_option( '_beaver_extender_active', false ) !== '1' ) {
			// Append our post type
			$activated_post_types[] = 'contentblock';
			// Update option
			update_option( '_fl_builder_post_types', $activated_post_types );
		}

		update_option( '_beaver_extender_active', '1' );
	}

	/**
	 * Flush permalinks
	 */
	public static function flush_permalinks() {
		global $wp_rewrite;
		$wp_rewrite->init();
		$wp_rewrite->flush_rules();
	}



} // end class