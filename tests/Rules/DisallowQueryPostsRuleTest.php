<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Tests\Rules;

use Apermo\PhpStanWordPressRules\Rules\DisallowQueryPostsRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<DisallowQueryPostsRule>
 */
final class DisallowQueryPostsRuleTest extends RuleTestCase
{
	protected function getRule(): Rule
	{
		return new DisallowQueryPostsRule();
	}

	public function testRule(): void
	{
		$this->analyse([__DIR__ . '/../data/disallow-query-posts.php'], [
			[
				'Do not use query_posts(). Use WP_Query or get_posts() instead.',
				6,
			],
			[
				'Do not use query_posts(). Use WP_Query or get_posts() instead.',
				7,
			],
		]);
	}
}
