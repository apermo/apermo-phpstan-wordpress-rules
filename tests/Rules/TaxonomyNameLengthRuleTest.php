<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\TaxonomyNameLengthRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for TaxonomyNameLengthRule.
 *
 * @extends RuleTestCase<TaxonomyNameLengthRule>
 */
final class TaxonomyNameLengthRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new TaxonomyNameLengthRule();
	}

	/**
	 * Tests that taxonomy names exceeding 32 characters are flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/taxonomy-name-length.php' ],
			[
				[
					'Taxonomy name "extremely_long_custom_taxonomy_name_xy" is 38 characters long. WordPress limits taxonomy names to 32 characters.',
					6,
				],
				[
					'Taxonomy name "my_very_long_custom_taxonomy_tags" is 33 characters long. WordPress limits taxonomy names to 32 characters.',
					9,
				],
			]
		);
	}
}
