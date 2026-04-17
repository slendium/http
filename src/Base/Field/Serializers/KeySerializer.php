<?php

namespace Slendium\Http\Base\Field\Serializers;

use Slendium\Http\Base\HttpChar;
use Slendium\Http\Base\SerializeException;

/**
 * @internal
 * @see https://datatracker.ietf.org/doc/html/rfc9651#section-4.1.1.3
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class KeySerializer {

	/** @param non-empty-string $char */
	private static function isValidFirstCharacter(string $char): bool {
		return $char === '*' || HttpChar::isLowercaseAlpha($char);
	}

	/** @param non-empty-string $char */
	private static function isValidRemainderCharacter(string $char): bool {
		return match($char) {
			'_', '-', '.', '*', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' => true,
			default => HttpChar::isLowercaseAlpha($char)
		};
	}

	public static function serialize9651(mixed $input): SerializeException|string {
		// 1. Convert input_key into a sequence of ASCII characters; if conversion fails, fail serialization.
		if (!\is_string($input) && !\is_int($input) && !\is_float($input)) {
			return new SerializeException("Key could not be converted into a sequence of ASCII characters (RFC 9651, 4.1.1.3, 1)");
		}
		$input = (string)$input;

		if ($input === '') {
			return new SerializeException("First character of key must be `_` or LCALPHA (RFC 9651, 4.1.1.3, 3)");
		}

		for ($i = 0; $i < \strlen($input); $i += 1) {
			$char = $input[$i];
			// 3. If the first character of input_key is not lcalpha or "*", fail serialization.
			if ($i === 0 && !self::isValidFirstCharacter($char)) {
				return new SerializeException("First character of key must be `_` or LCALPHA, not `$char` (RFC 9651, 4.1.1.3, 3)");
			// 2. If input_key contains characters not in lcalpha, DIGIT, "_", "-", ".", or "*", fail serialization.
			} else if (!self::isValidRemainderCharacter($char)) {
				return new SerializeException("Characters in key must be LCALPHA, DIGIT, `_`, `-`, `.` or `*`, not `$char` (RFC 9651, 4.1.1.3, 2)");
			}
		}

		// 4. Let output be an empty string.
		// 5. Append input_key to output.
		// 6. Return output.
		return $input;
	}

}
