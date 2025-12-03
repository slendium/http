<?php

namespace Slendium\Http\Base\Field\Parsers;

use Slendium\Http\Field\Item;
use Slendium\Http\Field\Parameterized as IParameterized;
use Slendium\Http\Base\ParseException;
use Slendium\Http\Base\StringConsumer;
use Slendium\Http\Base\Field\Parameterized;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class ItemParser {

	/**
	 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2.3
	 * @return IParameterized<Item>
	 */
	public static function parse9651(StringConsumer $inputString): IParameterized {
		// 1. Let bare_item be the result of running Parsing a Bare Item (Section 4.2.3.1) with input_string.
		$bareItem = self::parseBareItem9651($inputString);

		// 2. Let parameters be the result of running Parsing Parameters (Section 4.2.3.2) with input_string.
		$parameters = ParametersParser::parse9651($inputString);

		// 3. Return the tuple (bare_item, parameters).
		return new Parameterized($bareItem, $parameters);
	}

	/** @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2.3.1 */
	public static function parseBareItem9651(StringConsumer $inputString): Item {
		return match(\ord($inputString->peek(1))) {
			// 1. If the first character of input_string is a "-" or a DIGIT, return the result of running Parsing an Integer or Decimal (Section 4.2.4) with input_string.
			\ord('-'), /* 0-9 */ 48, 49, 50, 51, 52, 53, 54, 55, 56, 57
				=> IntegerOrDecimalParser::parse9651($inputString),

			// 2. If the first character of input_string is a DQUOTE, return the result of running Parsing a String (Section 4.2.5) with input_string.
			\ord('"') => StringParser::parse9651($inputString),

			// 3. If the first character of input_string is an ALPHA or "*", return the result of running Parsing a Token (Section 4.2.6) with input_string.
			\ord('*'),
			/* A-Z */ 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90,
			/* a-z */ 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122,
				=> TokenParser::parse9651($inputString),

			// 4. If the first character of input_string is ":", return the result of running Parsing a Byte Sequence (Section 4.2.7) with input_string.
			\ord(':') => ByteSequenceParser::parse9651($inputString),

			// 5. If the first character of input_string is "?", return the result of running Parsing a Boolean (Section 4.2.8) with input_string.
			\ord('?') => BooleanParser::parse9651($inputString),

			// 6. If the first character of input_string is "@", return the result of running Parsing a Date (Section 4.2.9) with input_string.
			\ord('@') => DateParser::parse9651($inputString),

			// 7. If the first character of input_string is "%", return the result of running Parsing a Display String (Section 4.2.10) with input_string.
			\ord('%') => DisplayStringParser::parse9651($inputString),

			// 8. Otherwise, the item type is unrecognized; fail parsing.
			default => throw new ParseException('The item type is unrecognized (RFC 9651, 4.2.3.1, 8)')
		};
	}

}
