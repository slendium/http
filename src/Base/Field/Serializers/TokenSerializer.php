<?php

namespace Slendium\Http\Base\Field\Serializers;

use Slendium\Http\Base\HttpChar;
use Slendium\Http\Base\SerializeException;
use Slendium\Http\Field\Item;

/**
 * @internal
 * @see https://datatracker.ietf.org/doc/html/rfc9651#section-4.1.7
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class TokenSerializer {

	public static function serialize9651(Item\Token|string $input): SerializeException|string {
		if ($input instanceof Item\Token) {
			$input = $input->value;
		}

		// 1. Convert input_token into a sequence of ASCII characters; if conversion fails, fail serialization.
		// PHP strings are binary/ASCII strings by default

		// 2. If the first character of input_token is not ALPHA or "*", or the remaining portion
		//    contains a character not in tchar, ":", or "/", fail serialization.
		for ($i = 0; $i < \strlen($input); $i += 1) {
			$char = $input[$i];
			if ($i === 0 && !HttpChar::isAlpha($char) && $char !== '*') {
				return new SerializeException("First character of token may only be ALPHA or `*', not `$char` (RFC 9651, 4.1.7, 2)");
			} else if ($i !== 0 && !HttpChar::isTChar($char) && $char !== ':' && $char !== '/') {
				return new SerializeException("Token may only contain TCHAR, `:` or `/`, not `$char` (RFC 9651, 4.1.7, 2)");
			}
		}

		// 3. Let output be an empty string.
		// 4. Append input_token to output.
		// 5. Return output.
		return $input;
	}

}
