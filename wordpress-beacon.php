<?php
/**
 * Plugin Name:     eighteen73 Beacon
 * Plugin URI:      https://github.com/eighteen73/wordpress-beacon
 * Description:     Sends non-PII website information to our monitor for support purposes. Refer to the plugin's readme for what's included.
 * Author:          eighteen73
 * Author URI:      https://eighteen73.co.uk
 * Text Domain:     wordpress-beacon
 * Version:         1.0.0
 *
 * @package         Beacon
 */

namespace Eighteen73\Beacon;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

Actions::setup();
Cron::setup();
