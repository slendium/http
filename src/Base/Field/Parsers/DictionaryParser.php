<?php

namespace Slendium\Http\Base\Field\Parsers;

use ArrayAccess;
use Countable;
use Traversable;

use Slendium\Http\Base\ParseException;
use Slendium\Http\Base\StringConsumer;
use Slendium\Http\Base\Field\Dictionary;
use Slendium\Http\Base\Field\InnerList;
use Slendium\Http\Base\Field\Parameterized;
use Slendium\Http\Field\Item;
use Slendium\Http\Field\Parameterized as IParameterized;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class DictionaryParser {

	/**
	 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2.2
	 * @return (ArrayAccess<non-empty-string&lowercase-string,?IParameterized<(Countable&Traversable<int,IParameterized<Item>>)|Item>>)&Countable&Traversable<non-empty-string&lowercase-string,IParameterized<(Countable&Traversable<int,IParameterized<Item>>)|Item>>
	 */
	public static function parse9651(StringConsumer $inputString): ArrayAccess&Countable&Traversable {
		// 1. Let dictionary be an empty, ordered map.
		$dictionary = [ ];

		// 2. While input_string is not empty:
		while ($inputString->hasMore) {
			// 2.1. Let this_key be the result of running Parsing a Key (Section 4.2.3.3) with input_string.
			$thisKey = KeyParser::parse9651($inputString);
			$member = null;
			// 2.2. If the first character of input_string is "=":
			if ($inputString->peek(1) === '=') {
				// 2.2.1. Consume the first character of input_string.
				$inputString->discard(1);
				// 2.2.2. Let member be the result of running Parsing an Item or Inner List (Section 4.2.1.1) with input_string.
				$member = ListParser::parseItemOrInnerList9651($inputString);
			// 2.3. Otherwise:
			} else {
				// 2.3.1. Let value be Boolean true.
				$value = self::boolTrue();
				// 2.3.2. Let parameters be the result of running Parsing Parameters (Section 4.2.3.2) with input_string.
				$parameters = ParametersParser::parse9651($inputString);
				// 2.3.3. Let member be the tuple (value, parameters).
				$member = Parameterized::newInterface($value, $parameters);
			}
			// 2.4. If dictionary already contains a key this_key (comparing character for character), overwrite its value with member.
			// 2.5. Otherwise, append key this_key with value member to dictionary.
			$dictionary[$thisKey] = $member;
			// 2.6. Discard any leading OWS characters from input_string.
			$inputString->discardSpaces();
			// 2.7. If input_string is empty, return dictionary.
			if (!$inputString->hasMore) {
				return self::newDictionary($dictionary);
			}
			// 2.8. Consume the first character of input_string; if it is not ",", fail parsing.
			$firstChar = $inputString->consume(1);
			if ($firstChar !== ',') {
				throw new ParseException('Expected a comma after a dictionary entry (RFC 9651, 4.2.2, 2.8)');
			}
			// 2.9. Discard any leading OWS characters from input_string.
			$inputString->discardSpaces();
			// 2.10. If input_string is empty, there is a trailing comma; fail parsing.
			if (!$inputString->hasMore) {
				throw new ParseException('Unexpected trailing comma in dictionary (RFC 9651, 4.2.2, 2.10)');
			}
		}

		// 3. No structured data has been found; return dictionary (which is empty).
		return self::newDictionary($dictionary);
	}

	/** @param array<lowercase-string&non-empty-string,IParameterized<(Countable&Traversable<int,IParameterized<Item>>)|Item>|IParameterized<Item>> $dictionary */
	private static function newDictionary(array $dictionary): Dictionary {
		return new Dictionary($dictionary); // @phpstan-ignore argument.type (this function converts Wrapper<A>|Wrapper<B> to Wrapper<A|B> for static analysis)
	}

	private static function boolTrue(): Item {
		return Item::Boolean(true);
	}

}
