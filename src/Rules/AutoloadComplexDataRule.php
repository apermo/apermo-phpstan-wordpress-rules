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
 * Warns when update_option() or add_option() autoloads array-typed data.
 *
 * Autoloading large or complex data bloats the alloptions cache loaded on
 * every page request. Passing false as the autoload argument avoids this.
 *
 * Identifier: apermo.autoload.complexData
 *
 * @implements Rule<FuncCall>
 */
final class AutoloadComplexDataRule implements Rule {

	/**
	 * Function name => [ value_arg_index, autoload_arg_index, autoload_arg_ordinal_label ]
	 *
	 * @var array<string, array{int, int, string}>
	 */
	private const FUNCTIONS = [
		'update_option' => [ 1, 2, 'third' ],
		'add_option'    => [ 1, 3, 'fourth' ],
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
	 * @param FuncCall $node  Function call node.
	 * @param Scope    $scope Analysis scope.
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

		[ $value_index, $autoload_index, $ordinal ] = self::FUNCTIONS[ $function_name ];

		$args = $node->getArgs();

		if ( ! isset( $args[ $autoload_index ] ) ) {
			return [];
		}

		if ( ! $this->is_autoload_enabled( $args[ $autoload_index ]->value ) ) {
			return [];
		}

		if ( ! isset( $args[ $value_index ] ) ) {
			return [];
		}

		$value_type = $scope->getType( $args[ $value_index ]->value );

		if ( ! $value_type->isArray()->yes() ) {
			return [];
		}

		return [
			RuleErrorBuilder::message(
				sprintf(
					'%s() autoloads complex data. Pass false as the %s argument to disable autoloading for large or infrequently-used values.',
					$function_name,
					$ordinal,
				)
			)->identifier( 'apermo.autoload.complexData' )->build(),
		];
	}

	/**
	 * Checks whether the autoload argument evaluates to true or 'yes'.
	 *
	 * @param \PhpParser\Node\Expr $expr Autoload argument expression.
	 * @return bool
	 */
	private function is_autoload_enabled( \PhpParser\Node\Expr $expr ): bool {
		if ( $expr instanceof \PhpParser\Node\Expr\ConstFetch ) {
			return strtolower( $expr->name->toLowerString() ) === 'true';
		}

		if ( $expr instanceof String_ ) {
			return strtolower( $expr->value ) === 'yes';
		}

		return false;
	}
}
