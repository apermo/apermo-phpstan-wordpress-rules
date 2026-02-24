<?php

declare(strict_types=1);

// Should be flagged — missing expiration
set_transient('my_cache', $data);
set_site_transient('my_cache', $data);

// Should be flagged — explicit zero
set_transient('my_cache', $data, 0);

// Should NOT be flagged — positive integer
set_transient('my_cache', $data, 3600);
set_site_transient('my_cache', $data, 86400);

// Should NOT be flagged — variable expiration
set_transient('my_cache', $data, $expiration);

// Should NOT be flagged — constant
set_transient('my_cache', $data, HOUR_IN_SECONDS);
