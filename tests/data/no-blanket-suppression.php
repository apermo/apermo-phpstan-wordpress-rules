<?php

declare(strict_types=1);

// phpcs:disable
$a = 1;

// phpcs:ignore
$b = 2;

// phpcs:disable WordPress.Security.EscapeOutput
$c = 3;
// phpcs:enable WordPress.Security.EscapeOutput

// phpcs:ignore WordPress.Security.EscapeOutput -- reason
$d = 4;

// phpcs:enable
$e = 5;

// phpcs:disable -- just a reason
$f = 6;

// phpcs:ignore -- just a reason
$g = 7;

// @phpstan-ignore-next-line
$h = 8;

// @phpstan-ignore
$i = 9;

// @phpstan-ignore argument.type
$j = 10;
