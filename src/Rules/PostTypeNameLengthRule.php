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
 * Flags register_post_type() calls where the name exceeds 20 characters.
 *
 * @implements Rule<FuncCall>
 */
final class PostTypeNameLengthRule implements Rule {

	private const MAX_LENGTH = 20;

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

		if ( $node->name->toLowerString() !== 'register_post_type' ) {
			return [];
		}

		$args = $node->getArgs();

		if ( $args === [] ) {
			return [];
		}

		$first_arg = $args[0]->value;

		if ( ! $first_arg instanceof String_ ) {
			return [];
		}

		$length = strlen( $first_arg->value );

		if ( $length <= self::MAX_LENGTH ) {
			return [];
		}

		return [
			RuleErrorBuilder::message(
				sprintf(
					'Post type name "%s" is %d characters long. WordPress limits post type names to %d characters.',
					$first_arg->value,
					$length,
					self::MAX_LENGTH,
				)
			)->identifier( 'apermo.postTypeNameLength' )->build(),
		];
	}
}
