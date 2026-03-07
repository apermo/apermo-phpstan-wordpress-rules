<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\WpQueryNoFoundRowsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for WpQueryNoFoundRowsRule.
 *
 * @extends RuleTestCase<WpQueryNoFoundRowsRule>
 */
final class WpQueryNoFoundRowsRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new WpQueryNoFoundRowsRule();
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
	 * Tests that WP_Query without no_found_rows is flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/wp-query-no-found-rows.php' ],
			[
				[
					'WP_Query is missing \'no_found_rows\' => true. Set it to skip SQL_CALC_FOUND_ROWS when pagination is not needed.',
					31,
				],
				[
					'WP_Query is missing \'no_found_rows\' => true. Set it to skip SQL_CALC_FOUND_ROWS when pagination is not needed.',
					37,
				],
			]
		);
	}
}
