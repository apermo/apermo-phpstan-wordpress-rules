<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<FuncCall>
 */
final class TaxonomyNameLengthRule implements Rule
{
	private const MAX_LENGTH = 32;

	public function getNodeType(): string
	{
		return FuncCall::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (!$node->name instanceof Name) {
			return [];
		}

		if ($node->name->toLowerString() !== 'register_taxonomy') {
			return [];
		}

		$args = $node->getArgs();

		if ($args === []) {
			return [];
		}

		$firstArg = $args[0]->value;

		if (!$firstArg instanceof String_) {
			return [];
		}

		$length = strlen($firstArg->value);

		if ($length <= self::MAX_LENGTH) {
			return [];
		}

		return [
			RuleErrorBuilder::message(
				sprintf(
					'Taxonomy name "%s" is %d characters long. WordPress limits taxonomy names to %d characters.',
					$firstArg->value,
					$length,
					self::MAX_LENGTH,
				)
			)->identifier('apermo.taxonomyNameLength')->build(),
		];
	}
}
