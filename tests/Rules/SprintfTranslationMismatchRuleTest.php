<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\SprintfTranslationMismatchRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for SprintfTranslationMismatchRule.
 *
 * @extends RuleTestCase<SprintfTranslationMismatchRule>
 */
final class SprintfTranslationMismatchRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new SprintfTranslationMismatchRule();
	}

	/**
	 * Tests that sprintf() placeholder count mismatches in translation calls are flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/sprintf-translation-mismatch.php' ],
			[
				[
					'sprintf() format string has 2 placeholders but 3 arguments.',
					37,
				],
				[
					'sprintf() format string has 2 placeholders but 1 argument.',
					40,
				],
				[
					'sprintf() format string has 3 placeholders but 2 arguments.',
					43,
				],
				[
					'sprintf() format string has 1 placeholder but 2 arguments.',
					46,
				],
			]
		);
	}
}
