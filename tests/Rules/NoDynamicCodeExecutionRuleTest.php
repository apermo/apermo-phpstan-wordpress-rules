<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\NoDynamicCodeExecutionRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for NoDynamicCodeExecutionRule.
 *
 * @extends RuleTestCase<NoDynamicCodeExecutionRule>
 */
final class NoDynamicCodeExecutionRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new NoDynamicCodeExecutionRule();
	}

	/**
	 * Tests that dynamic code execution functions are flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/no-dynamic-code-execution.php' ],
			[
				[
					'Do not use create_function(). Use anonymous functions (closures) instead.',
					9,
				],
				[
					'Do not use assert() with a string argument. It evaluates the string as PHP code. Use an expression instead.',
					12,
				],
				[
					'Do not use preg_replace() with the /e modifier. Use preg_replace_callback() instead.',
					15,
				],
			]
		);
	}
}
