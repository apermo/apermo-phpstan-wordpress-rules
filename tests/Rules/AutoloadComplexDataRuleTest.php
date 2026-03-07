<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\AutoloadComplexDataRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for AutoloadComplexDataRule.
 *
 * @extends RuleTestCase<AutoloadComplexDataRule>
 */
final class AutoloadComplexDataRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new AutoloadComplexDataRule();
	}

	/**
	 * Tests that autoloading array-typed data is flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/autoload-complex-data.php' ],
			[
				[
					'update_option() autoloads complex data. Pass false as the third argument to disable autoloading for large or infrequently-used values.',
					34,
				],
				[
					'update_option() autoloads complex data. Pass false as the third argument to disable autoloading for large or infrequently-used values.',
					37,
				],
				[
					'add_option() autoloads complex data. Pass false as the fourth argument to disable autoloading for large or infrequently-used values.',
					40,
				],
				[
					'add_option() autoloads complex data. Pass false as the fourth argument to disable autoloading for large or infrequently-used values.',
					43,
				],
			]
		);
	}
}
