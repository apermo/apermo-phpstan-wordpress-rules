<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\NoHtmlDomParsingNewRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for NoHtmlDomParsingNewRule (Masterminds\HTML5 instantiation).
 *
 * @extends RuleTestCase<NoHtmlDomParsingNewRule>
 */
final class NoHtmlDomParsingNewRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new NoHtmlDomParsingNewRule();
	}

	/**
	 * Tests that Masterminds\HTML5 instantiation is flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/no-html-dom-parsing.php' ],
			[
				[
					'Do not use Masterminds\HTML5. Use WP_HTML_Tag_Processor or WP_HTML_Processor instead.',
					14,
				],
			]
		);
	}
}
