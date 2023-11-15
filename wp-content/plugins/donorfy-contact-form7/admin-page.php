<?php
/*
Plugin Name: Donorfy Contact Form 7 Integration
Description: Send Contact Form 7 data to Donorfy API.
Version: 1.0
Author: Abdul Hannan Danish
Author URI: https://www.abdulhannandanish.com
Contact: mrahdanish@gmail.com
*/
?>
<style>
    .wrap {
        max-width: 600px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        color: #333;
    }

    form {
        margin-top: 20px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #555;
    }

    input {
        width: 100%;
        padding: 8px;
        margin-bottom: 15px;
        box-sizing: border-box;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    button {
        background-color: #4caf50;
        color: #fff;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background-color: #45a049;
    }

    /* Optional: Add some spacing and styling for better readability */

    form label:first-child {
        margin-top: 0;
    }

    form input:last-child {
        margin-bottom: 0;
    }

</style>


<!-- admin-page.php -->
<div class="wrap">
    <h1>Donorfy Integration Settings</h1>
    <form id="donorfy-settings-form">
        <?php wp_nonce_field('donorfy_settings_nonce', 'donorfy_nonce'); ?>

        <label for="api_key">API Key:</label>
        <input type="text" name="api_key" id="api_key" value="<?php echo esc_attr(get_option('donorfy_api_key')); ?>"/>

        <label for="api_token">API Token:</label>
        <input type="text" name="api_token" id="api_token"
               value="<?php echo esc_attr(get_option('donorfy_api_token')); ?>"/>

        <button type="button" id="save-settings-button">Save Settings</button>
    </form>
</div>
