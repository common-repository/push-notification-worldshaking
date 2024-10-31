<?php

defined('ABSPATH') or die('This page may not be accessed directly.');

/**
 * Plugin Name: WorldShaking Push Notifications
 * Plugin URI: https://worldshaking.com/
 * Description: Web Push Notifications.
 * Version: 1.0.5
 * Author: Grizzly New Technologies
 * Author URI: https://grizzlynt.com
 * License: MIT
 */
define('WSBPN_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The number of seconds required to wait between requests.
 */

require_once( plugin_dir_path(__FILE__) . 'wsbpn-utils.php' ); 
require_once( plugin_dir_path(__FILE__) . 'wsbpn-admin.php' );
require_once( plugin_dir_path(__FILE__) . 'wsbpn-public.php' );
require_once( plugin_dir_path(__FILE__) . 'wsbpn-settings.php' );

add_action('init', array('WSBPN_Admin', 'init'));
add_action('init', array('WSBPN_Public', 'init'));
?>