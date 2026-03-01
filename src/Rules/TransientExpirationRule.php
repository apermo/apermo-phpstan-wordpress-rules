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
 * Flags set_transient() and set_site_transient() without an expiration.
 *
 * @implements Rule<FuncCall>
 */
final class TransientExpirationRule implements Rule {

	private const FUNCTIONS = [
		'set_transient'      => 2,
		'set_site_transient' => 2,
	];

	/**
	 * Returns the node type this rule processes.
	 *
	 * @return class-string<FuncCall>
	 */
	public function getNodeType(): string {
		return FuncCall::class;
	}

	/**
	 * Processes a function call node.
	 *
	 * @param \PhpParser\Node\Expr\FuncCall $node  Function call node.
	 * @param Scope                         $scope Analysis scope.
	 * @return list<\PHPStan\Rules\IdentifierRuleError>
	 */
	public function processNode( Node $node, Scope $scope ): array { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint -- Rule interface requires Node
		if ( ! $node->name instanceof Name ) {
			return [];
		}

		$function_name = $node->name->toLowerString();

		if ( ! isset( self::FUNCTIONS[ $function_name ] ) ) {
			return [];
		}

		$expiration_index = self::FUNCTIONS[ $function_name ];
		$args             = $node->getArgs();

		if ( count( $args ) <= $expiration_index ) {
			return [
				RuleErrorBuilder::message(
					sprintf(
						'%s() called without an expiration. Transients without expiration never expire and bloat the options table. Add an expiration or use update_option() instead.',
						$function_name,
					)
				)->identifier( 'apermo.transientExpiration' )->build(),
			];
		}

		$expiration_arg = $args[ $expiration_index ]->value;

		if ( $expiration_arg instanceof Int_ && $expiration_arg->value === 0 ) {
			return [
				RuleErrorBuilder::message(
					sprintf(
						'%s() called with an expiration of 0, which means it will never expire. Add a positive expiration or use update_option() instead.',
						$function_name,
					)
				)->identifier( 'apermo.transientExpiration' )->build(),
			];
		}

		return [];
	}
}
