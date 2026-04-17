<?php

namespace Slendium\Http\Base\Field\Serializers;

use Slendium\Http\Base\SerializeException;
use Slendium\Http\Field\Item;

/**
 * @internal
 * @see https://datatracker.ietf.org/doc/html/rfc9651#section-4.1.4
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class IntegerSerializer {

	private const MIN = -999999999999999;

	private const MAX = 999999999999999;

	public static function serialize9651(Item\Integer|int $input): SerializeException|string {
		if ($input instanceof Item\Integer) {
			$input = $input->value;
		}

		// 1. If input_integer is not an integer in the range of -999,999,999,999,999 to 999,999,999,999,999 inclusive, fail serialization.
		if ($input < self::MIN || $input > self::MAX) {
			return new SerializeException('Integer to serialize outside of valid range: <-999,999,999,999,999; 999,999,999,999,999> (RFC 9651, 4.1.4-1)');
		}

		// 2. Let output be an empty string.
		// 3. If input_integer is less than (but not equal to) 0, append "-" to output.
		// 4. Append input_integer's numeric value represented in base 10 using only decimal digits to output.
		// 5. Return output.
		return (string)$input;
	}

}
