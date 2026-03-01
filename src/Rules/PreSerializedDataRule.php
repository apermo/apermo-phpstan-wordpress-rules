<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Rules;

use Apermo\PhpStanWordPressRules\Constants\WordPressStorageFunctions;
use Apermo\PhpStanWordPressRules\Type\SerializedStringType;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Flags pre-serialized data passed to WordPress storage functions.
 *
 * @implements Rule<FuncCall>
 */
final class PreSerializedDataRule implements Rule {

	private const BANNED_FUNCTIONS = [
		'serialize',
		'maybe_serialize',
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

		if ( ! isset( WordPressStorageFunctions::VALUE_PARAM_INDEX[ $function_name ] ) ) {
			return [];
		}

		$value_index = WordPressStorageFunctions::VALUE_PARAM_INDEX[ $function_name ];
		$args        = $node->getArgs();

		if ( ! isset( $args[ $value_index ] ) ) {
			return [];
		}

		$value_expr = $args[ $value_index ]->value;

		if ( $this->is_direct_serialize_call( $value_expr ) ) {
			return [
				RuleErrorBuilder::message(
					sprintf(
						'Do not pass pre-serialized data to %s(). WordPress serializes data automatically.',
						$function_name,
					)
				)->identifier( 'apermo.preSerializedData' )->build(),
			];
		}

		$arg_type = $scope->getType( $value_expr );

		if ( $arg_type instanceof SerializedStringType ) {
			return [
				RuleErrorBuilder::message(
					sprintf(
						'Do not pass pre-serialized data to %s(). WordPress serializes data automatically.',
						$function_name,
					)
				)->identifier( 'apermo.preSerializedData' )->build(),
			];
		}

		return [];
	}

	/**
	 * Checks if the expression is a direct call to serialize() or maybe_serialize().
	 *
	 * @param Node\Expr $expr Expression to check.
	 * @return bool
	 */
	private function is_direct_serialize_call( Node\Expr $expr ): bool {
		if ( ! $expr instanceof FuncCall ) {
			return false;
		}

		if ( ! $expr->name instanceof Name ) {
			return false;
		}

		return in_array( $expr->name->toLowerString(), self::BANNED_FUNCTIONS, true );
	}
}
