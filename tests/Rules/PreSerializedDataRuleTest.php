<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\PreSerializedDataRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for PreSerializedDataRule.
 *
 * @extends RuleTestCase<PreSerializedDataRule>
 */
final class PreSerializedDataRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new PreSerializedDataRule();
	}

	/**
	 * Returns additional PHPStan config files to load.
	 *
	 * @return string[]
	 */
	public static function getAdditionalConfigFiles(): array {
		return [ __DIR__ . '/../../rules.neon' ];
	}

	/**
	 * Tests that pre-serialized data passed to WP storage functions is flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/pre-serialized-data.php' ],
			[
				[
					'Do not pass pre-serialized data to update_option(). WordPress serializes data automatically.',
					6,
				],
				[
					'Do not pass pre-serialized data to update_post_meta(). WordPress serializes data automatically.',
					7,
				],
				[
					'Do not pass pre-serialized data to set_transient(). WordPress serializes data automatically.',
					8,
				],
				[
					'Do not pass pre-serialized data to update_user_meta(). WordPress serializes data automatically.',
					12,
				],
				[
					'Do not pass pre-serialized data to update_option(). WordPress serializes data automatically.',
					15,
				],
			]
		);
	}
}
