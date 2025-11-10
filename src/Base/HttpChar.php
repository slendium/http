<?php

namespace Slendium\Http\Base;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class HttpChar {

	private const LCALPHA = [ // @phpstan-ignore classConstant.unused (exists for PHPStan asserts)
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'
	];

	private const UCALPHA = [ // @phpstan-ignore classConstant.unused (exists for PHPStan asserts)
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
	];

	private const DIGIT = [ // @phpstan-ignore classConstant.unused (exists for PHPStan asserts)
		'0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
	];

	/** @phpstan-assert-if-true '!'|'#'|'$'|'%'|'&'|'\''|'*'|'+'|'-'|'.'|'^'|'_'|'`'|'|'|'~'|value-of<self::LCALPHA>|value-of<self::UCALPHA> $char */
	public static function isTChar(string $char): bool {
		return match($char) {
			'!', '#', '$', '%', '&', "'", '*', '+', '-', '.', '^', '_', '`', '|', '~' => true,
			default => self::isAlpha($char)
		};
	}

	public static function isControl(string $char): bool {
		return \ord($char) <= 31;
	}

	/** @phpstan-assert-if-true value-of<self::LCALPHA>|value-of<self::UCALPHA> $char */
	public static function isAlpha(string $char): bool {
		$char = \ord($char);
		return ($char >= 65 && $char <= 90) || ($char >= 97 && $char <= 122);
	}

	/** @phpstan-assert-if-true value-of<self::LCALPHA> $char */
	public static function isLowercaseAlpha(string $char): bool {
		$char = \ord($char);
		return $char >= 97 && $char <= 122;
	}

	/** @phpstan-assert-if-true value-of<self::DIGIT> $char */
	public static function isDigit(string $char): bool {
		$char = \ord($char);
		return $char >= 48 && $char <= 57;
	}

	/** @phpstan-assert-if-true '0'|'1' $char */
	public static function isBit(string $char): bool {
		$char = \ord($char);
		return $char === 48 || $char === 49;
	}

}
