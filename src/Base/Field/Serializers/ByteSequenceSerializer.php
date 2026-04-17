<?php

namespace Slendium\Http\Base\Field\Serializers;

use Slendium\Http\Field\Item;

/**
 * @internal
 * @see https://datatracker.ietf.org/doc/html/rfc9651#section-4.1.8
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class ByteSequenceSerializer {

	public static function serialize9651(Item\ByteSequence|string $input): string {
		if ($input instanceof Item\ByteSequence) {
			$input = $input->value;
		}

		// 1. If input_bytes is not a sequence of bytes, fail serialization.
		// PHP strings are byte sequences by default

		// 2. Let output be an empty string.
		// 3. Append ":" to output.
		// 4. Append the result of base64-encoding input_bytes as per [RFC4648], Section 4, taking account
		//    of the requirements below.
		// 5. Append ":" to output.
		// 6. Return output.
		return ':'.\base64_encode($input).':';
	}

}
