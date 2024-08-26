<?php
/*
Plugin Name: WP Multiple Admin Emails
Plugin URI: https://github.com/kahnu044/wp-multiple-admin-emails
Description: WP Multiple Admin Emails extends WordPress’s default admin email settings by allowing multiple email addresses to receive notifications.
Version: 1.0.0
Author: kahnu044
Author URI: https://github.com/kahnu044
*/

// Register the settings and the field
function wp_mae_register_settings()
{
    // Register the setting with proper sanitization
    register_setting('general', 'wp_mae_multiple_admin_emails', array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_textarea_field',
        'default' => ''
    ));

    // Add the settings field
    add_settings_field(
        'wp_mae_multiple_admin_emails',
        'Additional Admin Emails',
        'wp_mae_settings_field_callback',
        'general',
        'default',
        array('label_for' => 'wp_mae_multiple_admin_emails')
    );
}
add_action('admin_init', 'wp_mae_register_settings');

// Callback function to render the settings field
function wp_mae_settings_field_callback()
{
    $value = get_option('wp_mae_multiple_admin_emails', '');
    echo '<textarea id="wp_mae_multiple_admin_emails" name="wp_mae_multiple_admin_emails" rows="5" cols="50" class="large-text code">' . esc_textarea($value) . '</textarea>';
    echo '<p class="description">Enter additional admin email addresses, one per line.</p>';
}

// Hook into wp_mail filter to add additional emails
function wp_mae_add_additional_emails_to_args($args)
{
    // Get the original admin email
    $admin_email = get_option('admin_email');

    // Get the additional admin emails from the settings
    $additional_emails = get_option('wp_mae_multiple_admin_emails', '');

    if (!empty($additional_emails)) {
        // Convert the textarea input into an array of emails
        $additional_emails_array = array_map('trim', explode("\n", $additional_emails));

        // If the email is going to the admin, add the additional emails
        if ($args['to'] === $admin_email) {
            $args['to'] = array_merge([$admin_email], $additional_emails_array);
        }
    }

    return $args;
}
add_filter('wp_mail', 'wp_mae_add_additional_emails_to_args', 10, 1);
