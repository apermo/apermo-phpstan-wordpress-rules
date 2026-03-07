<?php

declare(strict_types=1);

// --- Should NOT be flagged ---

// Matching count
echo sprintf( __( 'Hello %s', 'domain' ), $name );
echo sprintf( __( 'You have %d items in %s', 'domain' ), $count, $category );

// Numbered placeholders (%1$s counts unique numbers, not occurrences)
echo sprintf( __( '%1$s has %2$d items', 'domain' ), $name, $count );
echo sprintf( __( '%1$s — %1$s again', 'domain' ), $name );

// %% is an escaped percent, not a placeholder
echo sprintf( __( '100%% done', 'domain' ) );
echo sprintf( __( 'Progress: %d%%', 'domain' ), $percent );

// No placeholders, no extra args
echo sprintf( __( 'Hello World', 'domain' ) );

// _n with matching args (singular and plural both have 1 placeholder)
echo sprintf( _n( '%d item', '%d items', $count, 'domain' ), $count );

// Non-literal format string — cannot analyse
$format = __( 'Hello %s', 'domain' );
echo sprintf( $format, $name );

// Other translation functions
echo sprintf( esc_html__( 'Hello %s', 'domain' ), $name );
echo sprintf( esc_attr__( '%d items', 'domain' ), $count );
echo sprintf( _x( 'Hello %s', 'greeting', 'domain' ), $name );

// --- Should be flagged ---

// Too many arguments (2 placeholders, 3 args)
echo sprintf( __( 'Hello %s, you have %d items', 'domain' ), $name, $count, $extra );

// Too few arguments (2 placeholders, 1 arg)
echo sprintf( __( 'From %s to %s', 'domain' ), $start );

// esc_html__ mismatch
echo sprintf( esc_html__( 'Updated %s at %s on %s', 'domain' ), $what, $time );

// _n where both forms have 1 placeholder but 2 args are passed
echo sprintf( _n( '%d item', '%d items', $count, 'domain' ), $count, $extra );
