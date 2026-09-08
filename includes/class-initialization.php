<?php
/**
 * Plugin bootstrap.
 *
 * @package WPB
 * @since 1.0.0
 */

namespace WPB\Includes;

use WPB\Includes\Admin\Notice;
use WPB\Includes\Admin\Options;
use WPB\Includes\Common\Hooks;
use WPB\Includes\Restapi\RequestApi;

defined( 'ABSPATH' ) || exit;

/**
 * Wires every part of the plugin together.
 *
 * Register new classes inside `requires()` so there is a single, readable list
 * of everything the plugin boots.
 *
 * @since 1.0.0
 */
class Initialization {

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->requires();

		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts_callback' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts_callback' ) );
	}

	/**
	 * Instantiate the plugin classes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function requires() {
		new PostType();

		new Options();
		new Notice();

		new Hooks();
		new RequestApi();
	}

	/**
	 * Load the plugin translations.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wp-boilerplate', false, dirname( WPB_BASE ) . '/languages' );
	}

	/**
	 * Enqueue the admin assets.
	 *
	 * The heavy dashboard bundle is only loaded on the plugin's own screen so
	 * the rest of wp-admin stays untouched.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function admin_scripts_callback() {
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		wp_enqueue_style( 'wpb-admin-style', WPB_URL . 'assets/css/wpb-admin.css', array(), WPB_VER );
		wp_enqueue_script( 'wpb-admin-script', WPB_URL . 'assets/js/wpb-admin.js', array( 'jquery' ), WPB_VER, true );

		wp_localize_script(
			'wpb-admin-script',
			'wpbAdmin',
			array(
				'nonce' => wp_create_nonce( 'wpb-nonce' ),
			)
		);

		if ( 'wpb-dashboard' !== $page ) {
			return;
		}

		$user_info = get_userdata( get_current_user_id() );

		// 'wp-components' pulls in core's component styles and, as a dependency,
		// guarantees they print before the plugin stylesheet can override them.
		wp_enqueue_style( 'wpb-dashboard-style', WPB_URL . 'assets/css/wpb-backend.css', array( 'wp-components' ), WPB_VER );

		// These handles provide the globals the bundle expects, see the
		// externals map in webpack.config.js. React comes from core so the
		// dashboard shares one React instance with wp.components.
		wp_enqueue_script(
			'wpb-dashboard-script',
			WPB_URL . 'assets/js/wpb.js',
			array( 'react', 'react-dom', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch' ),
			WPB_VER,
			true
		);
		wp_enqueue_media();

		wp_localize_script(
			'wpb-dashboard-script',
			'wpbBackendData',
			array(
				'url'      => WPB_URL,
				'ajax'     => admin_url( 'admin-ajax.php' ),
				'rest'     => esc_url_raw( rest_url( 'wpb/v1/' ) ),
				'restNonce' => wp_create_nonce( 'wp_rest' ),
				'nonce'    => wp_create_nonce( 'wpb-nonce' ),
				'version'  => WPB_VER,
				'userInfo' => array(
					'name'  => $user_info->first_name ? trim( $user_info->first_name . ' ' . $user_info->last_name ) : $user_info->user_login,
					'email' => $user_info->user_email,
				),
			)
		);

		wp_set_script_translations( 'wpb-dashboard-script', 'wp-boilerplate', WPB_PATH . 'languages/' );
	}

	/**
	 * Enqueue the frontend assets.
	 *
	 * Add a conditional here so the bundle only loads on the pages that need
	 * it rather than site-wide.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function frontend_scripts_callback() {
		wp_enqueue_style( 'wpb-frontend-style', WPB_URL . 'assets/css/wpb-frontend.css', array(), WPB_VER );
		wp_enqueue_script( 'wpb-frontend-script', WPB_URL . 'assets/js/frontend-script.js', array(), WPB_VER, true );

		wp_localize_script(
			'wpb-frontend-script',
			'wpbFrontendData',
			array(
				'ajax'  => admin_url( 'admin-ajax.php' ),
				'rest'  => esc_url_raw( rest_url( 'wpb/v1/' ) ),
				'nonce' => wp_create_nonce( 'wpb-nonce' ),
			)
		);
	}
}
