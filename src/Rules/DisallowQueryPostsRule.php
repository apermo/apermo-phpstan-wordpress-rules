<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<FuncCall>
 */
final class DisallowQueryPostsRule implements Rule
{
	public function getNodeType(): string
	{
		return FuncCall::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (!$node->name instanceof Name) {
			return [];
		}

		if ($node->name->toLowerString() !== 'query_posts') {
			return [];
		}

		return [
			RuleErrorBuilder::message(
				'Do not use query_posts(). Use WP_Query or get_posts() instead.'
			)->identifier('apermo.disallowQueryPosts')->build(),
		];
	}
}
