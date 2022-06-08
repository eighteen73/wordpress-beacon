<?php
/**
 * Singleton definition for this plugin's classes
 *
 * @package WordPressDiagnostics
 */

namespace Eighteen73\WordPressDiagnostics;

/**
 * Abstract class
 */
abstract class Singleton {
	/**
	 * Return instance of class
	 *
	 * @return self
	 */
	public static function instance(): Singleton {
		static $instance;

		if ( empty( $instance ) ) {
			$class = get_called_class();

			$instance = new $class();

			if ( method_exists( $instance, 'setup' ) ) {
				$instance->setup();
			}
		}

		return $instance;
	}
}
