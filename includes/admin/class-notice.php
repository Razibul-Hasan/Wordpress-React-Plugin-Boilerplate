<?php
/**
 * Dismissible admin notices.
 *
 * @package WPB
 * @since 1.0.0
 */

namespace WPB\Includes\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * A single dismissible notice, wired end to end as a working example.
 *
 * Duplicate `render()` and the dismiss handler for each additional notice, or
 * delete the class if the plugin does not need one.
 *
 * @since 1.0.0
 */
class Notice {

	/**
	 * Option key holding the dismissed notice ids.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OPTION = 'wpb_dismissed_notices';

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'render' ) );
		add_action( 'wp_ajax_wpb_dismiss_notice', array( $this, 'dismiss_callback' ) );
	}

	/**
	 * Whether a notice has already been dismissed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Notice identifier.
	 *
	 * @return bool
	 */
	public static function is_dismissed( $id ) {
		$dismissed = get_option( self::OPTION, array() );

		return in_array( $id, (array) $dismissed, true );
	}

	/**
	 * Print the welcome notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render() {
		if ( ! current_user_can( Options::capability() ) || self::is_dismissed( 'welcome' ) ) {
			return;
		}
		?>
		<div class="notice notice-info is-dismissible wpb-notice" data-notice="welcome">
			<p>
				<?php esc_html_e( 'Thanks for installing WP Boilerplate.', 'wp-boilerplate' ); ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . Options::MENU_SLUG ) ); ?>">
					<?php esc_html_e( 'Open the dashboard', 'wp-boilerplate' ); ?>
				</a>
			</p>
		</div>
		<?php
	}

	/**
	 * Persist a dismissal. Called over admin-ajax from `assets/js/wpb-admin.js`.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function dismiss_callback() {
		check_ajax_referer( 'wpb-nonce', 'nonce' );

		if ( ! current_user_can( Options::capability() ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Permission denied.', 'wp-boilerplate' ) ), 403 );
		}

		$id = isset( $_POST['notice'] ) ? sanitize_key( wp_unslash( $_POST['notice'] ) ) : '';

		if ( ! $id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Missing notice id.', 'wp-boilerplate' ) ), 400 );
		}

		$dismissed   = (array) get_option( self::OPTION, array() );
		$dismissed[] = $id;

		update_option( self::OPTION, array_values( array_unique( $dismissed ) ) );

		wp_send_json_success();
	}
}
