<?php

namespace Slendium\Http\Base\Field\Parsers;

use Slendium\Http\Base\ParseException;
use Slendium\Http\Base\StringConsumer;
use Slendium\Http\Field\Item;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class BooleanParser {

	/** @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2.8 */
	public static function parse9651(StringConsumer $inputString): Item\Boolean {
		// 1. If the first character of input_string is not "?", fail parsing.
		if ($inputString->peek(1) !== '?') {
			throw new ParseException('First character of a boolean must be "?" (RFC 9651, 4.2.8, 1)');
		}

		// 2. Discard the first character of input_string.
		$inputString->discard(1);

		return match($inputString->consume(1)) {
			// 3. If the first character of input_string matches "1", discard the first character, and return true.
			'1' => Item::Boolean(true),
			// 4. If the first character of input_string matches "0", discard the first character, and return false.
			'0' => Item::Boolean(false),
			// 5. No value has matched; fail parsing.
			default => throw new ParseException('Boolean value must be either "?1" or "?0" (RFC 9651, 4.2.8, 5)')
		};
	}

}
