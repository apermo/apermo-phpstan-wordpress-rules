<?php

declare(strict_types=1);

/** @var array<string, mixed> $large_array */
$large_array = [ 'key' => 'value' ];

// --- Should NOT be flagged ---

// Autoload disabled
update_option( 'my_plugin_settings', $large_array, false );
update_option( 'my_plugin_settings', $large_array, 'no' );

// Scalar value with autoload (fine)
update_option( 'my_plugin_version', '1.0.0', true );
update_option( 'my_plugin_version', '1.0.0', 'yes' );

// No explicit autoload (uses WordPress smart default since 6.6)
update_option( 'my_plugin_settings', $large_array );

// add_option autoload disabled
add_option( 'my_plugin_data', $large_array, '', false );
add_option( 'my_plugin_data', $large_array, '', 'no' );

// add_option scalar with autoload
add_option( 'my_plugin_version', '1.0.0', '', true );

// add_option no explicit autoload
add_option( 'my_plugin_data', $large_array );

// --- Should be flagged ---

// update_option array value with autoload true
update_option( 'my_plugin_settings', $large_array, true );

// update_option array literal with autoload 'yes'
update_option( 'my_plugin_settings', [ 'a' => 1 ], 'yes' );

// add_option array value with autoload true
add_option( 'my_plugin_data', $large_array, '', true );

// add_option array literal with autoload 'yes'
add_option( 'my_plugin_data', [ 'key' => 'value' ], '', 'yes' );
