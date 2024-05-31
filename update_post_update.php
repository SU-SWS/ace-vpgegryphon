<?php

// Use the post_update key-value store
$key_value = \Drupal::keyValue('post_update');
$module_name = 'stanford_profile_helper';
$update_hook = 'stanford_profile_helper_post_update_create_cron';

// Fetch the current value for the module
$current_value = $key_value->get("existing_updates");

$current_value[] = $update_hook;

// Save the updated value back to the key-value store
$key_value->set("existing_updates", $current_value);

echo "Update successful.\n";
