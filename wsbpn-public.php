<?php

defined('ABSPATH') or die('This page may not be accessed directly.');

class WSBPN_Public {

    private static $RESOURCES_VERSION = '40';

    public function __construct() {
        
    }

    public static function init() {
        add_action('wp_head', array(__CLASS__, 'wsbpn_header'), 10);
    }

    public static function insert_header_manifest($wsbpn_wp_settings, $current_plugin_url) {
        if ($wsbpn_wp_settings !== false && array_key_exists('gcm_sender_id', $wsbpn_wp_settings)) {
            $gcm_sender_id = $wsbpn_wp_settings['gcm_sender_id'];
        } else {
            $gcm_sender_id = 'WORDPRESS_NO_SENDER_ID_ENTERED';
        }
        echo '<link rel="manifest" href="' . $current_plugin_url . 'sdk_files/manifest.json.php?gcm_sender_id=' . $gcm_sender_id . '"/>';
        echo '<link rel="manifest" href="' . $current_plugin_url . 'sdk_files/manifest.json.php?gcm_sender_id=' . $gcm_sender_id . '"/>';
        wp_enqueue_style('wsbpn', 'https://cdn.worldshaking.com/bpn/wsbpn.css', false, WSBPN_Public::$RESOURCES_VERSION);
    }

    // For easier debugging of sites by identifying them as WordPress
    public static function insert_stamp() {
        echo '<meta name="worldshaking" content="wordpress-plugin"/>' . "\r\n";
        echo '<link rel="dns-prefetch" href="//cdn.worldshaking.com" />' . "\r\n";
    }

    public static function wsbpn_header() {
        $wsbpn_wp_settings = WSBPN::get_wsbpn_settings();
        $current_plugin_url = WSBPN_PLUGIN_URL;
        WSBPN_Public::insert_stamp();
        WSBPN_Public::insert_header_manifest($wsbpn_wp_settings, $current_plugin_url);
        wp_enqueue_script("wsbpnjs", "https://cdn.worldshaking.com/bpn/wsbpn.js", array(), "1.1.1", false);
        $dialog = empty($wsbpn_wp_settings["use_slidedown_permission_message_for_https"]) ? "false" : "true";
        $debugmode = !empty($wsbpn_wp_settings["debug_mode"]) ? 'true' : "false";
        $inline = 'var wsbpn_options = { debug: ' . $debugmode . ', ws_app_id : "' . $wsbpn_wp_settings["app_id"]
                . '", ws_app_url : "' . trim(get_home_url(), "/")
                . '", ws_app_title : "' . get_bloginfo('name')
                . '", show_dialog : ' . $dialog
                . ', dialog_message : "' . $wsbpn_wp_settings["permission_message_for_https"]
                . '", dialog_okbutton : "' . $wsbpn_wp_settings["permission_message_okbutton_for_https"]
                . '", dialog_okbutton_color : "' . $wsbpn_wp_settings["permission_message_okbuttoncolor_for_https"]
                . '", dialog_cancelbutton : "' . $wsbpn_wp_settings["permission_message_cancelbutton_for_https"]
                . '", service_worker : "' . $current_plugin_url . 'sdk_files/WSBPNWorker.js.php" };';
        //$inline .= ' setTimeout(function(){ wsbpn.subscribe(); }, 3000);';
        wp_add_inline_script('wsbpnjs', $inline, 'before');
    }

}
