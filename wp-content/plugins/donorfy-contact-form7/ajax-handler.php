<?php
/*
Plugin Name: Donorfy Contact Form 7 Integration
Description: Send Contact Form 7 data to Donorfy API.
Version: 1.0
Author: Abdul Hannan Danish
Author URI: https://www.abdulhannandanish.com
Contact: mrahdanish@gmail.com
*/

function save_donorfy_settings_ajax() {
    check_ajax_referer('donorfy_settings_nonce', 'security');

    $api_key = sanitize_text_field($_POST['api_key']);
    $api_token = sanitize_text_field($_POST['api_token']);

    // Save the values to options
    update_option('donorfy_api_key', $api_key);
    update_option('donorfy_api_token', $api_token);

    // You can send a response if needed
    wp_send_json_success('Settings saved successfully');
}
add_action('wp_ajax_save_donorfy_settings', 'save_donorfy_settings_ajax');
