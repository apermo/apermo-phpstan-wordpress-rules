<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Flags instantiation of Masterminds\HTML5 which implies non-WP HTML parsing.
 *
 * @implements Rule<New_>
 */
final class NoHtmlDomParsingNewRule implements Rule {

	/**
	 * Returns the node type this rule processes.
	 *
	 * @return class-string<New_>
	 */
	public function getNodeType(): string {
		return New_::class;
	}

	/**
	 * Processes a new expression node.
	 *
	 * @param \PhpParser\Node\Expr\New_ $node  New expression node.
	 * @param Scope                     $scope Analysis scope.
	 * @return list<\PHPStan\Rules\IdentifierRuleError>
	 */
	public function processNode( Node $node, Scope $scope ): array { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint -- Rule interface requires Node
		if ( ! $node->class instanceof Name ) {
			return [];
		}

		$class_name = $node->class->toString();

		if ( $class_name !== 'Masterminds\\HTML5' && $class_name !== 'HTML5' ) {
			return [];
		}

		return [
			RuleErrorBuilder::message(
				'Do not use Masterminds\HTML5. Use WP_HTML_Tag_Processor or WP_HTML_Processor instead.'
			)->identifier( 'apermo.noHtmlDomParsing' )->build(),
		];
	}
}
