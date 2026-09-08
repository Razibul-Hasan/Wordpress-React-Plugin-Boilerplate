<?php
/**
 * Singleton trait.
 *
 * @package WPB
 * @since 1.0.0
 */

namespace WPB\Includes\Traits;

defined( 'ABSPATH' ) || exit;

/**
 * Gives a class a shared instance via `ClassName::get_instance()`.
 *
 * Exists mainly to demonstrate the autoloader's `trait-` naming rule: anything
 * under `includes/traits/` is loaded from a `trait-*.php` file.
 *
 * @since 1.0.0
 */
trait Singleton {

	/**
	 * Shared instances keyed by class name.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static $instances = array();

	/**
	 * Get the shared instance for the calling class.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public static function get_instance() {
		$class = static::class;

		if ( ! isset( self::$instances[ $class ] ) ) {
			self::$instances[ $class ] = new static();
		}

		return self::$instances[ $class ];
	}
}
