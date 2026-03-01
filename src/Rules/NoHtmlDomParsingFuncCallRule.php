<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Flags tidy_parse_string() and tidy_parse_file() function calls.
 *
 * @implements Rule<FuncCall>
 */
final class NoHtmlDomParsingFuncCallRule implements Rule {

	/**
	 * Banned functions and their error messages.
	 *
	 * @var array<string, string>
	 */
	private const BANNED_FUNCTIONS = [
		'tidy_parse_string' => 'Do not use tidy_parse_string(). Use WP_HTML_Tag_Processor or WP_HTML_Processor instead.',
		'tidy_parse_file'   => 'Do not use tidy_parse_file(). Use WP_HTML_Tag_Processor or WP_HTML_Processor instead.',
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
	 * @param \PhpParser\Node\Expr\FuncCall $node  Function call node.
	 * @param Scope                         $scope Analysis scope.
	 * @return list<\PHPStan\Rules\IdentifierRuleError>
	 */
	public function processNode( Node $node, Scope $scope ): array { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint -- Rule interface requires Node
		if ( ! $node->name instanceof Name ) {
			return [];
		}

		$function_name = $node->name->toLowerString();

		if ( ! isset( self::BANNED_FUNCTIONS[ $function_name ] ) ) {
			return [];
		}

		return [
			RuleErrorBuilder::message( self::BANNED_FUNCTIONS[ $function_name ] )
				->identifier( 'apermo.noHtmlDomParsing' )->build(),
		];
	}
}
