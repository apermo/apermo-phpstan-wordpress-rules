<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\RemoteRequestTimeoutRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for RemoteRequestTimeoutRule.
 *
 * @extends RuleTestCase<RemoteRequestTimeoutRule>
 */
final class RemoteRequestTimeoutRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new RemoteRequestTimeoutRule();
	}

	/**
	 * Tests that remote requests without timeout are flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/remote-request-timeout.php' ],
			[
				[
					'wp_remote_get() called without an explicit timeout. Pass a timeout in the $args array, e.g. [\'timeout\' => 10].',
					6,
				],
				[
					'wp_remote_post() called without an explicit timeout. Pass a timeout in the $args array, e.g. [\'timeout\' => 10].',
					7,
				],
				[
					'wp_remote_request() called without an explicit timeout. Pass a timeout in the $args array, e.g. [\'timeout\' => 10].',
					8,
				],
				[
					'wp_safe_remote_get() called without an explicit timeout. Pass a timeout in the $args array, e.g. [\'timeout\' => 10].',
					9,
				],
				[
					'wp_safe_remote_post() called without an explicit timeout. Pass a timeout in the $args array, e.g. [\'timeout\' => 10].',
					10,
				],
				[
					'wp_safe_remote_request() called without an explicit timeout. Pass a timeout in the $args array, e.g. [\'timeout\' => 10].',
					11,
				],
				[
					'wp_remote_get() called without an explicit timeout. Add a \'timeout\' key to the $args array.',
					14,
				],
				[
					'wp_remote_post() called without an explicit timeout. Add a \'timeout\' key to the $args array.',
					15,
				],
			]
		);
	}
}
