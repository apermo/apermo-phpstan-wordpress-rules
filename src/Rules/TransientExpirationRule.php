<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Int_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<FuncCall>
 */
final class TransientExpirationRule implements Rule
{
	private const FUNCTIONS = [
		'set_transient' => 2,
		'set_site_transient' => 2,
	];

	public function getNodeType(): string
	{
		return FuncCall::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (!$node->name instanceof Name) {
			return [];
		}

		$functionName = $node->name->toLowerString();

		if (!isset(self::FUNCTIONS[$functionName])) {
			return [];
		}

		$expirationArgIndex = self::FUNCTIONS[$functionName];
		$args = $node->getArgs();

		if (count($args) <= $expirationArgIndex) {
			return [
				RuleErrorBuilder::message(
					sprintf(
						'%s() called without an expiration. Transients without expiration never expire and bloat the options table. Add an expiration or use update_option() instead.',
						$functionName,
					)
				)->identifier('apermo.transientExpiration')->build(),
			];
		}

		$expirationArg = $args[$expirationArgIndex]->value;

		if ($expirationArg instanceof Int_ && $expirationArg->value === 0) {
			return [
				RuleErrorBuilder::message(
					sprintf(
						'%s() called with an expiration of 0, which means it will never expire. Add a positive expiration or use update_option() instead.',
						$functionName,
					)
				)->identifier('apermo.transientExpiration')->build(),
			];
		}

		return [];
	}
}
