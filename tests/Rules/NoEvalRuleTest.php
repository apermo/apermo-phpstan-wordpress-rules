<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\NoEvalRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for NoEvalRule.
 *
 * @extends RuleTestCase<NoEvalRule>
 */
final class NoEvalRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new NoEvalRule();
	}

	/**
	 * Tests that eval() is flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/no-dynamic-code-execution.php' ],
			[
				[
					'Do not use eval(). It executes arbitrary PHP code and enables remote code execution.',
					6,
				],
			]
		);
	}
}
