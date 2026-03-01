<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\NoConcatenationInTranslationRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for NoConcatenationInTranslationRule.
 *
 * @extends RuleTestCase<NoConcatenationInTranslationRule>
 */
final class NoConcatenationInTranslationRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new NoConcatenationInTranslationRule();
	}

	/**
	 * Tests that concatenation in translation functions is flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/no-concatenation-in-translation.php' ],
			[
				[
					'Do not concatenate dynamic values in __(). Use sprintf() with a translatable format string instead.',
					6,
				],
				[
					'Do not concatenate dynamic values in __(). Use sprintf() with a translatable format string instead.',
					7,
				],
				[
					'Do not concatenate dynamic values in __(). Use sprintf() with a translatable format string instead.',
					10,
				],
				[
					'Do not concatenate dynamic values in esc_html__(). Use sprintf() with a translatable format string instead.',
					11,
				],
				[
					'Do not concatenate dynamic values in _e(). Use sprintf() with a translatable format string instead.',
					14,
				],
				[
					'Do not concatenate dynamic values in _n(). Use sprintf() with a translatable format string instead.',
					17,
				],
				[
					'Do not concatenate dynamic values in _n(). Use sprintf() with a translatable format string instead.',
					17,
				],
				[
					'Do not concatenate dynamic values in esc_attr__(). Use sprintf() with a translatable format string instead.',
					38,
				],
				[
					'Do not concatenate dynamic values in _x(). Use sprintf() with a translatable format string instead.',
					41,
				],
				[
					'Do not concatenate dynamic values in esc_html_e(). Use sprintf() with a translatable format string instead.',
					44,
				],
				[
					'Do not concatenate dynamic values in esc_attr_e(). Use sprintf() with a translatable format string instead.',
					47,
				],
				[
					'Do not concatenate dynamic values in _ex(). Use sprintf() with a translatable format string instead.',
					50,
				],
			]
		);
	}
}
