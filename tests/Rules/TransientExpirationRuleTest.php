<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\TransientExpirationRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for TransientExpirationRule.
 *
 * @extends RuleTestCase<TransientExpirationRule>
 */
final class TransientExpirationRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new TransientExpirationRule();
	}

	/**
	 * Tests that transients without expiration are flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/transient-expiration.php' ],
			[
				[
					'set_transient() called without an expiration. Transients without expiration never expire and bloat the options table. Add an expiration or use update_option() instead.',
					6,
				],
				[
					'set_site_transient() called without an expiration. Transients without expiration never expire and bloat the options table. Add an expiration or use update_option() instead.',
					7,
				],
				[
					'set_transient() called with an expiration of 0, which means it will never expire. Add a positive expiration or use update_option() instead.',
					10,
				],
			]
		);
	}
}
