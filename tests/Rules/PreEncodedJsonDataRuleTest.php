<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\PreEncodedJsonDataRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for PreEncodedJsonDataRule.
 *
 * @extends RuleTestCase<PreEncodedJsonDataRule>
 */
final class PreEncodedJsonDataRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new PreEncodedJsonDataRule();
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
	 * Tests that JSON-encoded data passed to WP storage functions is flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/pre-encoded-json-data.php' ],
			[
				[
					'Do not pass JSON-encoded data to update_option(). Pass the raw array/object and let WordPress handle storage.',
					6,
				],
				[
					'Do not pass JSON-encoded data to update_post_meta(). Pass the raw array/object and let WordPress handle storage.',
					7,
				],
				[
					'Do not pass JSON-encoded data to set_transient(). Pass the raw array/object and let WordPress handle storage.',
					8,
				],
				[
					'Do not pass JSON-encoded data to update_option(). Pass the raw array/object and let WordPress handle storage.',
					15,
				],
			]
		);
	}
}
