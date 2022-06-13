<?php
/**
 * Schedules this plugin's tasks
 *
 * @package ConditionReport
 */

namespace Eighteen73\ConditionReport;

use WP_Error;

/**
 * Schedules this plugin's tasks
 */
class Cron {

	/**
	 * Set up the cron control
	 */
	public static function setup() {
		add_filter( 'cron_schedules', [ 'Eighteen73\ConditionReport\Cron', 'job_schedule' ] );

		// Note this can't be run if it's converted to a mu-plugin
		register_activation_hook( 'wordpress-condition-report/wordpress-condition-report.php', [ 'Eighteen73\ConditionReport\Cron', 'job_activation' ] );
		register_deactivation_hook( 'wordpress-condition-report/wordpress-condition-report.php', [ 'Eighteen73\ConditionReport\Cron', 'job_deactivation' ] );

		add_action( 'run_checks_event', [ 'Eighteen73\ConditionReport\Cron', 'run_checks' ], 10, 2 );
	}

	/**
	 * Get the scheduled task's interval (in seconds). Defaults to three hours.
	 *
	 * @return int
	 */
	public static function get_interval(): ?int {
		if ( defined( 'CONDITION_REPORT_INTERVAL' ) && is_integer( CONDITION_REPORT_INTERVAL ) ) {
			return CONDITION_REPORT_INTERVAL;
		}
		return 10800;
	}

	/**
	 * Get the URL that this data will be POSTed to
	 *
	 * @return string|null
	 */
	public static function get_remote_url(): ?string {
		if ( defined( 'CONDITION_REPORT_URL' ) && CONDITION_REPORT_URL ) {
			return CONDITION_REPORT_URL;
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
		$schedules['condition_report_schedule'] = [
			'interval' => self::get_interval(),
			'display' => __( 'Every 3 hours' ),
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
			wp_schedule_event( time(), 'condition_report_schedule', 'run_checks_event' );
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
			error_log( 'Failed run wordpress-condition-report: Missing CONDITION_REPORT_URL' );
			return [
				'invalid_configuration' => [ 'CONDITION_REPORT_URL is not defined' ],
			];
		}

		$headers = [
			'Accept' => 'application/json',
			'Content-Type' => 'application/json',
			'X-Condition-Report-Hostname' => $data['technical']['web']['domain'],
		];

		// Allow the headers to be modified
		$headers = apply_filters( 'condition_report_headers', $headers );

		$response = wp_remote_post(
			$remote_url,
			[
				'body' => [
					'headers' => $headers,
					'body' => json_encode( $data ),
				],
			]
		);

		if ( $response instanceof WP_Error ) {
			$error = json_encode( $response->errors );
			error_log( "Failed run wordpress-condition-report: {$error}" );
			return $response->errors;
		}

		return true;
	}
}
