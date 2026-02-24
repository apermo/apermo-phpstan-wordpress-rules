<?php

declare(strict_types=1);

// Should be flagged — no second argument
$data = unserialize($input);

// Should be flagged — empty options array
$data = unserialize($input, []);

// Should NOT be flagged — allowed_classes => false
$data = unserialize($input, ['allowed_classes' => false]);

// Should NOT be flagged — allowed_classes with specific classes
$data = unserialize($input, ['allowed_classes' => [stdClass::class]]);

// Should NOT be flagged — variable options
$data = unserialize($input, $options);

// Should NOT be flagged — json_decode
$data = json_decode($input, true);
