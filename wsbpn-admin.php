<?php
defined('ABSPATH') or die('This page may not be accessed directly.');

function wsbpn_change_footer_admin() {
    return '';
}

class WSBPN_Admin {

    /**
     * Increment $RESOURCES_VERSION any time the CSS or JavaScript changes to view the latest changes.
     */
    private static $RESOURCES_VERSION = '40';
    private static $SAVE_POST_NONCE_KEY = 'wsbpn_meta_box_nonce';
    private static $SAVE_POST_NONCE_ACTION = 'wsbpn_meta_box';
    public static $SAVE_CONFIG_NONCE_KEY = 'wsbpn_config_page_nonce';
    public static $SAVE_CONFIG_NONCE_ACTION = 'wsbpn_config_page';

    public function __construct() {
        
    }

    public static function init() {
        $wsbpn = new self();

        if (class_exists('WDS_Log_Post')) {

            function exception_error_handler($errno, $errstr, $errfile, $errline) {
                try {
                    switch ($errno) {
                        case E_USER_ERROR:
                            exit(1);
                            break;

                        case E_USER_WARNING:
                            break;

                        case E_USER_NOTICE || E_NOTICE:
                            break;

                        case E_STRICT:
                            break;

                        default:
                            break;
                    }

                    return true;
                } catch (Exception $ex) {
                    return true;
                }
            }

            set_error_handler("exception_error_handler");

            function fatal_exception_error_handler() {
                $error = error_get_last();
                try {
                    switch ($error['type']) {
                        case E_ERROR:
                        case E_CORE_ERROR:
                        case E_COMPILE_ERROR:
                        case E_USER_ERROR:
                        case E_RECOVERABLE_ERROR:
                        case E_CORE_WARNING:
                        case E_COMPILE_WARNING:
                        case E_PARSE:
                            exit;
                    }
                } catch (Exception $ex) {
                    return true;
                }
            }

            register_shutdown_function('fatal_exception_error_handler');
        }

        if (WSBPNUtils::can_modify_plugin_settings()) {
            add_action('admin_menu', array(__CLASS__, 'add_admin_page'));
        }

        add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_styles'));
        return $wsbpn;
    }

    public static function admin_styles() {
        
    }

    public static function save_config_page($config) {
        if (!WSBPNUtils::can_modify_plugin_settings()) {
            set_transient('wsbpn_transient_error', '<div class="error notice wsbpn-error-notice">
                    <p><strong>WSBPN Push:</strong><em> Only administrators are allowed to save plugin settings.</em></p>
                </div>', 86400);
            return;
        }

        $sdk_dir = plugin_dir_path(__FILE__) . 'sdk_files/';
        $wsbpn_wp_settings = WSBPN::get_wsbpn_settings();
        $new_app_id = $config['app_id'];

        // Validate the UUID
        if (preg_match('/([0-9]{7,8})/', $new_app_id, $m)) {
            $wsbpn_wp_settings['app_id'] = $new_app_id;
        }

        $booleanSettings = array('use_slidedown_permission_message_for_https', 'debug_mode');
        WSBPN_Admin::saveBooleanSettings($wsbpn_wp_settings, $config, $booleanSettings);

        $stringSettings = array("permission_message_for_https", "permission_message_okbutton_for_https", "permission_message_cancelbutton_for_https", "permission_message_okbuttoncolor_for_https");

        WSBPN_Admin::saveStringSettings($wsbpn_wp_settings, $config, $stringSettings);

        WSBPN::save_wsbpn_settings($wsbpn_wp_settings);
        return $wsbpn_wp_settings;
    }

    public static function saveBooleanSettings(&$wsbpn_wp_settings, &$config, $settings) {
        foreach ($settings as $setting) {
            if (array_key_exists($setting, $config)) {
                $wsbpn_wp_settings[$setting] = true;
            } else {
                $wsbpn_wp_settings[$setting] = false;
            }
        }
    }

    public static function saveStringSettings(&$wsbpn_wp_settings, &$config, $settings) {
        foreach ($settings as $setting) {
            if (array_key_exists($setting, $config)) {
                $value = $config[$setting];
                $normalized_value = WSBPNUtils::normalize($value);
                $wsbpn_wp_settings[$setting] = $normalized_value;
            }
        }
    }

    public static function add_admin_page() {
        $WSBPN_menu = add_menu_page('Web Push', 'Web Push', 'manage_options', 'web-push', array(__CLASS__, 'admin_menu')
        );

        add_action('load-' . $WSBPN_menu, array(__CLASS__, 'admin_custom_load'));
    }

    public static function admin_menu() {
        require_once( plugin_dir_path(__FILE__) . '/views/config.php' );
    }

    public static function admin_custom_load() {
        add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_custom_scripts'));

        $wsbpn_wp_settings = WSBPN::get_wsbpn_settings();
        if (
                $wsbpn_wp_settings['app_id'] == ''
        ) {

            function admin_notice_setup_not_complete() {
                ?>
                <div class="error notice wsbpn-error-notice">
                    <p><strong>WS Push:</strong> <em>Your setup is not complete. Please follow the Setup guide to set up web push notifications. The App ID must be filled in.</em></p>
                </div>
                <?php
            }

            add_action('admin_notices', 'admin_notice_setup_not_complete');
        }

        if (!function_exists('curl_init')) {

            function admin_notice_curl_not_installed() {
                ?>
                <div class="error notice wsbpn-error-notice">
                    <p><strong>WS Push:</strong> <em>cURL is not installed on this server. cURL is required to send notifications. Please make sure cURL is installed on your server before continuing.</em></p>
                </div>
                <?php
            }

            add_action('admin_notices', 'admin_notice_curl_not_installed');
        }
    }

    public static function admin_custom_scripts() {
        add_filter('admin_footer_text', 'wsbpn_change_footer_admin', 9999); // 9999 means priority, execute after the original fn executes

        wp_enqueue_style('icons', plugin_dir_url(__FILE__) . 'views/css/icons.css', false, WSBPN_Admin::$RESOURCES_VERSION);
        wp_enqueue_style('semantic-ui', plugin_dir_url(__FILE__) . 'views/css/semantic-ui.css', false, WSBPN_Admin::$RESOURCES_VERSION);
        wp_enqueue_style('site', plugin_dir_url(__FILE__) . 'views/css/site.css', false, WSBPN_Admin::$RESOURCES_VERSION);
        wp_enqueue_script('semantic-ui', plugin_dir_url(__FILE__) . 'views/javascript/semantic-ui.js', false, WSBPN_Admin::$RESOURCES_VERSION);

    }

}
