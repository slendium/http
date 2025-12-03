<?php

namespace Slendium\Http\Base\Field\Parsers;

use DateTime;
use DateTimeImmutable;

use Slendium\Http\Base\ParseException;
use Slendium\Http\Base\StringConsumer;
use Slendium\Http\Field\Item;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class DateParser {

	/** @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2.9 */
	public static function parse9651(StringConsumer $inputString): Item\Date {
		// 1. If the first character of input_string is not "@", fail parsing.
		if ($inputString->peek(1) !== '@') {
			throw new ParseException('First character of a date must be "@" (RFC 9651, 4.2.9, 1)');
		}

		// 2. Discard the first character of input_string.
		$inputString->discard(1);

		// 3. Let output_date be the result of running Parsing an Integer or Decimal (Section 4.2.4) with input_string.
		$outputDate = IntegerOrDecimalParser::parse9651($inputString)->value;

		// 4. If output_date is a Decimal, fail parsing.
		if (\is_float($outputDate)) {
			throw new ParseException('Date must be an integer, not a decimal (RFC 9651, 4.2.9, 4)');
		}

		// 5. Return output_date.
		$date = new DateTime;
		$date->setTimestamp($outputDate);
		return Item::Date(DateTimeImmutable::createFromMutable($date));
	}

}
