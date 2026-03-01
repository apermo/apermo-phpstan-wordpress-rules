<?php

declare(strict_types=1);

// Should be flagged — eval
eval('echo "hello";');

// Should be flagged — create_function
$func = create_function('$a', 'return $a * 2;');

// Should be flagged — assert with string
assert('$x > 0');

// Should be flagged — preg_replace with /e
preg_replace('/pattern/e', 'strtoupper("$1")', $subject);

// Should NOT be flagged — closure
$func = function ($a) {
	return $a * 2;
};

// Should NOT be flagged — assert with expression
assert($x > 0);

// Should NOT be flagged — preg_replace without /e
preg_replace('/pattern/', 'replacement', $subject);

// Should NOT be flagged — preg_replace_callback
preg_replace_callback('/pattern/', function ($matches) {
	return strtoupper($matches[1]);
}, $subject);

// Should NOT be flagged — preg_replace with variable pattern
preg_replace($pattern, 'replacement', $subject);
