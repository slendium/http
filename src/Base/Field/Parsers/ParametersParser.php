<?php

namespace Slendium\Http\Base\Field\Parsers;

use ArrayAccess;
use Countable;
use Traversable;

use Slendium\Http\Field\Item;
use Slendium\Http\Base\HttpChar;
use Slendium\Http\Base\ParseException;
use Slendium\Http\Base\StringConsumer;
use Slendium\Http\Base\Field\Parameters;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class ParametersParser {

	/**
	 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2.3.2
	 * @return ArrayAccess<(non-empty-string&lowercase-string)|int<0,max>,?Item>&Countable&Traversable<non-empty-string&lowercase-string,Item>
	 */
	public static function parse9651(StringConsumer $inputString): ArrayAccess&Countable&Traversable {
		// 1. Let parameters be an empty, ordered map.
		$parameters = [ ];

		// 2. While input_string is not empty:
		while ($inputString->hasMore) {
			// 2.1 If the first character of input_string is not ";", exit the loop.
			if (!$inputString->peekEquals(';')) {
				break;
			}
			// 2.2. Consume the ";" character from the beginning of input_string.
			$inputString->discard(1);
			// 2.3. Discard any leading SP characters from input_string.
			$inputString->discardSpaces();
			// 2.4. Let param_key be the result of running Parsing a Key (Section 4.2.3.3) with input_string.
			$paramKey = KeyParser::parse9651($inputString);
			// 2.5. Let param_value be Boolean true.
			$paramValue = Item::Boolean(true);
			// 2.6. If the first character of input_string is "=":
			if ($inputString->peekEquals('=')) {
				// 2.6.1. Consume the "=" character at the beginning of input_string.
				$inputString->discard(1);
				// 2.6.2. Let param_value be the result of running Parsing a Bare Item (Section 4.2.3.1) with input_string.
				$paramValue = ItemParser::parseBareItem9651($inputString);
			}
			// 2.7. If parameters already contains a key param_key (comparing character for character), overwrite its value with param_value.
			// 2.8. Otherwise, append key param_key with value param_value to parameters.
			$parameters[$paramKey] = $paramValue;
		}

		// 3. Return parameters.
		return new Parameters($parameters);
	}

}
