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
 * Flags unserialize() calls without the allowed_classes restriction.
 *
 * @implements Rule<FuncCall>
 */
final class UnsafeUnserializeRule implements Rule {

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

		if ( $node->name->toLowerString() !== 'unserialize' ) {
			return [];
		}

		$args = $node->getArgs();

		if ( count( $args ) < 2 ) {
			return [
				RuleErrorBuilder::message(
					'unserialize() called without allowed_classes restriction. Use unserialize($data, [\'allowed_classes\' => false]) or json_decode() instead.'
				)->identifier( 'apermo.unsafeUnserialize' )->build(),
			];
		}

		$options_arg = $args[1]->value;

		if ( ! $options_arg instanceof Array_ ) {
			return [];
		}

		if ( ! $this->has_allowed_classes_key( $options_arg ) ) {
			return [
				RuleErrorBuilder::message(
					'unserialize() called without allowed_classes restriction. Use unserialize($data, [\'allowed_classes\' => false]) or json_decode() instead.'
				)->identifier( 'apermo.unsafeUnserialize' )->build(),
			];
		}

		return [];
	}

	/**
	 * Checks if an array expression contains the allowed_classes key.
	 *
	 * @param Array_ $options_array Options array expression.
	 * @return bool
	 */
	private function has_allowed_classes_key( Array_ $options_array ): bool {
		foreach ( $options_array->items as $item ) {
			if ( $item === null ) { // @phpstan-ignore identical.alwaysFalse (Array_::$items can contain null for spread operators)
				continue;
			}

			if ( $item->key instanceof String_ && $item->key->value === 'allowed_classes' ) {
				return true;
			}
		}

		return false;
	}
}
