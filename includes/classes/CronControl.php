<?php
/**
 * Schedules this plugin's tasks
 *
 * @package WordPressDiagnostics
 */

namespace Eighteen73\WordPressDiagnostics;

/**
 * Schedules this plugin's tasks
 */
class CronControl extends Singleton {

	/**
	 * Setup the cron control
	 */
	public function setup() {
		add_filter( 'cron_schedules', [ $this, 'job_schedule' ] );

		// Note this can't be run if it's converted to a mu-plugin
		register_activation_hook( 'wordpress-diagnostics/wordpress-diagnostics.php', [ $this, 'job_activation' ] );
		register_deactivation_hook( 'wordpress-diagnostics/wordpress-diagnostics.php', [ $this, 'job_deactivation' ] );

		add_action( 'run_checks_event', [ $this, 'run_checks' ], 10, 2 );
	}

	public function job_schedule($schedules)
	{
		$schedules['diagnostics_schedule'] = array(
			'interval' => 10800,
			'display' => __('Every 3 hours')
		);
		return $schedules;
	}

	public function job_activation()
	{
		if ( ! wp_next_scheduled( 'run_checks_event' ) ) {
			wp_schedule_event( time(), 'diagnostics_schedule', 'run_checks_event' );
		}
	}

	public function job_deactivation()
	{
		wp_clear_scheduled_hook( 'run_checks_event' );
	}

	public function run_checks()
	{
		$data = Checks::instance()->run();
		$response = wp_remote_post( 'https://hub.eighteen73.co.uk/api/website-diagnostics', [
			'body' => [
				'headers' => [
					'Accept' => 'application/json',
					'Content-Type' => 'application/json',
					'X-Diagnostics-Domain' => $data['technical']['web']['domain'],
				],
				'body' => json_encode($data),
			],
		] );

		if ($response instanceof \WP_Error) {
			$error = json_encode($response->errors);
			error_log("Failed to send wordpress-diagnostics: {$error}");
		}

	}
}
