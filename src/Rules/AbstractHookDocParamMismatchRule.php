<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Rules;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\NullType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\ObjectWithoutClassType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\VerbosityLevel;
use PHPStan\Type\VoidType;

/**
 * Abstract base for rules that detect @param mismatches in WordPress hook PHPDoc blocks.
 *
 * Subclasses declare which hook functions to analyse by implementing getHookFunctions().
 *
 * @implements Rule<Expression>
 */
abstract class AbstractHookDocParamMismatchRule implements Rule {

	/**
	 * Constructs the rule with PHPDoc parsing dependencies.
	 *
	 * @param PhpDocParser $php_doc_parser PHPDoc parser.
	 * @param Lexer        $lexer          Lexer for tokenising PHPDoc strings.
	 */
	public function __construct(
		private readonly PhpDocParser $php_doc_parser,
		private readonly Lexer $lexer,
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
	 * @return class-string<Expression>
	 */
	public function getNodeType(): string {
		return Expression::class;
	}

	/**
	 * Processes a statement node, looking for hook calls with PHPDoc @param mismatches.
	 *
	 * @param Expression $node  Statement node.
	 * @param Scope      $scope Analysis scope.
	 * @return list<\PHPStan\Rules\IdentifierRuleError>
	 */
	public function processNode( Node $node, Scope $scope ): array { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint -- Rule interface requires Node
		if ( ! $node->expr instanceof FuncCall ) {
			return [];
		}

		$func_call = $node->expr;

		if ( ! $func_call->name instanceof Name ) {
			return [];
		}

		$function_name = $func_call->name->toLowerString();

		if ( ! in_array( $function_name, $this->getHookFunctions(), true ) ) {
			return [];
		}

		// Skip _ref_array and _deprecated variants — array-based args require shape analysis.
		if ( str_contains( $function_name, '_ref_array' ) || str_contains( $function_name, '_deprecated' ) ) {
			return [];
		}

		$doc_comment = $this->find_doc_comment( $node );

		if ( $doc_comment === null ) {
			return [];
		}

		// Skip cross-reference blocks used in WordPress core
		// (e.g. "/** This action is documented in wp-includes/post.php */").
		if (
			str_contains( $doc_comment, 'This action is documented in' ) ||
			str_contains( $doc_comment, 'This filter is documented in' )
		) {
			return [];
		}

		$tokens     = new TokenIterator( $this->lexer->tokenize( $doc_comment ) );
		$php_doc    = $this->php_doc_parser->parse( $tokens );
		$param_tags = $php_doc->getParamTagValues();

		// First arg is the hook name string — skip it.
		$hook_args = array_slice( $func_call->getArgs(), 1 );

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

		foreach ( $hook_args as $index => $arg ) {
			$declared_type = $this->resolve_type_node( $param_tags[ $index ]->type );
			$actual_type   = $scope->getType( $arg->value );

			if ( $declared_type->isSuperTypeOf( $actual_type )->no() ) {
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

	/**
	 * Finds the PHPDoc comment attached to the statement node.
	 *
	 * @param Expression $node Statement node.
	 * @return string|null
	 */
	private function find_doc_comment( Expression $node ): ?string {
		$comments = $node->getAttribute( 'comments', [] );

		// Reverse to pick the doc block closest to (immediately before) the call.
		foreach ( array_reverse( $comments ) as $comment ) {
			if ( $comment instanceof Doc ) {
				return $comment->getText();
			}
		}

		return null;
	}

	/**
	 * Resolves a PHPDoc TypeNode to a PHPStan Type.
	 * Returns MixedType for complex or unresolvable types to avoid false positives.
	 *
	 * @param TypeNode $type_node PHPDoc type node to resolve.
	 * @return Type
	 */
	private function resolve_type_node( TypeNode $type_node ): Type {
		if ( $type_node instanceof NullableTypeNode ) {
			return TypeCombinator::addNull( $this->resolve_type_node( $type_node->type ) );
		}

		if ( $type_node instanceof UnionTypeNode ) {
			return TypeCombinator::union(
				...array_map( [ $this, 'resolve_type_node' ], $type_node->types )
			);
		}

		if ( $type_node instanceof IntersectionTypeNode ) {
			return TypeCombinator::intersect(
				...array_map( [ $this, 'resolve_type_node' ], $type_node->types )
			);
		}

		if ( $type_node instanceof ArrayTypeNode ) {
			return new ArrayType( new MixedType(), $this->resolve_type_node( $type_node->type ) );
		}

		if ( ! $type_node instanceof IdentifierTypeNode ) {
			return new MixedType();
		}

		$name = ltrim( $type_node->name, '\\' );

		return match ( strtolower( $name ) ) {
			'int', 'integer'   => new IntegerType(),
			'string'           => new StringType(),
			'bool', 'boolean'  => new BooleanType(),
			'float', 'double'  => new FloatType(),
			'null'             => new NullType(),
			'true'             => new ConstantBooleanType( true ),
			'false'            => new ConstantBooleanType( false ),
			'void'             => new VoidType(),
			'array'            => new ArrayType( new MixedType(), new MixedType() ),
			'object'           => new ObjectWithoutClassType(),
			'mixed'            => new MixedType(),
			default            => $name !== '' ? new ObjectType( $name ) : new MixedType(),
		};
	}
}
