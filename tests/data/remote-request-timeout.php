<?php

declare(strict_types=1);

// Should be flagged — no args
$response = wp_remote_get($url);
$response = wp_remote_post($url);
$response = wp_remote_request($url);
$response = wp_safe_remote_get($url);
$response = wp_safe_remote_post($url);
$response = wp_safe_remote_request($url);

// Should be flagged — args without timeout
$response = wp_remote_get($url, ['headers' => ['Accept' => 'application/json']]);
$response = wp_remote_post($url, ['body' => $data]);

// Should NOT be flagged — timeout set
$response = wp_remote_get($url, ['timeout' => 10]);
$response = wp_remote_post($url, ['body' => $data, 'timeout' => 15]);
$response = wp_remote_request($url, ['method' => 'PUT', 'timeout' => 5]);
$response = wp_safe_remote_get($url, ['timeout' => 10]);
$response = wp_safe_remote_post($url, ['timeout' => 10]);
$response = wp_safe_remote_request($url, ['timeout' => 10]);

// Should NOT be flagged — variable args
$response = wp_remote_get($url, $args);
