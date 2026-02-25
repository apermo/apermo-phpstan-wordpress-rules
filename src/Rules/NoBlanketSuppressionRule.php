<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Rules;

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Flags blanket suppression comments that suppress all checks without specifying which rules.
 *
 * @implements Rule<Stmt>
 */
final class NoBlanketSuppressionRule implements Rule {

	/**
	 * Returns the node type this rule processes.
	 *
	 * @return class-string<Stmt>
	 */
	public function getNodeType(): string {
		return Stmt::class;
	}

	/**
	 * Processes a statement node and checks its comments for blanket suppressions.
	 *
	 * @param \PhpParser\Node\Stmt $node  Statement node.
	 * @param Scope                $scope Analysis scope.
	 * @return list<\PHPStan\Rules\IdentifierRuleError>
	 */
	public function processNode( Node $node, Scope $scope ): array { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint -- Rule interface requires Node
		$errors = [];

		foreach ( $node->getComments() as $comment ) {
			if ( $this->is_blanket_suppression( $comment ) ) {
				$errors[] = RuleErrorBuilder::message(
					'Blanket suppression is not allowed. Specify which rules to suppress.'
				)->identifier( 'apermo.noBlanketSuppression' )->line( $comment->getStartLine() )->build();
			}
		}

		return $errors;
	}

	/**
	 * Checks if a comment is a blanket suppression.
	 *
	 * @param Comment $comment Comment to check.
	 * @return bool
	 */
	private function is_blanket_suppression( Comment $comment ): bool {
		$text = $comment->getText();

		// Strip comment delimiters to get the content.
		$content = $this->get_comment_content( $text );
		$content = trim( $content );

		// Blanket phpcs:disable with no rule specified.
		if ( preg_match( '/^phpcs:disable\s*$/i', $content ) === 1 ) {
			return true;
		}

		// phpcs:disable with only a reason, no rule name.
		if ( preg_match( '/^phpcs:disable\s+--/i', $content ) === 1 ) {
			return true;
		}

		// Blanket phpcs:ignore with no rule specified.
		if ( preg_match( '/^phpcs:ignore\s*$/i', $content ) === 1 ) {
			return true;
		}

		// phpcs:ignore with only a reason, no rule name.
		if ( preg_match( '/^phpcs:ignore\s+--/i', $content ) === 1 ) {
			return true;
		}

		// Blanket phpstan ignore-next-line with no rule identifiers.
		if ( preg_match( '/^@phpstan-ignore-next-line\s*$/i', $content ) === 1 ) {
			return true;
		}

		// Blanket phpstan ignore-line with no rule identifiers.
		if ( preg_match( '/^@phpstan-ignore-line\s*$/i', $content ) === 1 ) {
			return true;
		}

		return false;
	}

	/**
	 * Extracts the content from a comment, stripping delimiters.
	 *
	 * @param string $text Raw comment text including delimiters.
	 * @return string
	 */
	private function get_comment_content( string $text ): string {
		// Single-line: // content or # content.
		if ( str_starts_with( $text, '//' ) ) {
			return substr( $text, 2 );
		}

		if ( str_starts_with( $text, '#' ) ) {
			return substr( $text, 1 );
		}

		// Multi-line: /* content */ or /** content */.
		if ( str_starts_with( $text, '/*' ) ) {
			$inner = substr( $text, 2, -2 );

			// Remove leading * from doc comment lines.
			$inner = preg_replace( '/^\s*\*\s?/m', '', $inner );

			return $inner ?? '';
		}

		return $text;
	}
}
