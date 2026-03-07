<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\ActionDocParamMismatchRule;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for ActionDocParamMismatchRule.
 *
 * @extends RuleTestCase<ActionDocParamMismatchRule>
 */
final class ActionDocParamMismatchRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new ActionDocParamMismatchRule(
			self::getContainer()->getByType( PhpDocParser::class ),
			self::getContainer()->getByType( Lexer::class ),
		);
	}

	/**
	 * Tests that @param count and type mismatches in do_action() PHPDoc blocks are flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/hook-doc-param-mismatch.php' ],
			[
				[
					'do_action() PHPDoc has 1 @param tag but the hook call passes 2 arguments.',
					69,
				],
				[
					'do_action() PHPDoc has 2 @param tags but the hook call passes 1 argument.',
					76,
				],
				[
					'do_action() @param #1 declares type string but true is passed.',
					82,
				],
			]
		);
	}
}
