<?php

include_once plugin_dir_path(__FILE__) . 'chatx-shortcode.php';
include_once plugin_dir_path(__FILE__) . 'chatx-admin.php';
include_once plugin_dir_path(__FILE__) . 'chatx-db.php';

/**
 * Enqueue scripts and styles for the plugin
 * @return void
 */
function chatx_enqueue_scripts() {
    // Enqueue your custom JavaScript file
    wp_enqueue_script('chatx-script', plugin_dir_url(__FILE__) . '../js/chatx.js', array(), '1.0.0', true);

    // Localize the JavaScript file with API key and other settings
    wp_localize_script('chatx-script', 'chatx_settings', array(
        'api_key' => chatx_get_openai_api_key(),
        'api_endpoint' => 'https://api.openai.com/v1/chat/completions',
        'model' => chatx_get_data()
    ));
}

/**
 * Define a function to enqueue stylesheets
 * @return void
 */
function chatx_enqueue_styles() {
    // Get the user-defined colors
    $user_prompt_color = get_option('chatx_user_prompt_color');
    $response_color = get_option('chatx_response_color');
    $user_prompt_bgcolor = get_option('chatx_bgPrompt_color');
    $response_bgcolor = get_option('chatx_bgRes_color');
    $loader_color = get_option('chatx_loader_color');

    // Enqueue the styles with dynamic colors
    wp_enqueue_style(
        'chatx-styles',
        plugin_dir_url(__FILE__) . '../css/chatx-styles.css',
        [],
        '1.0.0'
    );

    // Inline CSS to customize chat container colors
    $inline_styles = "
        .user-prompts .prompts .prompt-message {
            background-color: $user_prompt_color;
        }

        .chat-prompts .prompts .prompt-message {
            background-color: $response_color;
        }
        .user-prompts {
            background-color: $user_prompt_bgcolor;
        }

        .chat-prompts {
            background-color: $response_bgcolor;
        }
        .chat-loader:after {
            border-color: $loader_color transparent $loader_color transparent;
        }
    ";

    wp_add_inline_style('chatx-styles', $inline_styles);
}

//  hook the js scripts
add_action('wp_enqueue_scripts', 'chatx_enqueue_scripts');

// Hook the function to the 'admin_enqueue_scripts' action
add_action( 'wp_enqueue_scripts', 'chatx_enqueue_styles' );
