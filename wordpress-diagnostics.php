<?php
/**
 * Plugin Name:     eighteen73 Diagnostics
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     A collection of developer tools for WordPress projects
 * Author:          Orphans Web Team
 * Author URI:      https://orphans.co.uk
 * Update URI:      https://code.orphans.co.uk/packages/wordpress/wordpress-diagnostics/
 * Text Domain:     wordpress-diagnostics
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         WordPressDiagnostics
 */

namespace Eighteen73\WordPressDiagnostics;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

spl_autoload_register(
	function ( $class_name ) {
		$path_parts = explode( '\\', $class_name );

		if ( ! empty( $path_parts ) ) {
			$package = $path_parts[0];

			unset( $path_parts[0] );

			if ( 'WordPressDiagnostics' === $package ) {
				require_once __DIR__ . '/includes/classes/' . implode( '/', $path_parts ) . '.php';
			}
		}
	}
);

CronControl::instance()->setup();




