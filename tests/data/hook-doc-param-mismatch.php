<?php

declare(strict_types=1);

$post_id = 1;
$update = true;
$args = [ $post_id, $update ];

// --- Should NOT be flagged ---

// Correct count and compatible types
/**
 * @param int $post_id
 * @param bool $update
 */
do_action( 'save_post', $post_id, $update );

// No PHPDoc - skip
do_action( 'no_doc', $post_id, $update );

// No args, no PHPDoc - skip
do_action( 'init' );

// Splat arg - skip (static count cannot be determined)
/**
 * @param int $post_id
 * @param bool $update
 */
do_action( 'save_post', ...$args );

// Cross-reference - skip
/**
 * This action is documented in src/wp-admin/admin.php
 */
do_action( 'save_post', $post_id );

// do_action_ref_array - not checked
/**
 * @param int $post_id
 * @param bool $update
 */
do_action_ref_array( 'save_post', [ $post_id, $update ] );

// apply_filters - correct count and types
/**
 * @param string $value
 * @param int $post_id
 */
apply_filters( 'the_content', 'hello', $post_id );

// apply_filters - cross-reference - skip
/**
 * This filter is documented in src/wp-includes/post.php
 */
apply_filters( 'the_title', 'hello' );

// apply_filters_ref_array - not checked
/**
 * @param string $value
 */
apply_filters_ref_array( 'the_content', [ 'hello' ] );

// --- Should be flagged ---

// Count mismatch: 2 args, 1 @param
/**
 * @param int $post_id
 */
do_action( 'save_post', $post_id, $update );

// Count mismatch: 1 arg, 2 @param
/**
 * @param int $post_id
 * @param bool $update
 */
do_action( 'save_post', $post_id );

// Type mismatch: declared string, passed bool
/**
 * @param string $flag
 */
do_action( 'my_hook', true );

// apply_filters count mismatch: 2 args, 1 @param
/**
 * @param string $value
 */
apply_filters( 'the_content', 'hello', $post_id );

// apply_filters type mismatch: declared int, passed string
/**
 * @param int $count
 */
apply_filters( 'my_filter', 'not-an-int' );
