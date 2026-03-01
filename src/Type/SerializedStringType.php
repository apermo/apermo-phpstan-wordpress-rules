<?php

declare(strict_types=1);

namespace Apermo\PhpStanWordPressRules\Type;

use PHPStan\Type\StringType;
use PHPStan\Type\VerbosityLevel;

/**
 * Branded string type for values produced by serialize() or maybe_serialize().
 */
final class SerializedStringType extends StringType {

	/**
	 * Describes the type for error messages.
	 *
	 * @param VerbosityLevel $level Verbosity level.
	 * @return string
	 */
	public function describe( VerbosityLevel $level ): string {
		return 'serialized-string';
	}
}
