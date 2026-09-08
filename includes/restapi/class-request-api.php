<?php
/**
 * REST API routes.
 *
 * @package WPB
 * @since 1.0.0
 */

namespace WPB\Includes\Restapi;

use WPB\Includes\Admin\Options;
use WP_REST_Response;

defined( 'ABSPATH' ) || exit;

/**
 * Registers every route under the `wpb/v1` namespace.
 *
 * Routes are declared as a flat array so adding one is a single entry rather
 * than another `register_rest_route()` call.
 *
 * @since 1.0.0
 */
class RequestApi {

	/**
	 * REST namespace.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const REST_NAMESPACE = 'wpb/v1';

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_route' ) );
	}

	/**
	 * Register the routes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_route() {
		$routes = array(
			array(
				'endpoint'            => 'settings',
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_settings_callback' ),
				'permission_callback' => array( $this, 'get_endpoint_permissions' ),
			),
			array(
				'endpoint'            => 'settings',
				'methods'             => 'POST',
				'callback'            => array( $this, 'save_settings_callback' ),
				'permission_callback' => array( $this, 'get_endpoint_permissions' ),
			),
		);

		foreach ( $routes as $route ) {
			register_rest_route(
				self::REST_NAMESPACE,
				'/' . $route['endpoint'],
				array(
					'methods'             => $route['methods'],
					'callback'            => $route['callback'],
					'permission_callback' => $route['permission_callback'],
				)
			);
		}
	}

	/**
	 * Capability check shared by the authenticated routes.
	 *
	 * Swap for `__return_true` only on routes that are genuinely public.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function get_endpoint_permissions() {
		return current_user_can( Options::capability() );
	}

	/**
	 * GET /wpb/v1/settings
	 *
	 * @since 1.0.0
	 *
	 * @return WP_REST_Response
	 */
	public function get_settings_callback() {
		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => wp_boilerplate()->get_settings(),
			),
			200
		);
	}

	/**
	 * POST /wpb/v1/settings
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request Incoming request.
	 *
	 * @return WP_REST_Response
	 */
	public function save_settings_callback( $request ) {
		$params = $request->get_json_params();

		if ( empty( $params ) || ! is_array( $params ) ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => esc_html__( 'No settings were supplied.', 'wp-boilerplate' ),
				),
				400
			);
		}

		wp_boilerplate()->update_settings( wp_boilerplate()->sanitize( $params ) );

		return new WP_REST_Response(
			array(
				'success' => true,
				'data'    => wp_boilerplate()->get_settings(),
				'message' => esc_html__( 'Settings saved.', 'wp-boilerplate' ),
			),
			200
		);
	}
}
