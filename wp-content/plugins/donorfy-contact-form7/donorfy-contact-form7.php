<?php
/*
Plugin Name: Donorfy Contact Form 7 Integration
Description: Send Contact Form 7 data to Donorfy API.
Version: 1.0
Author: Abdul Hannan Danish
Author URI: https://www.abdulhannandanish.com
Contact: mrahdanish@gmail.com
*/

// Include the DonorfyAdminMenu class
include_once(plugin_dir_path(__FILE__) . 'DonorfyAdminMenu.php');

// Create an instance of DonorfyAdminMenu to handle the admin menu
$donorfy_admin_menu = new DonorfyAdminMenu();

// Hook the function to Contact Form 7's submission event
add_action('wpcf7_mail_sent', 'send_data_to_donorfy_api');

// Retrieve API key and access token


include(plugin_dir_path(__FILE__) . 'ajax-handler.php');

// Enqueue AJAX script
function enqueue_ajax_script() {
    wp_enqueue_script('donorfy-ajax-script', plugin_dir_url(__FILE__) . '/includes/js/ajax-script.js', array('jquery'), '1.0', true);
    wp_localize_script('donorfy-ajax-script', 'donorfy_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('admin_enqueue_scripts', 'enqueue_ajax_script');

// Plugin code will go here
function send_data_to_donorfy_api($cf7) {
    // Check if the form ID matches the one you want to connect
    if (true == true) {
        try {
            // Get the form submission data
            $submission = WPCF7_Submission::get_instance();
            if (!$submission) {
                throw new Exception('Failed to get form submission data.');
            }

            $form_data = $submission->get_posted_data();

            // Prepare the data to send to Donorfy API
            $data_to_send = array(
                'FirstName' => $form_data['first-name'], // Replace 'field1' with your form field names
                'LastName' => $form_data['last-name'],
                'EmailAddress' => $form_data['your-email'],
                'ConstituentType' => $form_data['type'],
                // Add more fields as needed
            );

            // Convert data to JSON format
            $json_data = json_encode($data_to_send);

            // Perform the API request to Donorfy
            $api_response = send_data_to_donorfy($json_data);

            $constituentId = get_constituent_id($api_response);

            // Log the API response
            error_log('Donorfy Constituent API Response: ' . $api_response);

            $campaign_name = "Test Campaign1";
            $data_for_campaign = array(
                "LookUpDescription"=> $campaign_name
            );

            // Convert data to JSON format
            $campaign_data = json_encode($data_for_campaign);

            // Perform the API request to Donorfy
            $campaign_api_response = create_campaign_donorfy($campaign_data);

            $message_from_response = get_message_from_response($campaign_api_response);

            // Log the API response
            error_log('Donorfy API Response: ' . $campaign_api_response);

            $amount = 654;

            $response_from_transaction = create_transaction_donorfy($campaign_name, $constituentId, $amount);

            // Log relevant information
            error_log('Message from Response: ' . $message_from_response . ' Constituent Id: ' . $constituentId . ' Amount: ' . $amount);
            error_log('Response from Transaction: ' . $response_from_transaction);

        } catch (Exception $e) {
            // Log any exceptions
            error_log('Exception: ' . $e->getMessage());
        }
    }
}


function send_data_to_donorfy($data) {
    $api_key = get_option('donorfy_api_key');
    $api_token = get_option('donorfy_api_token');
    // Set the API endpoint URL
    $api_url = 'https://data.donorfy.com/api/v1/'.$api_key.'/constituents'; // Replace with the actual API endpoint

    // Set the request parameters
    $args = array(
        'body' => $data,
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '.$api_token, // Replace with your API key
        ),
        'timeout' => 10
    );

    // Make the API request
    $response = wp_remote_post($api_url, $args);

    // Check for errors and return the response
    if (is_wp_error($response)) {
        return 'Error: ' . $response->get_error_message();
    } else {
        return wp_remote_retrieve_body($response);
    }
}

function create_campaign_donorfy($data){
    $api_key = get_option('donorfy_api_key');
    $api_token = get_option('donorfy_api_token');
    // Set the API endpoint URL
    $api_url = 'https://data.donorfy.com/api/v1/'.$api_key.'/System/LookUpTypes/Campaigns'; // Replace with the actual API endpoint

    // Set the request parameters
    $args = array(
        'body' => $data,
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '.$api_token, // Replace with your API key
        ),
    );

    // Make the API request
    $response = wp_remote_post($api_url, $args);

    // Check for errors and return the response
    if (is_wp_error($response)) {
        return 'Error: ' . $response->get_error_message();
    } else {
        return wp_remote_retrieve_body($response);
    }

}

function get_constituent_id($jsonResponse){

    // $jsonResponse = '{"Message":null,"Id":"7cf12cfb-fd82-ee11-a81c-002248a1c853","ConstituentId":"7cf12cfb-fd82-ee11-a81c-002248a1c853"}';

    // Decode the JSON response
    $responseArray = json_decode($jsonResponse, true);

    // Check if decoding was successful
    if ($responseArray !== null) {
        // Access the ConstituentId
        $constituentId = $responseArray['ConstituentId'];

        return $constituentId;
    } else {
        // Handle decoding error
        return "Error decoding JSON response";
    }

}

function create_transaction_donorfy($campaign_name, $constituentId, $amount){

    $api_key = get_option('donorfy_api_key');
    $api_token = get_option('donorfy_api_token');
    // Prepare the data to send to Donorfy API
    $data_to_send = array(
        "Product"=> "Donation",
        "Quantity"=> 1,
        "Fund"=> "General (unrestricted)",
        "Campaign"=> $campaign_name,
        "PaymentMethod"=> "Payment Card",
        "Amount"=> $amount,
        "ProcessingCostsAmount"=> 8,
        "DatePaid"=> "2023-11-03T14:41:13.456Z",
        "ConstituentType"=> "Individual",
        "ExistingConstituentId" => $constituentId
    );
    
    // Convert data to JSON format
    $json_data = json_encode($data_to_send);

    // Set the API endpoint URL
    $api_url = 'https://data.donorfy.com/api/v1/'.$api_key.'/transactions'; // Replace with the actual API endpoint

    // Set the request parameters
    $args = array(
        'body' => $json_data,
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '. $api_token, // Replace with your API key
        ),
        'timeout' => 10
    );

    // Make the API request
    $response = wp_remote_post($api_url, $args);

    // Check for errors and return the response
    if (is_wp_error($response)) {
        return 'Error: ' . $response->get_error_message();
    } else {
        return wp_remote_retrieve_body($response);
    }
}

function get_message_from_response($jsonResponse){

    // $jsonResponse = '{"Message":"Setting value already exists"}';

    // Decode the JSON response
    $responseArray = json_decode($jsonResponse, true);

    // Check if decoding was successful
    if ($responseArray !== null) {
        // Access the "Message"
        $message = $responseArray['Message'];

        // Use the "Message" as needed
        return $message;
    } else {
        // Handle decoding error
        return "Error decoding JSON response";
    }
}