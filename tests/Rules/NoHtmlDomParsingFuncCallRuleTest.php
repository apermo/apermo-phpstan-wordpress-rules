<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\NoHtmlDomParsingFuncCallRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for NoHtmlDomParsingFuncCallRule (tidy function calls).
 *
 * @extends RuleTestCase<NoHtmlDomParsingFuncCallRule>
 */
final class NoHtmlDomParsingFuncCallRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new NoHtmlDomParsingFuncCallRule();
	}

	/**
	 * Tests that tidy function calls are flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/no-html-dom-parsing.php' ],
			[
				[
					'Do not use tidy_parse_string(). Use WP_HTML_Tag_Processor or WP_HTML_Processor instead.',
					21,
				],
				[
					'Do not use tidy_parse_file(). Use WP_HTML_Tag_Processor or WP_HTML_Processor instead.',
					24,
				],
			]
		);
	}
}
