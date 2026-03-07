<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Rules;

/**
 * Flags @param count and type mismatches in apply_filters() PHPDoc blocks.
 *
 * WordPress filter invocations are documented with inline PHPDoc blocks. This rule
 * detects when the number of @param tags does not match the number of arguments
 * passed to the filter, or when the declared @param type is incompatible with the
 * inferred argument type.
 *
 * Identifiers: apermo.hookDoc.paramCountMismatch, apermo.hookDoc.paramTypeMismatch
 */
final class FilterDocParamMismatchRule extends AbstractHookDocParamMismatchRule {

	/**
	 * Returns the apply_filters family of hook functions.
	 *
	 * For apply_filters the first @param documents the filtered value (the value
	 * being passed through the filter), and subsequent @params document extra
	 * arguments — identical positional mapping to do_action.
	 *
	 * apply_filters_ref_array and apply_filters_deprecated are included so the
	 * rule recognises their call sites; the base class skips them at runtime
	 * because their args are array-based and cannot be statically counted.
	 *
	 * @return list<string>
	 */
	protected function getHookFunctions(): array {
		return [
			'apply_filters',
			'apply_filters_ref_array',
			'apply_filters_deprecated',
		];
	}
}
