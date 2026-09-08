<?php
/**
 * Activation and deactivation routines.
 *
 * @package WPB
 * @since 1.0.0
 */

namespace WPB\Includes;

defined( 'ABSPATH' ) || exit;

/**
 * Runs the one-off tasks tied to enabling or disabling the plugin.
 *
 * Anything that needs to happen exactly once — seeding options, creating
 * tables, scheduling cron events — belongs here rather than on `plugins_loaded`.
 *
 * @since 1.0.0
 */
class Activation {

	/**
	 * Default option values written on first activation.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function default_settings() {
		return array(
			'enabled' => true,
			'example' => '',
		);
	}

	/**
	 * Fired by `register_activation_hook`.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function activate() {
		if ( false === get_option( 'wpb_settings', false ) ) {
			add_option( 'wpb_settings', self::default_settings() );
		}

		add_option( 'wpb_installed_version', WPB_VER );

		// Custom post types registered on `init` are not loaded yet, so flush
		// after registering them here if the plugin adds rewrite rules.
		PostType::register();
		flush_rewrite_rules();
	}

	/**
	 * Fired by `register_deactivation_hook`.
	 *
	 * Never delete user data here — deactivation is not uninstallation. Put
	 * destructive cleanup in an `uninstall.php` file instead.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'wpb_daily_event' );
		flush_rewrite_rules();
	}
}
