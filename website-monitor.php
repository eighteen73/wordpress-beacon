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

Actions::setup();
Cron::setup();
