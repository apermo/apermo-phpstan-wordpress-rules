<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\NoHtmlDomParsingRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * Tests for NoHtmlDomParsingRule (method calls).
 *
 * @extends RuleTestCase<NoHtmlDomParsingRule>
 */
final class NoHtmlDomParsingRuleTest extends RuleTestCase {

	/**
	 * Creates the rule under test.
	 *
	 * @return Rule
	 */
	protected function getRule(): Rule {
		return new NoHtmlDomParsingRule();
	}

	/**
	 * Tests that HTML DOM parsing method calls are flagged.
	 *
	 * @return void
	 */
	public function testRule(): void {
		$this->analyse(
			[ __DIR__ . '/../data/no-html-dom-parsing.php' ],
			[
				[
					'Do not use DOMDocument::loadHTML(). Use WP_HTML_Tag_Processor or WP_HTML_Processor instead.',
					7,
				],
				[
					'Do not use DOMDocument::loadHTMLFile(). Use WP_HTML_Tag_Processor or WP_HTML_Processor instead.',
					11,
				],
				[
					'Do not use Tidy::parseString(). Use WP_HTML_Tag_Processor or WP_HTML_Processor instead.',
					18,
				],
			]
		);
	}
}
