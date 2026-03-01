<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Eval_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Flags usage of eval() which executes arbitrary PHP code.
 *
 * @implements Rule<Eval_>
 */
final class NoEvalRule implements Rule {

	/**
	 * Returns the node type this rule processes.
	 *
	 * @return class-string<Eval_>
	 */
	public function getNodeType(): string {
		return Eval_::class;
	}

	/**
	 * Processes an eval node.
	 *
	 * @param \PhpParser\Node\Expr\Eval_ $node  Eval expression node.
	 * @param Scope                      $scope Analysis scope.
	 * @return list<\PHPStan\Rules\IdentifierRuleError>
	 */
	public function processNode( Node $node, Scope $scope ): array { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint -- Rule interface requires Node
		return [
			RuleErrorBuilder::message(
				'Do not use eval(). It executes arbitrary PHP code and enables remote code execution.'
			)->identifier( 'apermo.noEval' )->build(),
		];
	}
}
