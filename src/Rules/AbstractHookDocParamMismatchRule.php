<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\Type\VerbosityLevel;
use SzepeViktor\PHPStan\WordPress\HookDocBlock;

/**
 * Abstract base for rules that detect @param mismatches in WordPress hook PHPDoc blocks.
 *
 * Uses szepeviktor's HookDocBlock for PHPDoc resolution (via HookDocsVisitor) and
 * RuleLevelHelper for type acceptance checks. Subclasses declare which hook
 * functions to analyse by implementing getHookFunctions().
 *
 * @implements Rule<FuncCall>
 */
abstract class AbstractHookDocParamMismatchRule implements Rule {

	/**
	 * Constructs the rule with szepeviktor's hook PHPDoc and type-checking services.
	 *
	 * @param HookDocBlock    $hook_doc_block    PHPDoc resolver for hook calls.
	 * @param RuleLevelHelper $rule_level_helper PHPStan type acceptance helper.
	 */
	public function __construct(
		private readonly HookDocBlock $hook_doc_block,
		private readonly RuleLevelHelper $rule_level_helper,
	) {}

	/**
	 * Returns the hook function names this rule should process (e.g. 'do_action').
	 * Only functions whose names do NOT contain '_ref_array' or '_deprecated'
	 * are fully checked; the others are skipped.
	 *
	 * @return list<string>
	 */
	abstract protected function getHookFunctions(): array;

	/**
	 * Returns the node type this rule processes.
	 *
	 * @return class-string<FuncCall>
	 */
	public function getNodeType(): string {
		return FuncCall::class;
	}

	/**
	 * Processes a function call node, checking for PHPDoc @param mismatches.
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

		if ( ! in_array( $function_name, $this->getHookFunctions(), true ) ) {
			return [];
		}

		// Skip _ref_array and _deprecated variants — array-based args require shape analysis.
		if ( str_contains( $function_name, '_ref_array' ) || str_contains( $function_name, '_deprecated' ) ) {
			return [];
		}

		$resolved_php_doc = $this->hook_doc_block->getNullableHookDocBlock( $node, $scope );

		if ( $resolved_php_doc === null ) {
			return [];
		}

		// Skip cross-reference blocks used in WordPress core
		// (e.g. "/** This action is documented in wp-includes/post.php */").
		$doc_string = $resolved_php_doc->getPhpDocString();

		if (
			str_contains( $doc_string, 'This action is documented in' ) ||
			str_contains( $doc_string, 'This filter is documented in' )
		) {
			return [];
		}

		$param_tags = $resolved_php_doc->getParamTags();

		// First arg is the hook name string — skip it.
		$hook_args = array_slice( $node->getArgs(), 1 );

		// Skip if any argument is unpacked — static count cannot be determined.
		foreach ( $hook_args as $arg ) {
			if ( $arg->unpack ) {
				return [];
			}
		}

		$param_count = count( $param_tags );
		$arg_count   = count( $hook_args );

		if ( $param_count !== $arg_count ) {
			return [
				RuleErrorBuilder::message(
					sprintf(
						'%s() PHPDoc has %d @param %s but the hook call passes %d %s.',
						$function_name,
						$param_count,
						$param_count === 1 ? 'tag' : 'tags',
						$arg_count,
						$arg_count === 1 ? 'argument' : 'arguments',
					)
				)->identifier( 'apermo.hookDoc.paramCountMismatch' )->build(),
			];
		}

		$errors = [];
		$param_tags_indexed = array_values( $param_tags );

		foreach ( $hook_args as $index => $arg ) {
			$declared_type = $param_tags_indexed[ $index ]->getType();
			$actual_type   = $scope->getType( $arg->value );

			$accepted = $this->rule_level_helper->accepts( $declared_type, $actual_type, $scope->isDeclareStrictTypes() );

			if ( ! $accepted->result ) {
				$errors[] = RuleErrorBuilder::message(
					sprintf(
						'%s() @param #%d declares type %s but %s is passed.',
						$function_name,
						$index + 1,
						$declared_type->describe( VerbosityLevel::typeOnly() ),
						$actual_type->describe( VerbosityLevel::typeOnly() ),
					)
				)->identifier( 'apermo.hookDoc.paramTypeMismatch' )->build();
			}
		}

		return $errors;
	}
}
