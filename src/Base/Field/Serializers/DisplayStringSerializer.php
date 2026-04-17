<?php

namespace Slendium\Http\Base\Field\Serializers;

use Slendium\Http\Base\SerializeException;
use Slendium\Http\Field\Item;

/**
 * @internal
 * @see https://datatracker.ietf.org/doc/html/rfc9651#section-4.1.11
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class DisplayStringSerializer {

	public static function serialize9651(Item\DisplayString|string $input): SerializeException|string {
		if ($input instanceof Item\DisplayString) {
			$input = $input->value;
		}

		// 1. If input_sequence is not a sequence of Unicode code points, fail serialization.
		if (!\mb_check_encoding($input, 'UTF-8')) {
			return new SerializeException('Display string must contain a valid sequence of unicode points (RFC 9651, 4.1.11, 1)');
		}

		// 2. Let byte_array be the result of applying UTF-8 encoding (Section 3 of [UTF8]) to input_sequence.
		//    If encoding fails, fail serialization.
		// $input is already a byte array

		// 3. Let encoded_string be a string containing "%" followed by DQUOTE.
		$encoded_string = '%"';

		// 4. For each byte in byte_array:
		for ($i = 0; $i < \strlen($input); $i += 1) {
			$byte = \ord($input[$i]);
			// 4.1. If byte is %x25 ("%"), %x22 (DQUOTE), or in the ranges %x00-1f or %x7f-ff:
			if ($byte === 0x25 || $byte === 0x22 || $byte <= 0x1F || $byte >= 0x7F) {
				// 4.1.1. Append "%" to encoded_string.
				// 4.2.2. Let encoded_byte be the result of applying base16 encoding (Section 8 of [RFC4648])
				//        to byte, with any alphabetic characters converted to lowercase.
				// 4.2.3. Append encoded_byte to encoded_string.
				$encoded_string .= '%'.\dechex($byte);
			// 4.2. Otherwise, decode byte as an ASCII character and append the result to encoded_string.
			} else {
				$encoded_string .= $input[$i];
			}
		}

		// 5. Append DQUOTE to encoded_string.
		// 6. Return encoded_string.
		return $encoded_string.'"';
	}

}
