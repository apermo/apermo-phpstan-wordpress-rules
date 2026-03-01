<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Flags string concatenation with non-literals inside WordPress translation functions.
 *
 * @implements Rule<FuncCall>
 */
final class NoConcatenationInTranslationRule implements Rule {

	// phpcs:disable Apermo.DataStructures.ArrayComplexity -- WordPress has many translation functions
	/**
	 * Translation functions and which argument positions (0-based) contain translatable strings.
	 *
	 * @var array<string, list<int>>
	 */
	private const TRANSLATION_FUNCTIONS = [
		'__' => [ 0 ],
		'_e' => [ 0 ],
		'esc_html__' => [ 0 ],
		'esc_attr__' => [ 0 ],
		'esc_html_e' => [ 0 ],
		'esc_attr_e' => [ 0 ],
		'_x' => [ 0 ],
		'_ex' => [ 0 ],
		'_n' => [ 0, 1 ],
		'_nx' => [ 0, 1 ],
	];
	// phpcs:enable Apermo.DataStructures.ArrayComplexity

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

		if ( ! isset( self::TRANSLATION_FUNCTIONS[ $function_name ] ) ) {
			return [];
		}

		$args           = $node->getArgs();
		$string_indices = self::TRANSLATION_FUNCTIONS[ $function_name ];
		$errors         = [];

		foreach ( $string_indices as $index ) {
			if ( ! isset( $args[ $index ] ) ) {
				continue;
			}

			$arg_value = $args[ $index ]->value;

			if ( ! $arg_value instanceof Concat ) {
				continue;
			}

			if ( $this->is_all_string_literals( $arg_value ) ) {
				continue;
			}

			$errors[] = RuleErrorBuilder::message(
				sprintf(
					'Do not concatenate dynamic values in %s(). Use sprintf() with a translatable format string instead.',
					$function_name,
				)
			)->identifier( 'apermo.noConcatenationInTranslation' )->build();
		}

		return $errors;
	}

	/**
	 * Recursively checks if a Concat expression is composed entirely of string literals.
	 *
	 * @param Expr $expr Expression to check.
	 * @return bool
	 */
	private function is_all_string_literals( Expr $expr ): bool {
		if ( $expr instanceof String_ ) {
			return true;
		}

		if ( $expr instanceof Concat ) {
			return $this->is_all_string_literals( $expr->left )
				&& $this->is_all_string_literals( $expr->right );
		}

		return false;
	}
}
