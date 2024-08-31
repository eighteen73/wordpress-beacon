<?php
/**
 * Schedules this plugin's tasks
 *
 * @package Beacon
 */

namespace Eighteen73\Beacon;

use WP_Error;

/**
 * Schedules this plugin's tasks
 */
class Cron {

	/**
	 * Set up the cron control
	 */
	public static function setup() {
		add_filter( 'cron_schedules', [ 'Eighteen73\Beacon\Cron', 'job_schedule' ] );

		// Note this can't be run if it's converted to a mu-plugin
		register_activation_hook( 'wordpress-beacon/wordpress-beacon.php', [ 'Eighteen73\Beacon\Cron', 'job_activation' ] );
		register_deactivation_hook( 'wordpress-beacon/wordpress-beacon.php', [ 'Eighteen73\Beacon\Cron', 'job_deactivation' ] );

		add_action( 'run_checks_event', [ 'Eighteen73\Beacon\Cron', 'run_checks' ], 10, 2 );
	}

	/**
	 * Get the scheduled task's interval (in seconds). Defaults to three hours.
	 *
	 * @return int
	 */
	public static function get_interval(): ?int {
		if ( defined( 'BEACON_INTERVAL' ) && is_integer( BEACON_INTERVAL ) ) {
			return BEACON_INTERVAL;
		}
		return 10800;
	}

	/**
	 * Get the URL that this data will be POSTed to
	 *
	 * @return string|null
	 */
	public static function get_remote_url(): ?string {
		if ( defined( 'BEACON_URL' ) && BEACON_URL ) {
			return BEACON_URL;
		}
		return null;
	}

	/**
	 * Add a custom schedule for this task
	 *
	 * @param array $schedules Cron schedules
	 * @return array
	 */
	public static function job_schedule( array $schedules ): array {
		$schedules['beacon_schedule'] = [
			'interval' => self::get_interval(),
			'display'  => __( 'Every 3 hours' ),
		];
		return $schedules;
	}

	/**
	 * Add the schedule event on activation
	 *
	 * @return void
	 */
	public static function job_activation() {
		if ( ! wp_next_scheduled( 'run_checks_event' ) ) {
			wp_schedule_event( time(), 'beacon_schedule', 'run_checks_event' );
		}
	}

	/**
	 * Delete the scheduled event on deactivation
	 *
	 * @return void
	 */
	public static function job_deactivation() {
		wp_clear_scheduled_hook( 'run_checks_event' );
	}

	/**
	 * Run the checks and send the result to the external service
	 *
	 * @return array|bool
	 */
	public static function run_checks() {
		$data = Checks::run();

		$remote_url = self::get_remote_url();
		if ( ! $remote_url ) {
			error_log( 'Failed run wordpress-beacon: Missing BEACON_URL' );
			return [
				'invalid_configuration' => [ 'BEACON_URL is not defined' ],
			];
		}

		$headers = [
			'Accept'            => 'application/json',
			'Content-Type'      => 'application/json',
			'X-Beacon-Hostname' => $data['technical']['web']['domain'],
		];

		// Allow the headers to be modified
		$headers = apply_filters( 'beacon_headers', $headers );

		$response = wp_remote_post(
			$remote_url,
			[
				'body' => [
					'headers' => $headers,
					'body'    => wp_json_encode( $data ),
				],
			]
		);

		if ( $response instanceof WP_Error ) {
			$error = wp_json_encode( $response->errors );
			error_log( "Failed run wordpress-beacon: {$error}" );
			return $response->errors;
		}

		return true;
	}
}
