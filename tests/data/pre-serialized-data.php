<?php

declare(strict_types=1);

// Should be flagged — direct serialize() as argument
update_option('my_settings', serialize($settings));
update_post_meta($post_id, '_config', serialize($data));
set_transient('cache_key', serialize($response), 3600);

// Should be flagged — serialize() via variable
$serialized = serialize($user_prefs);
update_user_meta($user_id, '_prefs', $serialized);

// Should be flagged — maybe_serialize() as argument
update_option('key', maybe_serialize($data));

// Should NOT be flagged — raw data
update_option('my_settings', $settings);
update_post_meta($post_id, '_config', ['foo' => 'bar']);
set_transient('cache_key', $response, 3600);

// Should NOT be flagged — serialize() not passed to WP storage
$encoded = serialize($debug_info);
file_put_contents('/tmp/debug.dat', $encoded);
