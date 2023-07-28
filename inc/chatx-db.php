<?php

/**
 * chatx_get_data
 * 
 * Retrieve model data from the database
 * 
 * @return string preferred model
 */
function chatx_get_data()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'chatxsettings';
  $results = $wpdb->get_results("SELECT preferredModel FROM $table_name", ARRAY_A);

  return $results[0]['preferredModel'];
}

/**
 * chatx_update_settings
 * 
 * Update the database with users preferences
 * 
 * @return boolean 
 */
function chatx_update_settings($api_key, $model = "")
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'chatxsettings';
  $query = array('preferredModel' => $model);
  if (strlen($api_key) > 0) {
    $query = array(
      'preferredModel' => $model,
      'api_key' => htmlspecialchars($api_key)
    );
  }
  // update the table
  if (
    $wpdb->update(
      $table_name,
      $query,
      array('id' => 1)
    )
  ) {
    return true; // Return true to indicate successful update
  }

  return false; // Return false if the API key doesn't exist
}

/**
 * chatx_update_settings
 * 
 * Update the database with users preferences
 * 
 * @return boolean 
 */
function chatx_update_colors($response, $prompt, $loader, $gbrescolor, $gbprocolor)
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'chatxsettings';
  $query = array(
    'responseColor' => $response,
    'promptColor' => $prompt,
    'loaderColor' => $loader,
    'bgColorRes' => $gbrescolor,
    'bgColorPrompt' => $gbprocolor,
  );
  // update the table
  if (
    $wpdb->update(
      $table_name,
      $query,
      array('id' => 1)
    )
  ) {
    update_option('chatx_user_prompt_color', $prompt);
    update_option('chatx_response_color', $response);
    update_option('chatx_loader_color', $loader);
    update_option('chatx_bgRes_color', $gbrescolor);
    update_option('chatx_bgPrompt_color', $gbprocolor);
    return true; // Return true to indicate successful update
  }

  return false; // Return false if the API key doesn't exist
}

/**
 * chatx_get_openai_api_key
 * 
 * Get api key from database
 * 
 * @return string
 */
function chatx_get_openai_api_key()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'chatxsettings';
  // Retrieve the API key from the database
  $api_key = $wpdb->get_var("SELECT api_key FROM $table_name");
  //echo $api_key;
  return $api_key;
}

/**
 * get color settings
 * 
 * @return array
 */
function chatx_get_color()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'chatxsettings';
  $colors = $wpdb->get_row("SELECT promptColor, responseColor, loaderColor, bgColorRes, bgColorPrompt FROM $table_name", ARRAY_A);
  return $colors;
}

/**
 * chatx_create_table
 * 
 * Create the 'chatxsettings' table if it doesn't exist
 * 
 * @return void
 */
function chatx_create_table()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'chatxsettings';
  $charset_collate = $wpdb->get_charset_collate();

  // Check if the table exists
  if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    // Create the table if it doesn't exist
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
            id INT(11) NOT NULL AUTO_INCREMENT,
            preferredModel VARCHAR(35) NOT NULL,
            api_key VARCHAR(255) NOT NULL,
            responseColor VARCHAR(10) NOT NULL,
            promptColor VARCHAR(10) NOT NULL,
            loaderColor VARCHAR(10) NOT NULL,
            bgColorPrompt VARCHAR(10) NOT NULL,
            bgColorRes VARCHAR(10) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

  // Insert demo data if table is empty
  $rows = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
  if (empty($rows)) {
    $demo_api_key = 'your-demo-api-key'; // Replace with your demo API key

    $hashed_api_key = password_hash($demo_api_key, PASSWORD_DEFAULT); // Hash the API key

    $wpdb->insert(
      $table_name,
      array(
        'api_key' => $hashed_api_key,
        'preferredModel' => 'gpt-3.5-turbo',
        'responseColor' => '#4343488f',
        'promptColor' => '#696a78',
        'loaderColor' => '#E61919',
        'bgColorRes' => '#696a78',
        'bgColorPrompt' => '#505052',
      )
    );
    add_option('chatx_user_prompt_color', '#696a78');
    add_option('chatx_response_color', '#4343488f');
    add_option('chatx_loader_color', '#E61919');
    add_option('chatx_bgRes_color', '#696a78');
    add_option('chatx_bgPrompt_color', '#505052');
  }
}

/**
 * 
 * Define plugin data after plugin uninstalls
 * 
 * @return void
 */
function chatx_delete_data()
{
  // Perform your database deletion operations here
  global $wpdb;
  $table_name = $wpdb->prefix . 'chatxsettings';

  // Delete the table
  $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
