<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Rules;

use Apermo\PhpStanWordPressRules\Constants\WordPressStorageFunctions;
use Apermo\PhpStanWordPressRules\Type\JsonEncodedStringType;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;

/**
 * Flags pre-encoded JSON data passed to WordPress storage functions.
 *
 * @implements Rule<FuncCall>
 */
final class PreEncodedJsonDataRule implements Rule {

	private const BANNED_FUNCTIONS = [
		'json_encode',
		'wp_json_encode',
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

		if ( $this->is_direct_json_encode_call( $value_expr ) ) {
			return [
				RuleErrorBuilder::message(
					sprintf(
						'Do not pass JSON-encoded data to %s(). Pass the raw array/object and let WordPress handle storage.',
						$function_name,
					)
				)->identifier( 'apermo.preEncodedJsonData' )->build(),
			];
		}

		$arg_type = $scope->getType( $value_expr );

		if ( $this->contains_json_encoded_type( $arg_type ) ) {
			return [
				RuleErrorBuilder::message(
					sprintf(
						'Do not pass JSON-encoded data to %s(). Pass the raw array/object and let WordPress handle storage.',
						$function_name,
					)
				)->identifier( 'apermo.preEncodedJsonData' )->build(),
			];
		}

		return [];
	}

	/**
	 * Checks if a type is or contains a JsonEncodedStringType.
	 *
	 * @param Type $type Type to check.
	 * @return bool
	 */
	private function contains_json_encoded_type( Type $type ): bool {
		if ( $type instanceof JsonEncodedStringType ) {
			return true;
		}

		if ( $type instanceof UnionType ) {
			foreach ( $type->getTypes() as $inner ) {
				if ( $inner instanceof JsonEncodedStringType ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Checks if the expression is a direct call to json_encode() or wp_json_encode().
	 *
	 * @param Node\Expr $expr Expression to check.
	 * @return bool
	 */
	private function is_direct_json_encode_call( Node\Expr $expr ): bool {
		if ( ! $expr instanceof FuncCall ) {
			return false;
		}

		if ( ! $expr->name instanceof Name ) {
			return false;
		}

		return in_array( $expr->name->toLowerString(), self::BANNED_FUNCTIONS, true );
	}
}
