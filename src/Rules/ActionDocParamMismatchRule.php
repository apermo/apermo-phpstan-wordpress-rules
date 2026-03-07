<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Rules;

/**
 * Flags @param count and type mismatches in do_action() PHPDoc blocks.
 *
 * WordPress action invocations are documented with inline PHPDoc blocks. This rule
 * detects when the number of @param tags does not match the number of arguments
 * passed to the action, or when the declared @param type is incompatible with the
 * inferred argument type.
 *
 * Identifiers: apermo.hookDoc.paramCountMismatch, apermo.hookDoc.paramTypeMismatch
 */
final class ActionDocParamMismatchRule extends AbstractHookDocParamMismatchRule {

	/**
	 * Returns the do_action family of hook functions.
	 *
	 * @return list<string>
	 */
	protected function getHookFunctions(): array {
		return [
			'do_action',
			'do_action_ref_array',
			'do_action_deprecated',
		];
	}
}
