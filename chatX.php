<?php

/*
Plugin Name: ChatX
Description: Integrate ChatGPT API with WordPress.
Version: 1.0.0
Author: Kuzitech Solutions
Author URI: Your Website
*/

require_once('inc/core.php');

//  register the shortcode block
add_shortcode( 'chatx_block', 'chatx_block_render' );

//  register during plugin activation
register_activation_hook(__FILE__, 'chatx_create_table');

// Hook the function to the plugin's uninstall action
register_deactivation_hook( __FILE__, 'chatx_delete_data');

//  register the settings links
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'chatx_add_settings_link');
