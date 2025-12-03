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
final class IntegerOrDecimalParser {

	/** @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2.4 */
	public static function parse9651(StringConsumer $inputString): Item\Integer|Item\Decimal {
		// 1. Let type be "integer".
		$type = 'integer';

		// 2. Let sign be 1.
		$sign = 1;

		// 3. Let input_number be an empty string.
		$inputNumber = '';

		// 4. If the first character of input_string is "-", consume it and set sign to -1.
		if ($inputString->peek(1) === '-') {
			$inputString->discard(1);
			$sign = -1;
		}

		// 5. If input_string is empty, there is an empty integer; fail parsing.
		if (!$inputString->hasMore) {
			throw new ParseException('An empty integer or decimal was encountered (RFC 9651, 4.2.4, 5)');
		}

		// 6. If the first character of input_string is not a DIGIT, fail parsing.
		if (!HttpChar::isDigit($inputString->peek(1))) {
			throw new ParseException('The first character of an integer or decimal must be a digit (RFC 9651, 4.2.4, 6)');
		}

		// 7. While input_string is not empty:
		while ($inputString->hasMore) {
			// 7.1. Let char be the result of consuming the first character of input_string.
			$char = $inputString->consume(1);
			// 7.2. If char is a DIGIT, append it to input_number.
			if (HttpChar::isDigit($char)) {
				$inputNumber .= $char;
			// 7.3. Else, if type is "integer" and char is ".":
			} else if ($type === 'integer' && $char === '.') {
				// 7.3.1. If input_number contains more than 12 characters, fail parsing.
				if (\strlen($inputNumber) > 12) {
					throw new ParseException('The input number contains more than 12 characters (RFC 9651, 4.2.4, 7.3.1)');
				// 7.3.2. Otherwise, append char to input_number and set type to "decimal".
				} else {
					$inputNumber .= $char;
					$type = 'decimal';
				}
			// 7.4. Otherwise, prepend char to input_string, and exit the loop.
			} else {
				$inputString->rewind(1);
				break;
			}
			// 7.5. If type is "integer" and input_number contains more than 15 characters, fail parsing.
			if ($type === 'integer' && \strlen($inputNumber) > 15) {
				throw new ParseException('The input number contains more than 15 characters (RFC 9651, 4.2.4, 7.5)');
			}
			// 7.6. If type is "decimal" and input_number contains more than 16 characters, fail parsing.
			if ($type === 'decimal' && \strlen($inputNumber) > 16) {
				throw new ParseException('The input number contains more than 16 characters (RFC 9651, 4.2.4, 7.6)');
			}
		}

		// 8. If type is "integer":
		if ($type === 'integer') {
			// 8.1. Let output_number be an Integer that is the result of parsing input_number as an integer.
			// 10. Let output_number be the product of output_number and sign.
			// 11. Return output_number.
			return Item::Integer((int)$inputNumber*$sign);
		}

		// 9. Otherwise:
		// 9.1. If the final character of input_number is ".", fail parsing.
		if ($inputNumber[\strlen($inputNumber)-1] === '.') { // @phpstan-ignore offsetAccess.notFound (yes it exists)
			throw new ParseException('The input number\'s final character is "." (RFC 9651, 4.2.4, 9.1)');
		}
		// 9.2. If the number of characters after "." in input_number is greater than three, fail parsing.
		if (\strlen(\explode('.', $inputNumber)[1] ?? '') > 3) {
			throw new ParseException('The input number contained more than three characters after the "." (RFC 9651, 4.2.4, 9.1)');
		}
		// 9.3. Let output_number be a Decimal that is the result of parsing input_number as a decimal number.
		// 10. Let output_number be the product of output_number and sign.
		// 11. Return output_number.
		return Item::Decimal((float)$inputNumber*$sign);
	}

}
