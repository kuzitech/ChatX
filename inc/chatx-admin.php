<?php

/**
 * Add an admin menu item
 * 
 * @return void
 */
function chatx_add_admin_menu() {
    add_menu_page(
        'ChatX Settings',
        'ChatX',
        'manage_options',
        'chatx-settings',
        'chatx_render_admin_page',
        'dashicons-admin-generic',
        99
    );
}

/**
 * Render the admin page content
 * @return void
 */
function chatx_render_admin_page() {
    if (isset($_POST['chatx_save'])) {
        // Handle form submission and save data to the database
        $data_model = sanitize_text_field($_POST['chatx_data']);
        $data_select = $_POST['chatx_select'];
        if ( chatx_update_settings($data_model, $data_select) ) {
            echo '<div class="notice notice-success"><p>Data saved successfully.</p></div>';
        } else {
            echo '<div class="notice notice-info"><p>Data not saved.</p></div>';
        }
    }

    if (isset($_POST['chatx_user_prompt_color'])) {
        $response = sanitize_hex_color($_POST['chatx_response_color']);
        $prompt = sanitize_hex_color($_POST['chatx_user_prompt_color']);
        $loader = sanitize_hex_color($_POST['chatx_loader_color']);
        $bgrescolor = sanitize_hex_color($_POST['chatx_bgRes_color']);
        $bgprocolor = sanitize_hex_color($_POST['chatx_bgPrompt_color']);
        if ( chatx_update_colors($response, $prompt, $loader, $bgrescolor, $bgprocolor) ) {
            echo '<div class="notice notice-success"><p>Data saved successfully.</p></div>';
        } else {
            echo '<div class="notice notice-info"><p>Data not saved.</p></div>';
        }
    }
    $prefData = chatx_get_data();

    ?>
    <div class="wrap">
        <h1>ChatX Settings</h1>
        <p>Configure the settings for the plugin here.</p>
        <h2>Chat Block Usage Instructions</h2>
        <p>To add the chat block to your content, use the following shortcode:</p>
        <pre>[chatx_block]</pre>
        <p>Place this shortcode in your posts, pages, or custom widget areas to display the chat block.</p>
        <h2>Besic Settings</h2>
        <h4>OpenAI API KEY</h4>
        <p>Enter your API Key here or get your API keys <a target="_blank" href="https://platform.openai.com/account/api-keys">here</a> if you don't have yet.</p>
        <form id="chatx_settings_section" method="post" action="">
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><label for="chatx_data">OpenAI API KEY</label></th>
                        <td><input type="password" name="chatx_data" id="chatx_data"  class="regular-text" value="" placeholder="xxxxxxxxxxxxxxxxxx"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="chatx_select">Select Model</label></th>
                        <td>
                            <select name="chatx_select" id="chatx_select">
                                <option value="<?php echo esc_attr($prefData); ?>"><?php echo esc_html($prefData); ?></option>
                                <option value="gpt-4">gpt-4</option>
                                <option value="gpt-4-0314">gpt-4-0314</option>
                                <option value="gpt-4-32k">gpt-4-32k</option>
                                <option value="gpt-4-32k-0314">gpt-4-32k-0314</option>
                                <option value="gpt-3.5-turbo">gpt-3.5-turbo</option>
                                <option value="gpt-3.5-turbo-0301">gpt-3.5-turbo-0301</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><input type="submit" name="chatx_save" class="button button-primary" value="Save Basic Settings"></th>
                    </tr>
                </tbody>
            </table>   
        </form>
        <div class="wrap">
            <form id="chatx_color_section" method="post" action="">
                <?php
                // Output the settings fields
                settings_fields('chatx_settings');
                do_settings_sections('chatx-settings'); // Use the settings page slug as the section ID

                submit_button('Save Color Settings');
                ?>
            </form>
        </div>
        <a href="<?php echo admin_url('plugins.php'); ?>">Go to Plugin List</a>
    </div>
    <?php
}

/**
 * Add ChatX to settings menu
 */
function chatx_add_settings_page() {
    add_options_page(
        'ChatX Settings',
        'ChatX',
        'manage_options',
        'chatx-settings',
        'chatx_render_admin_page'
    );

    // Register a section
    add_settings_section(
        'chatx_color_section',    // Section ID
        'Color Settings',         // Section title
        'chatx_render_option',//'chatx_render_section',   // Section callback (optional)
        'chatx-settings'          // Settings page slug
    );

    // Register settings
    register_setting('chatx_settings', 'chatx_response_color');
    register_setting('chatx_settings', 'chatx_user_prompt_color');

    // Add color input field for user prompts
    add_settings_field(
        'chatx_user_prompt_color',
        'User Prompt Color',
        'chatx_render_color_input',
        'chatx-settings',
        'chatx_color_section',
        ['input_name' => 'chatx_user_prompt_color']
    );
    
    // Add color input field for ChatX responses
    add_settings_field(
        'chatx_response_color',
        'ChatX Response Color',
        'chatx_render_color_input',
        'chatx-settings',
        'chatx_color_section',
        ['input_name' => 'chatx_response_color']
    );
    
    // Add color input field for ChatX loader
    add_settings_field(
        'chatx_loader_color',
        'Loader Color',
        'chatx_render_color_input',
        'chatx-settings',
        'chatx_color_section',
        ['input_name' => 'chatx_loader_color']
    );
    
    // Add color input field for ChatX background
    add_settings_field(
        'chatx_bgPrompt_color',
        'Chat Background Color',
        'chatx_render_color_input',
        'chatx-settings',
        'chatx_color_section',
        ['input_name' => 'chatx_bgPrompt_color']
    );
    
    // Add color input field for ChatX background
    add_settings_field(
        'chatx_bgRes_color',
        'User Background Color',
        'chatx_render_color_input',
        'chatx-settings',
        'chatx_color_section',
        ['input_name' => 'chatx_bgRes_color']
    );
    $colors = chatx_get_color();
    update_option('chatx_user_prompt_color', $colors['promptColor']);
    update_option('chatx_response_color', $colors['responseColor']);
    update_option('chatx_loader_color', $colors['loaderColor']);
    update_option('chatx_bgRes_color', $colors['bgColorRes']);
    update_option('chatx_bgPrompt_color', $colors['bgColorPrompt']);
}

/**
 * add the settings link on plugin activation page
 * @param array $links
 * @return array
 */
function chatx_add_settings_link($links) {
    // Add the settings link to the plugin's action links
    $settings_link = '<a href="' . admin_url('options-general.php?page=chatx-settings') . '">Settings</a>';
    array_push($links, $settings_link);
    return $links;
}

// Function to render color input field
function chatx_render_color_input($args) {
    $input_name = $args['input_name'];
    $color_value = get_option($input_name);
    echo "<input type='color' name='$input_name' value='$color_value' />";
}

function chatx_render_option() {
    echo "<p>Customize the chat interface.</p>";
}

//  register link at admin menu
add_action('admin_menu', 'chatx_add_admin_menu');

//  register link at settings menu
add_action('admin_menu', 'chatx_add_settings_page');