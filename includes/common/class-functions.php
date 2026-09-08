<?php
/**
 * Shared helper functions.
 *
 * @package WPB
 * @since 1.0.0
 */

namespace WPB\Includes\Common;

use WPB\Includes\Activation;

defined( 'ABSPATH' ) || exit;

/**
 * Helpers reachable everywhere through `wp_boilerplate()`.
 *
 * @since 1.0.0
 */
class Functions {

	/**
	 * Option key holding the plugin settings.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const SETTINGS_KEY = 'wpb_settings';

	/**
	 * Read the plugin settings, merged over the defaults.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key     Optional single key to read.
	 * @param mixed  $default Value returned when the key is missing.
	 *
	 * @return mixed
	 */
	public function get_settings( $key = '', $default = null ) {
		$settings = wp_parse_args(
			(array) get_option( self::SETTINGS_KEY, array() ),
			Activation::default_settings()
		);

		if ( ! $key ) {
			return $settings;
		}

		return array_key_exists( $key, $settings ) ? $settings[ $key ] : $default;
	}

	/**
	 * Merge a partial array into the stored settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $values Values to write.
	 *
	 * @return bool
	 */
	public function update_settings( $values ) {
		if ( ! is_array( $values ) ) {
			return false;
		}

		return update_option( self::SETTINGS_KEY, array_merge( $this->get_settings(), $values ) );
	}

	/**
	 * Recursively sanitize a value coming from a request.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Raw value.
	 *
	 * @return mixed
	 */
	public function sanitize( $value ) {
		if ( is_array( $value ) ) {
			return array_map( array( $this, 'sanitize' ), $value );
		}

		if ( is_bool( $value ) || is_int( $value ) || is_float( $value ) ) {
			return $value;
		}

		return sanitize_text_field( $value );
	}

	/**
	 * HTML tags allowed when rendering stored, user supplied markup.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function allowed_html() {
		return apply_filters( 'wpb_allowed_html', wp_kses_allowed_html( 'post' ) );
	}

	/**
	 * Whether the current screen belongs to this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_plugin_screen() {
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		return 0 === strpos( $page, 'wpb-' );
	}
}
