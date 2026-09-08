<?php
/**
 * Plugin Name: WP Boilerplate
 * Plugin URI:  https://example.com/wp-boilerplate
 * Description: Starter boilerplate for building a WordPress plugin with a React admin dashboard, REST API and SCSS build pipeline.
 * Version:     1.0.0
 * Author:      Your Name
 * Author URI:  https://example.com
 * Text Domain: wp-boilerplate
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License:     GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WPB
 */

use WPB\Includes\Activation;
use WPB\Includes\Common\Functions;
use WPB\Includes\Initialization;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin constants.
 *
 * WPB_VER  is read by gulp when packaging a release, keep the format x.y.z.
 */
define( 'WPB_VER', '1.0.0' );
define( 'WPB_FILE', __FILE__ );
define( 'WPB_URL', plugin_dir_url( __FILE__ ) );
define( 'WPB_BASE', plugin_basename( __FILE__ ) );
define( 'WPB_PATH', plugin_dir_path( __FILE__ ) );

spl_autoload_register( 'wpb_autoloader' );

if ( ! function_exists( 'wp_boilerplate' ) ) {
	/**
	 * Shared helper instance.
	 *
	 * Gives every file one entry point to the common helpers, e.g.
	 * `wp_boilerplate()->get_settings()`.
	 *
	 * @since 1.0.0
	 *
	 * @return Functions
	 */
	function wp_boilerplate() {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new Functions();
		}

		return $instance;
	}
}

add_action( 'plugins_loaded', 'wpb_init', 10 );

/**
 * Boot the plugin once all other plugins are loaded.
 *
 * @since 1.0.0
 *
 * @return void
 */
function wpb_init() {
	new Initialization();
}

register_activation_hook( __FILE__, array( Activation::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Activation::class, 'deactivate' ) );

/**
 * PSR-4 style autoloader mapping the WPB namespace onto the plugin folders.
 *
 * Namespace segments become lowercase folders and underscores become dashes,
 * so `WPB\Includes\Admin\Options` resolves to `includes/admin/class-options.php`.
 * Classes are prefixed with `class-`, anything under a `traits` folder with
 * `trait-`, matching the WordPress file naming standard.
 *
 * @since 1.0.0
 *
 * @param string $class_name Fully qualified class name being loaded.
 *
 * @return void
 */
function wpb_autoloader( $class_name ) {
	$namespace = 'WPB\\';
	$base_dir  = trailingslashit( WPB_PATH );

	$len = strlen( $namespace );
	if ( strncmp( $namespace, $class_name, $len ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class_name, $len );
	$segments       = explode( '\\', $relative_class );

	$file_name = array_pop( $segments );
	$subfolder = strtolower(
		implode(
			'/',
			array_map(
				function ( $segment ) {
					return str_replace( '_', '-', $segment );
				},
				$segments
			)
		)
	);

	$prefix    = ( strpos( $subfolder, 'traits' ) !== false ) ? 'trait-' : 'class-';
	$file_name = strtolower(
		preg_replace(
			'/([a-z])([A-Z])/',
			'$1-$2',
			str_replace( '_', '-', $file_name )
		)
	);

	$file = rtrim( $base_dir . $subfolder, '/' ) . '/' . $prefix . $file_name . '.php';

	if ( file_exists( $file ) ) {
		require_once $file;
	}
}
