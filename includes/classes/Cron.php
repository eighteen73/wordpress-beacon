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
	 * How often should be data be sent (in seconds)
	 */
	const CRON_INTERVAL = 10800;

	/**
	 * The URL where should the data should be sent
	 */
	const REMOTE_POST_URL = 'https://hub.eighteen73.co.uk/api/condition-report';

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
	 * Add a custom schedule for this task
	 *
	 * @param array $schedules Cron schedules
	 * @return array
	 */
	public static function job_schedule( array $schedules ): array {
		$schedules['condition_report_schedule'] = [
			'interval' => self::CRON_INTERVAL,
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
		$response = wp_remote_post(
			self::REMOTE_POST_URL,
			[
				'body' => [
					'headers' => [
						'Accept' => 'application/json',
						'Content-Type' => 'application/json',
						'X-Condition-Report' => $data['technical']['web']['domain'],
					],
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
