<?php

namespace Slendium\Http\Base\Field\Parsers;

use Slendium\Http\Base\HttpChar;
use Slendium\Http\Base\ParseException;
use Slendium\Http\Base\StringConsumer;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class KeyParser {

	/**
	 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2.3.3
	 * @return lowercase-string&non-empty-string
	 */
	public static function parse9651(StringConsumer $inputString): string {
		// 1. If the first character of input_string is not lcalpha or "*", fail parsing.
		$firstChar = $inputString->peek(1);
		if ($firstChar !== '*' && !HttpChar::isLowercaseAlpha($firstChar)) {
			throw new ParseException('Key must start with "*" or lowercase alpha (RFC 9651, 4.2.3.3, 1)');
		}

		// 2. Let output_string be an empty string.
		// We set it to $firstChar to allow static analyzers to recognize outputString as non-empty
		$outputString = $firstChar;
		$inputString->discard(1);

		// 3. While input_string is not empty:
		while ($inputString->hasMore) {
			// 3.1. If the first character of input_string is not one of lcalpha, DIGIT, "_", "-", ".", or "*", return output_string.
			$char = $inputString->peek(1);
			if (!HttpChar::isLowercaseAlpha($char)
				&& !HttpChar::isDigit($char)
				&& $char !== '_'
				&& $char !== '-'
				&& $char !== '.'
				&& $char !== '*'
			) {
				return $outputString; // @phpstan-ignore return.type (PHPStan does not recognize lowercase?)
			}
			// 3.2. Let char be the result of consuming the first character of input_string.
			// 3.3. Append char to output_string.
			$outputString .= $inputString->consume(1);
		}
		return $outputString; // @phpstan-ignore return.type (PHPStan does not recognize lowercase?)
	}

}
