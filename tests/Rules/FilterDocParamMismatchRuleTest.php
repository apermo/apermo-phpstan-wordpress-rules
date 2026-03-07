<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\FilterDocParamMismatchRule;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for FilterDocParamMismatchRule.
 *
 * @extends RuleTestCase<FilterDocParamMismatchRule>
 */
final class FilterDocParamMismatchRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new FilterDocParamMismatchRule(
			self::getContainer()->getByType( PhpDocParser::class ),
			self::getContainer()->getByType( Lexer::class ),
		);
	}

	/**
	 * Tests that @param count and type mismatches in apply_filters() PHPDoc blocks are flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/hook-doc-param-mismatch.php' ],
			[
				[
					'apply_filters() PHPDoc has 1 @param tag but the hook call passes 2 arguments.',
					104,
				],
				[
					'apply_filters() @param #1 declares type int but string is passed.',
					110,
				],
				[
					'apply_filters() PHPDoc has 1 @param tag but the hook call passes 2 arguments.',
					116,
				],
				[
					'apply_filters() @param #1 declares type int but string is passed.',
					123,
				],
			]
		);
	}
}
