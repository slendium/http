<?php

namespace Slendium\Http\Base\Field\Serializers;

use Slendium\Http\Base\SerializeException;
use Slendium\Http\Field\Item;

/**
 * @internal
 * @see https://datatracker.ietf.org/doc/html/rfc9651#section-4.1.6
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class StringSerializer {

	public static function serialize9651(Item\String_|string $input): SerializeException|string {
		if ($input instanceof Item\String_) {
			$input = $input->value;
		}

		// 1. Convert input_string into a sequence of ASCII characters; if conversion fails, fail serialization.
		// PHP strings are binary/ASCII strings by default

		// Step 2 has been moved into the loop (to avoid iterating twice) since it makes no difference in the output

		// 3. Let output be the string DQUOTE.
		$output = '"';

		// 4. For each character char in input_string:
		for ($i = 0; $i < \strlen($input); $i += 1) {
			$ord = \ord($input[$i]);
			// 2. If input_string contains characters in the range %x00-1f or %x7f-ff (i.e., not in VCHAR or SP), fail serialization.
			if ($ord <= 0x1f || $ord >= 0x7f) {
				return new SerializeException('String to serialize cannot contain characters in the range 0x00-1f or 0x7f-ff (RFC 9651, 4.1.6, 2)');
			}
			// 4.1. If char is "\" or DQUOTE:
			if ($ord === \ord('\\') || $ord === \ord('"')) {
				// 4.1.1. Append "\" to output.
				$output .= '\\';
			}
			// 4.2. Append char to output.
			$output .= $input[$i];
		}

		// 5. Append DQUOTE to output.
		// 6. Return output.
		return $output.'"';
	}

}
