<?php
/**
 * Custom post type registration.
 *
 * @package WPB
 * @since 1.0.0
 */

namespace WPB\Includes;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the plugin's custom post type.
 *
 * Rename `wpb_item` and its labels, or delete this class entirely if the
 * plugin does not store its own content.
 *
 * @since 1.0.0
 */
class PostType {

	/**
	 * Post type slug. Keep it under 20 characters.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const POST_TYPE = 'wpb_item';

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( self::class, 'register' ) );
	}

	/**
	 * Register the post type.
	 *
	 * Called on `init` and again during activation so rewrite rules can be
	 * flushed against a registered type.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function register() {
		$labels = array(
			'name'          => esc_html__( 'Items', 'wp-boilerplate' ),
			'singular_name' => esc_html__( 'Item', 'wp-boilerplate' ),
			'add_new_item'  => esc_html__( 'Add New Item', 'wp-boilerplate' ),
			'edit_item'     => esc_html__( 'Edit Item', 'wp-boilerplate' ),
			'search_items'  => esc_html__( 'Search Items', 'wp-boilerplate' ),
			'not_found'     => esc_html__( 'No items found.', 'wp-boilerplate' ),
		);

		register_post_type(
			self::POST_TYPE,
			array(
				'labels'       => $labels,
				'public'       => false,
				'show_ui'      => false,
				'show_in_rest' => false,
				'supports'     => array( 'title', 'editor', 'custom-fields' ),
				'capability_type' => 'post',
			)
		);
	}
}
