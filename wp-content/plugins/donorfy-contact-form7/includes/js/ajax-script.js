jQuery(document).ready(function ($) {
    $('#save-settings-button').on('click', function () {
        var api_key = $('#api_key').val();
        var api_token = $('#api_token').val();
        var security = $('#donorfy_nonce').val();

        // Make AJAX request
        $.ajax({
            type: 'POST',
            url: donorfy_ajax_object.ajax_url,
            data: {
                action: 'save_donorfy_settings',
                security: security,
                api_key: api_key,
                api_token: api_token
            },
            success: function (response) {
                if (response.success) {
                    alert('Settings saved successfully');
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
});

