<?php
/*
Plugin Name: Donorfy Contact Form 7 Integration
Description: Send Contact Form 7 data to Donorfy API.
Version: 1.0
Author: Abdul Hannan Danish
Author URI: https://www.abdulhannandanish.com
Contact: mrahdanish@gmail.com
*/

class DonorfyAdminMenu {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu'));
        add_action('admin_post_save_donorfy_settings', array($this, 'save_settings'));

    }

    public function add_menu() {
        add_menu_page(
            'Donorfy Integration Settings',
            'Donorfy Settings',
            'manage_options',
            'donorfy-settings',
            array($this, 'render_admin_page'),
            'dashicons-admin-generic'
        );
    }

    public function render_admin_page() {
        include(plugin_dir_path(__FILE__) . 'admin-page.php');
    }

    public function save_settings() {
        error_log('Saving settings...');
        error_log('Save settings function called');
        if (isset($_POST['api_key'])) {
            update_option('donorfy_api_key', sanitize_text_field($_POST['api_key']));
        }
    
        if (isset($_POST['api_token'])) {
            update_option('donorfy_api_token', sanitize_text_field($_POST['api_token']));
        }
    }    
}
