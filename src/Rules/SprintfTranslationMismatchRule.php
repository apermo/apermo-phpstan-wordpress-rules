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
 * Flags sprintf() calls where the placeholder count in a translation string
 * does not match the number of extra arguments passed to sprintf().
 *
 * Identifier: apermo.sprintf.translationMismatch
 *
 * @implements Rule<FuncCall>
 */
final class SprintfTranslationMismatchRule implements Rule {

	/**
	 * Translation functions whose first argument is the format string.
	 *
	 * @var list<string>
	 */
	private const SINGLE_FORMAT_FUNCTIONS = [
		'__',
		'_e',
		'esc_html__',
		'esc_attr__',
		'esc_html_e',
		'esc_attr_e',
		'_x',
		'_ex',
	];

	/**
	 * Translation functions whose first two arguments are singular/plural format strings.
	 *
	 * @var list<string>
	 */
	private const PLURAL_FORMAT_FUNCTIONS = [
		'_n',
		'_nx',
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

		if ( $node->name->toLowerString() !== 'sprintf' ) {
			return [];
		}

		$args = $node->getArgs();

		if ( count( $args ) < 1 ) {
			return [];
		}

		$first_arg = $args[0]->value;

		if ( ! $first_arg instanceof FuncCall || ! $first_arg->name instanceof Name ) {
			return [];
		}

		$translation_func = $first_arg->name->toLowerString();
		$translation_args = $first_arg->getArgs();
		$sprintf_arg_count = count( $args ) - 1;

		if ( in_array( $translation_func, self::SINGLE_FORMAT_FUNCTIONS, true ) ) {
			return $this->check_single_format( $node->name->toLowerString(), $translation_args, $sprintf_arg_count );
		}

		if ( in_array( $translation_func, self::PLURAL_FORMAT_FUNCTIONS, true ) ) {
			return $this->check_plural_format( $node->name->toLowerString(), $translation_args, $sprintf_arg_count );
		}

		return [];
	}

	/**
	 * Checks a single-format translation function.
	 *
	 * @param string                    $sprintf_func     Function name for error message.
	 * @param list<\PhpParser\Node\Arg> $translation_args Arguments of the translation call.
	 * @param int                       $sprintf_arg_count Number of extra sprintf() arguments.
	 * @return list<\PHPStan\Rules\IdentifierRuleError>
	 */
	private function check_single_format( string $sprintf_func, array $translation_args, int $sprintf_arg_count ): array { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint -- list<> is not a native PHP type
		if ( ! isset( $translation_args[0] ) || ! $translation_args[0]->value instanceof String_ ) {
			return [];
		}

		$placeholder_count = $this->count_placeholders( $translation_args[0]->value->value );

		if ( $placeholder_count === $sprintf_arg_count ) {
			return [];
		}

		return [ $this->build_error( $sprintf_func, $placeholder_count, $sprintf_arg_count ) ];
	}

	/**
	 * Checks a plural-form translation function (_n, _nx).
	 * Only reports if both singular and plural have the same placeholder count —
	 * asymmetric forms (e.g. 'One item' vs '%d items') are intentional and skipped.
	 *
	 * @param string                    $sprintf_func     Function name for error message.
	 * @param list<\PhpParser\Node\Arg> $translation_args Arguments of the translation call.
	 * @param int                       $sprintf_arg_count Number of extra sprintf() arguments.
	 * @return list<\PHPStan\Rules\IdentifierRuleError>
	 */
	private function check_plural_format( string $sprintf_func, array $translation_args, int $sprintf_arg_count ): array { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint -- list<> is not a native PHP type
		if (
			! isset( $translation_args[0] ) || ! $translation_args[0]->value instanceof String_ ||
			! isset( $translation_args[1] ) || ! $translation_args[1]->value instanceof String_
		) {
			return [];
		}

		$singular_count = $this->count_placeholders( $translation_args[0]->value->value );
		$plural_count   = $this->count_placeholders( $translation_args[1]->value->value );

		// Asymmetric forms are intentional — skip to avoid false positives.
		if ( $singular_count !== $plural_count ) {
			return [];
		}

		if ( $singular_count === $sprintf_arg_count ) {
			return [];
		}

		return [ $this->build_error( $sprintf_func, $singular_count, $sprintf_arg_count ) ];
	}

	/**
	 * Builds the rule error.
	 *
	 * @param string $sprintf_func      Function name.
	 * @param int    $placeholder_count Number of placeholders in the format string.
	 * @param int    $arg_count         Number of arguments passed to sprintf().
	 * @return \PHPStan\Rules\IdentifierRuleError
	 */
	private function build_error( string $sprintf_func, int $placeholder_count, int $arg_count ): \PHPStan\Rules\IdentifierRuleError {
		return RuleErrorBuilder::message(
			sprintf(
				'%s() format string has %d %s but %d %s.',
				$sprintf_func,
				$placeholder_count,
				$placeholder_count === 1 ? 'placeholder' : 'placeholders',
				$arg_count,
				$arg_count === 1 ? 'argument' : 'arguments',
			)
		)->identifier( 'apermo.sprintf.translationMismatch' )->build();
	}

	/**
	 * Counts the number of sprintf format placeholders in a string.
	 *
	 * Handles:
	 * - Standard placeholders: %s, %d, %f, etc.
	 * - Numbered placeholders: %1$s — counts unique argument positions
	 * - Escaped percent: %% — not counted
	 *
	 * @param string $format The format string to analyse.
	 * @return int
	 */
	private function count_placeholders( string $format ): int {
		// Remove escaped percent signs so they are not counted.
		$clean = str_replace( '%%', '', $format );

		// Check for numbered placeholders first (%1$s, %2$d, …).
		preg_match_all( '/(?<!%)%(\d+)\$/', $clean, $numbered );

		if ( count( $numbered[1] ) > 0 ) {
			return count( array_unique( $numbered[1] ) );
		}

		// Count standard (non-numbered) placeholders.
		preg_match_all( '/(?<!%)%[-+]?(?:\'[^\']+|\d+)?(?:\.\d+)?[sdfegxXoubceEF]/i', $clean, $standard );

		return count( $standard[0] );
	}
}
