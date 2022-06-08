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

		// If it's not a mu-plugin
		// register_activation_hook( __FILE__, [ $this, 'job_activation' ] );
		// register_deactivation_hook( __FILE__, [ $this, 'job_deactivation' ] );

		add_action( 'run_checks_event', 'run_checks', 10, 2 );
	}

	public function job_schedule($schedules)
	{
		$schedules['diagnostics_schedule'] = array(
			'interval' => 1,
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
		// do something
	}
}
