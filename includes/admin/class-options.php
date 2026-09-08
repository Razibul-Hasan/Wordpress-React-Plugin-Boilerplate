<?php
/**
 * Admin menu and dashboard mount point.
 *
 * @package WPB
 * @since 1.0.0
 */

namespace WPB\Includes\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Builds the plugin's admin menu and renders the React root element.
 *
 * @since 1.0.0
 */
class Options {

	/**
	 * Top level menu slug, also used as the `page` query arg.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const MENU_SLUG = 'wpb-dashboard';

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu_page_callback' ) );
		add_action( 'in_admin_header', array( $this, 'remove_all_notices' ) );

		add_filter( 'plugin_action_links_' . WPB_BASE, array( $this, 'plugin_action_links_callback' ) );
	}

	/**
	 * Capability required to reach the plugin screens.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function capability() {
		return apply_filters( 'wpb_menu_capability', 'manage_options' );
	}

	/**
	 * Register the admin menu and its submenus.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function menu_page_callback() {
		add_menu_page(
			esc_html__( 'WP Boilerplate', 'wp-boilerplate' ),
			esc_html__( 'WP Boilerplate', 'wp-boilerplate' ),
			self::capability(),
			self::MENU_SLUG,
			array( self::class, 'dashboard_page_content' ),
			'dashicons-screenoptions',
			58.5
		);

		add_submenu_page(
			self::MENU_SLUG,
			esc_html__( 'Dashboard', 'wp-boilerplate' ),
			esc_html__( 'Dashboard', 'wp-boilerplate' ),
			self::capability(),
			self::MENU_SLUG,
			array( self::class, 'dashboard_page_content' )
		);

		add_submenu_page(
			self::MENU_SLUG,
			esc_html__( 'Settings', 'wp-boilerplate' ),
			esc_html__( 'Settings', 'wp-boilerplate' ),
			self::capability(),
			self::MENU_SLUG . '#settings',
			array( self::class, 'dashboard_page_content' )
		);
	}

	/**
	 * Render the container the React app mounts into.
	 *
	 * Everything after this point is handled in `src/index.js`.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function dashboard_page_content() {
		?>
		<div class="wpb-dashboard" id="wpb-dashboard-wrap">
			<div class="wpb-loading">
				<?php esc_html_e( 'Loading…', 'wp-boilerplate' ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Hide third party admin notices on the plugin's own screens.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function remove_all_notices() {
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( self::MENU_SLUG !== $page ) {
			return;
		}

		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
	}

	/**
	 * Add a settings shortcut under the plugin name on the plugins screen.
	 *
	 * @since 1.0.0
	 *
	 * @param array $links Existing action links.
	 *
	 * @return array
	 */
	public function plugin_action_links_callback( $links ) {
		$settings = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( admin_url( 'admin.php?page=' . self::MENU_SLUG ) ),
			esc_html__( 'Settings', 'wp-boilerplate' )
		);

		array_unshift( $links, $settings );

		return $links;
	}
}
