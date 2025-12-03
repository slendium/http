<?php

namespace Slendium\Http\Base\Field\Parsers;

use Slendium\Http\Base\HttpChar;
use Slendium\Http\Base\ParseException;
use Slendium\Http\Base\StringConsumer;
use Slendium\Http\Field\Item;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class TokenParser {

	/** @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2.6 */
	public static function parse9651(StringConsumer $inputString): Item\Token {
		// 1. If the first character of input_string is not ALPHA or "*", fail parsing.
		$firstChar = $inputString->peek(1);
		if (!HttpChar::isAlpha($firstChar) && $firstChar !== '*') {
			throw new ParseException('First character of a token must be ALPHA or "*" (RFC 9651, 4.2.6, 1)');
		}
		$inputString->discard(1);

		// 2. Let output_string be an empty string.
		// Deviating from the spec so outputString is detected as non-empty-string by static analyzer
		$outputString = $firstChar;

		// 3. While input_string is not empty:
		while ($inputString->hasMore) {
			$char = $inputString->peek(1);
			// 3.1. If the first character of input_string is not in tchar, ":", or "/", return output_string.
			if (!HttpChar::isTChar($char) && $char !== ':' && $char !== '/') {
				return Item::Token($outputString);
			}
			// 3.2. Let char be the result of consuming the first character of input_string.
			// 3.3. Append char to output_string.
			$outputString .= $inputString->consume(1);
		}

		// 4. Return output_string.
		return Item::Token($outputString);
	}

}
