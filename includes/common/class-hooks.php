<?php
/**
 * Plugin hooks.
 *
 * @package WPB
 * @since 1.0.0
 */

namespace WPB\Includes\Common;

defined( 'ABSPATH' ) || exit;

/**
 * One place to register the plugin's WordPress hooks and its own extension
 * points.
 *
 * Keeping filters and actions declared together in the constructor makes it
 * easy to see everything the plugin touches.
 *
 * @since 1.0.0
 */
class Hooks {

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'wpb_allowed_html', array( $this, 'allowed_html_callback' ), 10, 1 );
		add_action( 'wpb_daily_event', array( $this, 'daily_event_callback' ) );
	}

	/**
	 * Extend the tags allowed in plugin output.
	 *
	 * @since 1.0.0
	 *
	 * @param array $tags Allowed tags keyed by tag name.
	 *
	 * @return array
	 */
	public function allowed_html_callback( $tags ) {
		$tags['svg']  = array(
			'class'   => true,
			'width'   => true,
			'height'  => true,
			'viewbox' => true,
			'fill'    => true,
			'xmlns'   => true,
		);
		$tags['path'] = array(
			'd'    => true,
			'fill' => true,
		);

		return $tags;
	}

	/**
	 * Example cron callback. Schedule it with:
	 * `wp_schedule_event( time(), 'daily', 'wpb_daily_event' );`
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function daily_event_callback() {
		// Recurring background work goes here.
	}
}
