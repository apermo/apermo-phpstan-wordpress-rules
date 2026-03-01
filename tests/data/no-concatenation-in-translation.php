<?php

declare(strict_types=1);

// Should be flagged — variable concatenation
echo __('Hello ' . $name, 'my-plugin');
echo __('You have ' . $count . ' items', 'my-plugin');

// Should be flagged — expression concatenation
echo __('Total: ' . number_format($total), 'my-plugin');
echo esc_html__('Welcome, ' . $user->display_name, 'my-plugin');

// Should be flagged — _e with concatenation
_e('Click ' . '<a href="' . $url . '">here</a>' . ' to continue', 'my-plugin');

// Should be flagged — _n singular and plural
_n('You have ' . $count . ' item', 'You have ' . $count . ' items', $count, 'my-plugin');

// Should NOT be flagged — pure string literal
echo __('Hello World', 'my-plugin');
echo esc_html__('Welcome to our site', 'my-plugin');

// Should NOT be flagged — sprintf wrapper
echo sprintf(__('Hello %s', 'my-plugin'), $name);
echo sprintf(__('You have %d items', 'my-plugin'), $count);

// Should NOT be flagged — pure literal concatenation for line length
echo __(
	'This is a very long string that is split ' .
	'across multiple lines for readability',
	'my-plugin'
);

// Should NOT be flagged — other functions
echo 'Hello ' . $name;

// Should be flagged — esc_attr__
echo esc_attr__('Value: ' . $value, 'my-plugin');

// Should be flagged — _x with context
echo _x('Hello ' . $name, 'greeting context', 'my-plugin');

// Should be flagged — esc_html_e
esc_html_e('Welcome, ' . $user_name, 'my-plugin');

// Should be flagged — esc_attr_e
esc_attr_e('Value: ' . $value, 'my-plugin');

// Should be flagged — _ex
_ex('Hello ' . $name, 'greeting context', 'my-plugin');
