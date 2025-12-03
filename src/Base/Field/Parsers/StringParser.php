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
final class StringParser {

	/** @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2.6 */
	public static function parse9651(StringConsumer $inputString): Item\String_ {
		// 1. Let output_string be an empty string.
		$outputString = '';

		// 2. If the first character of input_string is not DQUOTE, fail parsing.
		if ($inputString->peek(1) !== '"') {
			throw new ParseException('The first character of a string must be a DQUOTE (RFC 9651, 4.2.5, 2)');
		}

		// 3. Discard the first character of input_string.
		$inputString->discard(1);

		// 4. While input_string is not empty:
		while ($inputString->hasMore) {
			// 4.1. Let char be the result of consuming the first character of input_string.
			$char = $inputString->consume(1);
			// 4.2. If char is a backslash ("\"):
			if ($char === '\\') {
				// 4.2.1. If input_string is now empty, fail parsing.
				if (!$inputString->hasMore) { // @phpstan-ignore booleanNot.alwaysFalse (PHPStan does not recognize iterator-like nature of the object)
					throw new ParseException('No more characters after backslash (RFC 9651, 4.2.5, 4.2.1)');
				}
				// 4.2.2. Let next_char be the result of consuming the first character of input_string.
				$nextChar = $inputString->consume(1);
				// 4.2.3. If next_char is not DQUOTE or "\", fail parsing.
				if ($nextChar !== '"' && $nextChar !== '\\') {
					throw new ParseException('Backslash not followed by a DQUOTE or another backslash (RFC 9651, 4.2.5, 4.2.3)');
				}
				// 4.2.4. Append next_char to output_string.
				$outputString .= $nextChar;
			// 4.3. Else, if char is DQUOTE, return output_string.
			} else if ($char === '"') {
				return Item::String($outputString);
			// 4.4. Else, if char is in the range %x00-1f or %x7f-ff (i.e., it is not in VCHAR or SP), fail parsing.
			} else if (HttpChar::isControl($char) || \ord($char) >= 127) {
				throw new ParseException('Strings must only consist of VCHAR or SP (RFC 9651, 4.2.5, 4.4)');
			// 4.5. Else, append char to output_string.
			} else {
				$outputString .= $char;
			}
		}
		// 5. Reached the end of input_string without finding a closing DQUOTE; fail parsing.
		throw new ParseException('Reached end of input without finding a DQUOTE to close the last string (RFC 9651, 4.2.5, 5)');
	}

}
