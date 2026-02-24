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
 * Flags dynamic code execution functions like create_function(), assert() with strings, and preg_replace() with /e.
 *
 * @implements Rule<FuncCall>
 */
final class NoDynamicCodeExecutionRule implements Rule {

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

		if ( $function_name === 'create_function' ) {
			return [
				RuleErrorBuilder::message(
					'Do not use create_function(). Use anonymous functions (closures) instead.'
				)->identifier( 'apermo.noDynamicCodeExecution' )->build(),
			];
		}

		if ( $function_name === 'assert' ) {
			return $this->check_assert( $node );
		}

		if ( $function_name === 'preg_replace' ) {
			return $this->check_preg_replace( $node );
		}

		return [];
	}

	/**
	 * Checks if assert() is called with a string argument.
	 *
	 * @param FuncCall $node Function call node.
	 * @return list<\PHPStan\Rules\IdentifierRuleError>
	 */
	private function check_assert( FuncCall $node ): array {
		$args = $node->getArgs();

		if ( count( $args ) === 0 ) {
			return [];
		}

		if ( ! $args[0]->value instanceof String_ ) {
			return [];
		}

		return [
			RuleErrorBuilder::message(
				'Do not use assert() with a string argument. It evaluates the string as PHP code. Use an expression instead.'
			)->identifier( 'apermo.noDynamicCodeExecution' )->build(),
		];
	}

	/**
	 * Checks if preg_replace() uses the deprecated e modifier.
	 *
	 * @param FuncCall $node Function call node.
	 * @return list<\PHPStan\Rules\IdentifierRuleError>
	 */
	private function check_preg_replace( FuncCall $node ): array {
		$args = $node->getArgs();

		if ( count( $args ) === 0 ) {
			return [];
		}

		if ( ! $args[0]->value instanceof String_ ) {
			return [];
		}

		$pattern = $args[0]->value->value;

		if ( ! $this->has_e_modifier( $pattern ) ) {
			return [];
		}

		return [
			RuleErrorBuilder::message(
				'Do not use preg_replace() with the /e modifier. Use preg_replace_callback() instead.'
			)->identifier( 'apermo.noDynamicCodeExecution' )->build(),
		];
	}

	/**
	 * Checks if a regex pattern contains the e modifier.
	 *
	 * @param string $pattern Regex pattern.
	 * @return bool
	 */
	private function has_e_modifier( string $pattern ): bool {
		$delimiter = $pattern[0] ?? '';

		if ( $delimiter === '' ) {
			return false;
		}

		$last_delimiter_pos = strrpos( $pattern, $delimiter, 1 );

		if ( $last_delimiter_pos === false ) {
			return false;
		}

		$modifiers = substr( $pattern, $last_delimiter_pos + 1 );

		return str_contains( $modifiers, 'e' );
	}
}
