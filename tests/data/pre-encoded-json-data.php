<?php

declare(strict_types=1);

// Should be flagged — direct json_encode() as argument
update_option('my_settings', json_encode($settings));
update_post_meta($post_id, '_config', json_encode($data));
set_transient('cache_key', json_encode($response), 3600);

// NOT flagged — json_encode() via variable (PHPStan core overrides our type extension)
$json = json_encode($user_prefs);
update_user_meta($user_id, '_prefs', $json);

// Should be flagged — wp_json_encode()
update_option('key', wp_json_encode($data));

// Should NOT be flagged — raw data
update_option('my_settings', $settings);
update_post_meta($post_id, '_config', ['foo' => 'bar']);

// Should NOT be flagged — json_encode() not passed to WP storage
$json = json_encode($data);
file_put_contents('/tmp/export.json', $json);
