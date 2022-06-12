<?php
/**
 * Plugin Name:     eighteen73 Condition Report
 * Plugin URI:      https://github.com/eighteen73/wordpress-condition-report
 * Description:     Sends non-PII website information to our monitor for support purposes. Refer to the plugin's readme for what's included.
 * Author:          eighteen73
 * Author URI:      https://eighteen73.co.uk
 * Text Domain:     wordpress-condition-report
 * Version:         1.0.0
 *
 * @package         ConditionReport
 */

namespace Eighteen73\ConditionReport;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

Actions::setup();
Cron::setup();
