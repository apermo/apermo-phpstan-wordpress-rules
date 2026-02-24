<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Extensions;

use Apermo\PhpStanWordPressRules\Type\SerializedStringType;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\Type;

/**
 * Marks serialize() and maybe_serialize() return values as SerializedStringType.
 */
final class SerializeReturnTypeExtension implements DynamicFunctionReturnTypeExtension {

	/**
	 * Checks if this extension handles the given function.
	 *
	 * phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPStan interface
	 *
	 * @param FunctionReflection $functionReflection Function reflection.
	 * @return bool
	 */
	public function isFunctionSupported( FunctionReflection $functionReflection ): bool { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPStan interface
		return in_array(
			$functionReflection->getName(), // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPStan interface
			[ 'serialize', 'maybe_serialize' ],
			true,
		);
	}

	/**
	 * Returns SerializedStringType for tracked function calls.
	 *
	 * phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPStan interface
	 *
	 * @param FunctionReflection $functionReflection Function reflection.
	 * @param FuncCall           $functionCall       Function call node.
	 * @param Scope              $scope              Analysis scope.
	 * @return Type
	 */
	public function getTypeFromFunctionCall(
		FunctionReflection $functionReflection, // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPStan interface
		FuncCall $functionCall, // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- PHPStan interface
		Scope $scope,
	): Type {
		return new SerializedStringType();
	}
}
