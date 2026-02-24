<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\UnsafeUnserializeRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for UnsafeUnserializeRule.
 *
 * @extends RuleTestCase<UnsafeUnserializeRule>
 */
final class UnsafeUnserializeRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new UnsafeUnserializeRule();
	}

	/**
	 * Tests that unsafe unserialize() calls are flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/unsafe-unserialize.php' ],
			[
				[
					'unserialize() called without allowed_classes restriction. Use unserialize($data, [\'allowed_classes\' => false]) or json_decode() instead.',
					6,
				],
				[
					'unserialize() called without allowed_classes restriction. Use unserialize($data, [\'allowed_classes\' => false]) or json_decode() instead.',
					9,
				],
			]
		);
	}
}
