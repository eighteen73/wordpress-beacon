<?php
/**
 * Plugin Name:     eighteen73 Website Monitor
 * Plugin URI:      https://code.orphans.co.uk/packages/wordpress/website-monitor/
 * Description:     Sends non-PII website information to our monitor for support purposes.
 * Author:          eighteen73
 * Author URI:      https://eighteen73.co.uk
 * Text Domain:     website-monitor
 * Version:         1.0.0
 *
 * @package         WebsiteMonitor
 */

namespace Eighteen73\WebsiteMonitor;

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
			if ( 'WebsiteMonitor' === $package ) {
				require_once __DIR__ . '/includes/classes/' . implode( '/', $path_parts ) . '.php';
			}
		}
	}
);

CronControl::instance()->setup();

add_filter(
	'plugin_action_links',
	function ( $actions, $plugin_file ) {
		static $plugin;
		if ( ! isset( $plugin ) ) {
			$plugin = plugin_basename( __FILE__ );
		}
		if ( $plugin == $plugin_file ) {
			$settings = [
				'settings' => '<a href="javascript:jQuery.post( ajaxurl, { \'action\': \'website_monitor_data\' }, function(data) { navigator.permissions.query({name: \'clipboard-write\'}).then(result => { if (result.state == \'granted\' || result.state == \'prompt\') { navigator.clipboard.writeText(data).then(function() { alert(\'The JSON data is in your clipboard. You can now paste it into another application.\'); }, function() { alert(\'Cannot write to clipboard\'); }); } else { alert(\'Cannot write to clipboard\'); } }); })">' . __( 'Copy data to clipboard', 'WebsiteMonitor' ) . '</a>',
			];
			$actions = array_merge( $settings, $actions );
		}
		return $actions;
	},
	10,
	5
);

add_action(
	'wp_ajax_website_monitor_data',
	function() {
		echo json_encode( Checks::instance()->run(), JSON_PRETTY_PRINT );
		wp_die();
	}
);
