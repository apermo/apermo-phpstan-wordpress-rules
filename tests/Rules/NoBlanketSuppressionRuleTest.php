<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\NoBlanketSuppressionRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for NoBlanketSuppressionRule.
 *
 * @extends RuleTestCase<NoBlanketSuppressionRule>
 */
final class NoBlanketSuppressionRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new NoBlanketSuppressionRule();
	}

	/**
	 * Tests that blanket suppressions are flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/no-blanket-suppression.php' ],
			[
				[
					'Blanket suppression is not allowed. Specify which rules to suppress.',
					5,
				],
				[
					'Blanket suppression is not allowed. Specify which rules to suppress.',
					8,
				],
				[
					'Blanket suppression is not allowed. Specify which rules to suppress.',
					21,
				],
				[
					'Blanket suppression is not allowed. Specify which rules to suppress.',
					24,
				],
				[
					'Blanket suppression is not allowed. Specify which rules to suppress.',
					27,
				],
				[
					'No error to ignore is reported on line 28.',
					28,
				],
				[
					'Blanket suppression is not allowed. Specify which rules to suppress.',
					30,
				],
				[
					'No error with identifier argument.type is reported on line 34.',
					34,
				],
			]
		);
	}
}
