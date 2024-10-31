<?php

defined( 'ABSPATH' ) or die('This page may not be accessed directly.');

if (!WSBPNUtils::can_modify_plugin_settings()) {
  // Exit if the current user does not have permission
  die('Insufficient permissions to access config page.');
}

// If the user is trying to save the form, require a valid nonce or die
if (array_key_exists('app_id', $_POST)) {
  // check_admin_referer dies if not valid; no if statement necessary
  check_admin_referer(WSBPN_Admin::$SAVE_CONFIG_NONCE_ACTION, WSBPN_Admin::$SAVE_CONFIG_NONCE_KEY);
  $wsbpn_wp_settings = WSBPN_Admin::save_config_page($_POST);
}

// The user is just viewing the config page; this page cannot be accessed directly
$wsbpn_wp_settings = WSBPN::get_wsbpn_settings();
?>


<div class="outer site wsbpn container">
    <div class="ui site wsbpn container" id="content-container">
        <form class="ui form" action="#" method="POST">
            <div class="ui pointing stackable menu">
              <a class="active item" data-tab="configuration">Configuration</a>
            </div>
            <div class="ui borderless shadowless active tab segment" style="z-index: 1; padding-top: 0; padding-bottom: 0;" data-tab="configuration">
                <div class="ui special padded raised stack segment">
                  <form class="ui form" role="configuration" action="#" method="POST">
                    <?php
                        // Add an nonce field so we can check for it later.
                        wp_nonce_field(WSBPN_Admin::$SAVE_CONFIG_NONCE_ACTION, WSBPN_Admin::$SAVE_CONFIG_NONCE_KEY, true);
                    ?>
                    <div class="ui dividing header">
                      <i class="setting icon"></i>
                      <div class="content">
                        Account Settings
                      </div>
                    </div>
                    <div class="ui borderless shadowless segment">
                      <div class="field">
                        <label>App ID</label>
                        <input type="text" name="app_id" placeholder="xxxxxxxx" value="<?php echo $wsbpn_wp_settings['app_id'] ?>">
                      </div>
                    </div>
                    <div class="ui borderless shadowless segment">
                      <div class="ui toggle checkbox">
                        <input type="checkbox" name="debug_mode" value="true" <?php if (array_key_exists('debug_mode', $wsbpn_wp_settings) && $wsbpn_wp_settings['debug_mode']) { echo "checked"; } ?>>
                        <label>Debug Mode</label>
                      </div>
                    </div>                       
                    <div class="ui dividing header">
                      <i class="setting icon"></i>
                      <div class="content">
                        Dialog
                      </div>
                    </div>                  
                    <div class="ui borderless shadowless segment">
                      <div class="ui toggle checkbox">
                        <input type="checkbox" name="use_slidedown_permission_message_for_https" value="true" <?php if (array_key_exists('use_slidedown_permission_message_for_https', $wsbpn_wp_settings) && $wsbpn_wp_settings['use_slidedown_permission_message_for_https']) { echo "checked"; } ?>>
                        <label>Show the slidedown permission message before prompting users to subscribe</label>
                      </div>
                    </div>                      
                    <div class="ui borderless shadowless segment">
                      <div class="field">
                        <label>Permission Message</label>
                        <input type="text" name="permission_message_for_https" placeholder="" value="<?php echo $wsbpn_wp_settings['permission_message_for_https'] ?>">
                      </div>
                    </div>
                    <div class="ui borderless shadowless segment">
                      <div class="field">
                        <label>Button Color</label>
                        <input type="text" id="permission_message_okbuttoncolor_for_https" name="permission_message_okbuttoncolor_for_https" placeholder="" value="<?php echo $wsbpn_wp_settings['permission_message_okbuttoncolor_for_https'] ?>">
                      </div>
                    </div>                       
                    <div class="ui borderless shadowless segment">
                      <div class="field">
                        <label>OK Button Text</label>
                        <input type="text" name="permission_message_okbutton_for_https" placeholder="" value="<?php echo $wsbpn_wp_settings['permission_message_okbutton_for_https'] ?>">
                      </div>
                    </div>   
                    <div class="ui borderless shadowless segment">
                      <div class="field">
                        <label>Cancel Button Text</label>
                        <input type="text" name="permission_message_cancelbutton_for_https" placeholder="" value="<?php echo $wsbpn_wp_settings['permission_message_cancelbutton_for_https'] ?>">
                      </div>
                    </div>   
                    <button class="ui large teal button" type="submit">Save</button>
                </div>
            </div>
        </form>            
    </div>
</div>
