<?php
/**
 * Plugin Name: Apprise Integration
 * Description: Sends custom WordPress notifications through Apprise API.
 * Version: 1.1.0
 * Author: Fadi
 */

if (!defined('ABSPATH')) {
    exit;
}

function fadi_apprise_add_menu() {
    add_options_page(
        'Apprise Integration',
        'Apprise Integration',
        'manage_options',
        'apprise-integration',
        'fadi_apprise_settings_page'
    );
}
add_action('admin_menu', 'fadi_apprise_add_menu');

function fadi_apprise_register_settings() {
    register_setting(
        'fadi_apprise_settings',
        'fadi_apprise_api_url',
        array(
            'sanitize_callback' => 'esc_url_raw',
            'default' => 'http://host.docker.internal:8000'
        )
    );

    register_setting(
        'fadi_apprise_settings',
        'fadi_apprise_config_key',
        array(
            'sanitize_callback' => 'sanitize_text_field'
        )
    );
}
add_action('admin_init', 'fadi_apprise_register_settings');

function fadi_apprise_send($title, $message, $type = 'info') {
    $api_url = rtrim(
        get_option(
            'fadi_apprise_api_url',
            'http://host.docker.internal:8000'
        ),
        '/'
    );

    $config_key = trim(
        get_option('fadi_apprise_config_key', '')
    );

    if (empty($config_key)) {
        return new WP_Error(
            'missing_config_key',
            'Apprise Configuration ID is missing.'
        );
    }

    $allowed_types = array('info', 'success', 'warning', 'failure');

    if (!in_array($type, $allowed_types, true)) {
        $type = 'info';
    }

    $endpoint = $api_url . '/notify/' . rawurlencode($config_key);

    $response = wp_remote_post(
        $endpoint,
        array(
            'timeout' => 20,
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body' => wp_json_encode(
                array(
                    'title' => $title,
                    'body'  => $message,
                    'type'  => $type
                )
            )
        )
    );

    if (is_wp_error($response)) {
        return $response;
    }

    $status_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    if ($status_code < 200 || $status_code >= 300) {
        return new WP_Error(
            'apprise_http_error',
            'Apprise returned HTTP ' . $status_code .
            '. Response: ' . $response_body
        );
    }

    return true;
}

function fadi_apprise_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $notice = '';

    if (
        isset($_POST['fadi_apprise_custom_send']) &&
        check_admin_referer('fadi_apprise_custom_action')
    ) {
        $title = isset($_POST['fadi_apprise_title'])
            ? sanitize_text_field(wp_unslash($_POST['fadi_apprise_title']))
            : '';

        $message = isset($_POST['fadi_apprise_message'])
            ? sanitize_textarea_field(wp_unslash($_POST['fadi_apprise_message']))
            : '';

        $type = isset($_POST['fadi_apprise_type'])
            ? sanitize_text_field(wp_unslash($_POST['fadi_apprise_type']))
            : 'info';

        if ($title === '' || $message === '') {
            $notice =
                '<div class="notice notice-error"><p>' .
                'Title and message are required.' .
                '</p></div>';
        } else {
            $result = fadi_apprise_send(
                $title,
                $message,
                $type
            );

            if (is_wp_error($result)) {
                $notice =
                    '<div class="notice notice-error"><p>' .
                    esc_html($result->get_error_message()) .
                    '</p></div>';
            } else {
                $notice =
                    '<div class="notice notice-success"><p>' .
                    'Notification sent successfully.' .
                    '</p></div>';
            }
        }
    }
    ?>
    <div class="wrap">
        <h1>Apprise Integration</h1>

        <?php echo wp_kses_post($notice); ?>

        <form method="post" action="options.php">
            <?php settings_fields('fadi_apprise_settings'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="fadi_apprise_api_url">
                            Apprise API URL
                        </label>
                    </th>
                    <td>
                        <input
                            id="fadi_apprise_api_url"
                            name="fadi_apprise_api_url"
                            type="text"
                            class="regular-text"
                            value="<?php
                                echo esc_attr(
                                    get_option(
                                        'fadi_apprise_api_url',
                                        'http://host.docker.internal:8000'
                                    )
                                );
                            ?>"
                        >
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="fadi_apprise_config_key">
                            Apprise Configuration ID
                        </label>
                    </th>
                    <td>
                        <input
                            id="fadi_apprise_config_key"
                            name="fadi_apprise_config_key"
                            type="text"
                            class="regular-text"
                            value="<?php
                                echo esc_attr(
                                    get_option(
                                        'fadi_apprise_config_key',
                                        ''
                                    )
                                );
                            ?>"
                        >
                    </td>
                </tr>
            </table>

            <?php submit_button('Save Settings'); ?>
        </form>

        <hr>

        <h2>Send Custom Notification</h2>

        <form method="post">
            <?php wp_nonce_field('fadi_apprise_custom_action'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="fadi_apprise_title">
                            Title
                        </label>
                    </th>
                    <td>
                        <input
                            id="fadi_apprise_title"
                            name="fadi_apprise_title"
                            type="text"
                            class="regular-text"
                            required
                        >
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="fadi_apprise_message">
                            Message
                        </label>
                    </th>
                    <td>
                        <textarea
                            id="fadi_apprise_message"
                            name="fadi_apprise_message"
                            rows="6"
                            class="large-text"
                            required
                        ></textarea>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="fadi_apprise_type">
                            Notification Type
                        </label>
                    </th>
                    <td>
                        <select
                            id="fadi_apprise_type"
                            name="fadi_apprise_type"
                        >
                            <option value="info">Info</option>
                            <option value="success">Success</option>
                            <option value="warning">Warning</option>
                            <option value="failure">Failure</option>
                        </select>
                    </td>
                </tr>
            </table>

            <p>
                <input
                    type="submit"
                    name="fadi_apprise_custom_send"
                    class="button button-primary"
                    value="Send Notification"
                >
            </p>
        </form>
    </div>
    <?php
}
