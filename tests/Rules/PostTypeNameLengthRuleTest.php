<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\PostTypeNameLengthRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for PostTypeNameLengthRule.
 *
 * @extends RuleTestCase<PostTypeNameLengthRule>
 */
final class PostTypeNameLengthRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new PostTypeNameLengthRule();
	}

	/**
	 * Tests that post type names exceeding 20 characters are flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/post-type-name-length.php' ],
			[
				[
					'Post type name "custom_portfolio_item" is 21 characters long. WordPress limits post type names to 20 characters.',
					6,
				],
				[
					'Post type name "my_very_long_custom_type_x" is 26 characters long. WordPress limits post type names to 20 characters.',
					9,
				],
			]
		);
	}
}
