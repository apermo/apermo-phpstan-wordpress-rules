<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\FilterDocParamMismatchRule;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Testing\RuleTestCase;
use PHPStan\Type\FileTypeMapper;
use SzepeViktor\PHPStan\WordPress\HookDocBlock;

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
			new HookDocBlock( self::getContainer()->getByType( FileTypeMapper::class ) ),
			self::getContainer()->getByType( RuleLevelHelper::class ),
		);
	}

	/**
	 * Loads rules.neon so HookDocsVisitor is registered during fixture analysis.
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
