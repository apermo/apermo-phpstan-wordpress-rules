<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Flags WordPress HTTP API calls without an explicit timeout argument.
 *
 * @implements Rule<FuncCall>
 */
final class RemoteRequestTimeoutRule implements Rule {

	/**
	 * WordPress HTTP API functions to check.
	 *
	 * @var list<string>
	 */
	private const HTTP_FUNCTIONS = [
		'wp_remote_get',
		'wp_remote_post',
		'wp_remote_request',
		'wp_safe_remote_get',
		'wp_safe_remote_post',
		'wp_safe_remote_request',
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

		if ( ! in_array( $function_name, self::HTTP_FUNCTIONS, true ) ) {
			return [];
		}

		$args = $node->getArgs();

		if ( count( $args ) < 2 ) {
			return [
				RuleErrorBuilder::message(
					sprintf(
						'%s() called without an explicit timeout. Pass a timeout in the $args array, e.g. [\'timeout\' => 10].',
						$function_name,
					)
				)->identifier( 'apermo.remoteRequestTimeout' )->build(),
			];
		}

		$args_param = $args[1]->value;

		// Skip variable args — can't check statically.
		if ( ! $args_param instanceof Array_ ) {
			return [];
		}

		if ( ! $this->has_timeout_key( $args_param ) ) {
			return [
				RuleErrorBuilder::message(
					sprintf(
						'%s() called without an explicit timeout. Add a \'timeout\' key to the $args array.',
						$function_name,
					)
				)->identifier( 'apermo.remoteRequestTimeout' )->build(),
			];
		}

		return [];
	}

	/**
	 * Checks if an array expression contains a timeout key.
	 *
	 * @param Array_ $args_array Arguments array expression.
	 * @return bool
	 */
	private function has_timeout_key( Array_ $args_array ): bool {
		foreach ( $args_array->items as $item ) {
			if ( $item === null ) { // @phpstan-ignore identical.alwaysFalse (Array_::$items can contain null for spread operators)
				continue;
			}

			if ( $item->key instanceof String_ && $item->key->value === 'timeout' ) {
				return true;
			}
		}

		return false;
	}
}
