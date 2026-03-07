<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Warns when new WP_Query() is missing 'no_found_rows' => true.
 *
 * By default WP_Query runs SQL_CALC_FOUND_ROWS, which adds overhead even
 * when pagination is not needed. Setting 'no_found_rows' => true skips it.
 *
 * Identifier: apermo.wpQuery.noFoundRows
 *
 * @implements Rule<New_>
 */
final class WpQueryNoFoundRowsRule implements Rule {

	private const MESSAGE = 'WP_Query is missing \'no_found_rows\' => true. Set it to skip SQL_CALC_FOUND_ROWS when pagination is not needed.';

	/**
	 * Returns the node type this rule processes.
	 *
	 * @return class-string<New_>
	 */
	public function getNodeType(): string {
		return New_::class;
	}

	/**
	 * Processes a new-object expression.
	 *
	 * @param New_  $node  New-object node.
	 * @param Scope $scope Analysis scope.
	 * @return list<\PHPStan\Rules\IdentifierRuleError>
	 */
	public function processNode( Node $node, Scope $scope ): array { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint -- Rule interface requires Node
		if ( ! $node->class instanceof Name ) {
			return [];
		}

		if ( $node->class->toLowerString() !== 'wp_query' ) {
			return [];
		}

		$args = $node->getArgs();

		if ( count( $args ) !== 1 ) {
			return [];
		}

		// Skip variable arguments — cannot analyse statically.
		if ( ! $args[0]->value instanceof Array_ ) {
			return [];
		}

		foreach ( $args[0]->value->items as $item ) {
			if ( ! $item->key instanceof String_ ) {
				continue;
			}

			if ( $item->key->value === 'no_found_rows' ) {
				return [];
			}
		}

		return [
			RuleErrorBuilder::message( self::MESSAGE )
				->identifier( 'apermo.wpQuery.noFoundRows' )
				->build(),
		];
	}
}
