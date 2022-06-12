<?php
/**
 * Custom actions for manual interaction
 *
 * @package WebsiteMonitor
 */

namespace Eighteen73\WebsiteMonitor;

/**
 * Custom actions for manual interaction
 */
class Actions {

	/**
	 * Set up the actions
	 */
	public static function setup() {
		add_filter( 'plugin_action_links', [ 'Eighteen73\WebsiteMonitor\Actions', 'add_links' ], 10, 5 );
		add_action( 'wp_ajax_website_monitor_send_data', [ 'Eighteen73\WebsiteMonitor\Actions', 'send_data' ] );
		add_action( 'wp_ajax_website_monitor_copy_data', [ 'Eighteen73\WebsiteMonitor\Actions', 'copy_data' ] );
	}

	/**
	 * Add action links to the plugins page
	 *
	 * @param array  $actions Plugin actions
	 * @param string $plugin_file The plugin file
	 * @return array|string[]
	 */
	public static function add_links( array $actions, string $plugin_file ): array {
		if ( $plugin_file === 'website-monitor/website-monitor.php' ) {
			$settings = [
				'send_data' => '<a href="javascript:jQuery.post( ajaxurl, { \'action\': \'website_monitor_send_data\' }, function(data) { data = JSON.parse(data); console.log(data); if (data.success) { alert(\'The monitoring server has been updated.\'); } else { alert(data.error_message); } })">' . __( 'Send data now', 'WebsiteMonitor' ) . '</a>',
				'copy_data' => '<a href="javascript:jQuery.post( ajaxurl, { \'action\': \'website_monitor_copy_data\' }, function(data) { navigator.permissions.query({name: \'clipboard-write\'}).then(result => { if (result.state == \'granted\' || result.state == \'prompt\') { navigator.clipboard.writeText(data).then(function() { alert(\'The JSON data is in your clipboard. You can now paste it into another application.\'); }, function() { alert(\'Cannot write to clipboard\'); }); } else { alert(\'Cannot write to clipboard\'); } }); })">' . __( 'Clipboard', 'WebsiteMonitor' ) . '</a>',
			];
			$actions = array_merge( $settings, $actions );
		}
		return $actions;
	}

	/**
	 * Respond to click that should send data to the remote service
	 *
	 * @return void
	 */
	public static function send_data() {
		$response = Cron::run_checks();
		$out = [
			'success' => $response === true,
			'error_message' => null,
		];
		if ( ! $out['success'] ) {
			// TODO Return a human-readable error message & advice
			$out['error_message'] = json_encode( $response );
		}
		echo json_encode( $out );
		wp_die();
	}

	/**
	 * Respond to click that should return the data for the clipboard
	 *
	 * @return void
	 */
	public static function copy_data() {
		echo json_encode( Checks::run(), JSON_PRETTY_PRINT );
		wp_die();
	}

}
