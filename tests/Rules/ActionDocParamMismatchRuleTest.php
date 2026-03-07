<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\ActionDocParamMismatchRule;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Testing\RuleTestCase;
use PHPStan\Type\FileTypeMapper;
use SzepeViktor\PHPStan\WordPress\HookDocBlock;

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
			new HookDocBlock( self::getContainer()->getByType( FileTypeMapper::class ) ),
			self::getContainer()->getByType( RuleLevelHelper::class ),
		);
	}

	/**
	 * Loads phpstan-szepeviktor-services.neon (HookDocBlock + HookDocsVisitor)
	 * and rules.neon for the full DI context needed by the rule under test.
	 *
	 * @return string[]
	 */
	public static function getAdditionalConfigFiles(): array {
		return [
			__DIR__ . '/../../rules.neon',
			__DIR__ . '/../phpstan-szepeviktor-services.neon',
		];
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
					85,
				],
				[
					'do_action() PHPDoc has 2 @param tags but the hook call passes 1 argument.',
					92,
				],
				[
					'do_action() @param #1 declares type string but true is passed.',
					98,
				],
			]
		);
	}
}
