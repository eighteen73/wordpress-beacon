<?php
/**
 * Runs the diagnostic checks
 *
 * @package WordPressDiagnostics
 */

namespace Eighteen73\WordPressDiagnostics;

/**
 * Runs the diagnostic checks
 */
class Checks extends Singleton {

	/**
	 * Runs should be initiated by a cron task
	 */
	public function run() {
		ray()->clearAll();
		ray( 'Running ' . time() );

		$version = get_bloginfo( 'url' );
		ray($version);

		$version = get_bloginfo( 'admin_email' );
		ray($version);

		$version = get_bloginfo( 'version' );
		ray($version);

		$version = phpversion();
		ray( $version );
	}
}
