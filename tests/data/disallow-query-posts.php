<?php

declare(strict_types=1);

// Should be flagged
query_posts('posts_per_page=5');
query_posts(['post_type' => 'page']);

// Should NOT be flagged
$query = new WP_Query(['posts_per_page' => 5]);
$posts = get_posts(['post_type' => 'page']);
