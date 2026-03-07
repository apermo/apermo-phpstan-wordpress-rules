<?php

declare(strict_types=1);

// --- Should NOT be flagged ---

// no_found_rows explicitly true
$q = new WP_Query( [
	'post_type'     => 'post',
	'posts_per_page' => 5,
	'no_found_rows' => true,
] );

// no_found_rows explicitly false (developer chose pagination)
$q = new WP_Query( [
	'post_type'      => 'post',
	'posts_per_page' => 10,
	'no_found_rows'  => false,
] );

// Variable args — cannot analyse statically
$args = [ 'post_type' => 'post' ];
$q = new WP_Query( $args );

// get_posts() already sets no_found_rows internally
$posts = get_posts( [ 'posts_per_page' => 5 ] );

// --- Should be flagged ---

// Missing no_found_rows
$q = new WP_Query( [
	'post_type'      => 'post',
	'posts_per_page' => 5,
] );

// Missing no_found_rows with more args
$q = new WP_Query( [
	'post_type'      => 'product',
	'posts_per_page' => 10,
	'meta_key'       => '_featured',
] );
