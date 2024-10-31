<?php

defined('ABSPATH') or die('This page may not be accessed directly.');

class WSBPN {

    public static function get_wsbpn_settings() {
        /*
          During first-time setup, all the keys here will be created with their
          default values, except for keys with value 'CALCULATE_LEGACY_VALUE' or
          'CALCULATE_SPECIAL_VALUE'. These special keys aren't created until further
          below.
         */
        $defaults = array(
            'app_id' => '',
            'debug_mode' => false,
            'use_slidedown_permission_message_for_https' => true,
            'permission_message_for_https' => "We'd like to show you notifications for the latest news and updates.",
            'permission_message_okbutton_for_https' => 'ALLOW',
            'permission_message_cancelbutton_for_https' => 'NO THANKS',
            'permission_message_okbuttoncolor_for_https' => '#4285f4'
        );

        // If not set or empty, load a fresh empty array
        if (!isset($wsbpn_wp_settings)) {
            $wsbpn_wp_settings = get_option("WSBPN_WPSetting");
            if (empty($wsbpn_wp_settings)) {
                $is_new_user = true;
                $wsbpn_wp_settings = array();
            }
        }

        // Assign defaults if the key doesn't exist in $wsbpn_wp_settings
        // Except for those with value CALCULATE_LEGACY_VALUE -- we need special logic for legacy values that used to exist in previous plugin versions
        reset($defaults);
        while (list($key, $value) = each($defaults)) {
            if (!array_key_exists($key, $wsbpn_wp_settings)) {
                $wsbpn_wp_settings[$key] = $value;
            }
        }

        return $wsbpn_wp_settings;
    }

    public static function save_wsbpn_settings($settings) {
        $wsbpn_wp_settings = $settings;
        update_option("WSBPN_WPSetting", $wsbpn_wp_settings);
    }

}

?>