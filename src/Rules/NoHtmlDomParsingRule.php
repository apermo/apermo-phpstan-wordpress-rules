<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Flags DOMDocument::loadHTML(), loadHTMLFile(), and Tidy::parseString() method calls.
 *
 * @implements Rule<MethodCall>
 */
final class NoHtmlDomParsingRule implements Rule {

	/**
	 * Map of class names to their banned method names and error messages.
	 *
	 * @var array<string, array<string, string>>
	 */
	private const BANNED_METHODS = [
		'DOMDocument' => [
			'loadhtml'     => 'Do not use DOMDocument::loadHTML(). Use WP_HTML_Tag_Processor or WP_HTML_Processor instead.',
			'loadhtmlfile' => 'Do not use DOMDocument::loadHTMLFile(). Use WP_HTML_Tag_Processor or WP_HTML_Processor instead.',
		],
		'Tidy'        => [
			'parsestring' => 'Do not use Tidy::parseString(). Use WP_HTML_Tag_Processor or WP_HTML_Processor instead.',
		],
	];

	/**
	 * Returns the node type this rule processes.
	 *
	 * @return class-string<MethodCall>
	 */
	public function getNodeType(): string {
		return MethodCall::class;
	}

	/**
	 * Processes a method call node.
	 *
	 * @param \PhpParser\Node\Expr\MethodCall $node  Method call node.
	 * @param Scope                           $scope Analysis scope.
	 * @return list<\PHPStan\Rules\IdentifierRuleError>
	 */
	public function processNode( Node $node, Scope $scope ): array { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint -- Rule interface requires Node
		if ( ! $node->name instanceof Identifier ) {
			return [];
		}

		$method_name = strtolower( $node->name->name );
		$caller_type = $scope->getType( $node->var );

		foreach ( self::BANNED_METHODS as $class_name => $methods ) {
			if ( ! isset( $methods[ $method_name ] ) ) {
				continue;
			}

			$class_names = $caller_type->getObjectClassNames();

			foreach ( $class_names as $resolved_class ) {
				if ( strcasecmp( $resolved_class, $class_name ) === 0 ) {
					return [
						RuleErrorBuilder::message( $methods[ $method_name ] )
							->identifier( 'apermo.noHtmlDomParsing' )->build(),
					];
				}
			}
		}

		return [];
	}
}
