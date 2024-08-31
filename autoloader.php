<?php
/**
 * Class autoloader
 *
 * @package Beacon
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

spl_autoload_register(
	function ( $class_name ) {
		$prefix = 'Eighteen73\\Beacon\\';
		if ( ! str_starts_with( $class_name, $prefix ) ) {
				return;
		}

		$relative_class = substr( $class_name, strlen( $prefix ) );
		$file           = __DIR__ . '/includes/classes/' . str_replace( '\\', '/', $relative_class ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);
