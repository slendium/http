<?php

namespace Slendium\Http\Base\Field\Parsers;

use Countable;
use Traversable;

use Slendium\Http\Field\Item;
use Slendium\Http\Field\Parameterized as IParameterized;
use Slendium\Http\Base\ParseException;
use Slendium\Http\Base\StringConsumer;
use Slendium\Http\Base\Field\InnerList;
use Slendium\Http\Base\Field\List_;
use Slendium\Http\Base\Field\Parameterized;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class ListParser {

	/**
	 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2.1
	 * @return Countable&Traversable<int,IParameterized<(Countable&Traversable<int,IParameterized<Item>>)|Item>>
	 */
	public static function parse9651(StringConsumer $inputString): Countable&Traversable {
		// 1. Let members be an empty array.
		$members = [ ];

		// 2. While input_string is not empty:
		while ($inputString->hasMore) {
			// 2.1. Append the result of running Parsing an Item or Inner List (Section 4.2.1.1) with input_string to members.
			$members[] = self::parseItemOrInnerList9651($inputString);
			// 2.2. Discard any leading OWS characters from input_string.
			$inputString->discardSpaces();
			// 2.3. If input_string is empty, return members.
			if (!$inputString->hasMore) {
				return new List_($members);
			}
			// 2.4. Consume the first character of input_string; if it is not ",", fail parsing.
			$inputString->expect([ ',' ], 'Expected a "," after an item or inner list (RFC 9651, 4.2.1, 2.4)');
			$inputString->discard(1);
			// 2.5. Discard any leading OWS characters from input_string.
			$inputString->discardSpaces();
			// 2.6. If input_string is empty, there is a trailing comma; fail parsing.
			if (!$inputString->hasMore) {
				throw new ParseException('Trailing comma encountered (RFC 9651, 4.2.1, 2.6)');
			}
		}
		// 3. No structured data has been found; return members (which is empty).
		return new List_($members);
	}

	/**
	 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2.1.1
	 * @return IParameterized<(Countable&Traversable<int,IParameterized<Item>>)|Item>
	 */
	public static function parseItemOrInnerList9651(StringConsumer $inputString): IParameterized {
		// 1. If the first character of input_string is "(", return the result of running Parsing an Inner List (Section 4.2.1.2) with input_string.
		if ($inputString->peekEquals('(')) {
			return self::parseInnerList($inputString); // @phpstan-ignore return.type (PHPStan is technically correct, but in practice this makes no difference)
		} else {
			// 2. Return the result of running Parsing an Item (Section 4.2.3) with input_string.
			return ItemParser::parse9651($inputString); // @phpstan-ignore return.type (PHPStan is technically correct, but in practice this makes no difference)
		}
	}

	/**
	 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2.1.2
	 * @return IParameterized<Countable&Traversable<int,IParameterized<Item>>>
	 */
	private static function parseInnerList(StringConsumer $inputString): IParameterized {
		// 1. Consume the first character of input_string; if it is not "(", fail parsing.
		$inputString->expect([ '(' ], 'Expected inner list to start with "(" (RFC 9651, 4.2.1.2, 1');
		$inputString->discard(1);

		// 2. Let inner_list be an empty array.
		$innerList = [ ];

		// 3. While input_string is not empty:
		while ($inputString->hasMore) {
			// 3.1. Discard any leading SP characters from input_string.
			$inputString->discardSpaces();
			// 3.2. If the first character of input_string is ")":
			if ($inputString->peekEquals(')')) {
				// 3.2.1. Consume the first character of input_string.
				$inputString->discard(1);
				// 3.2.2. Let parameters be the result of running Parsing Parameters (Section 4.2.3.2) with input_string.
				$parameters = ParametersParser::parse9651($inputString);
				// 3.2.3. Return the tuple (inner_list, parameters).
				return new Parameterized(self::newInnerList($innerList), $parameters);
			}
			// 3.3. Let item be the result of running Parsing an Item (Section 4.2.3) with input_string.
			// 3.4. Append item to inner_list.
			$innerList[] = ItemParser::parse9651($inputString);
			// 3.5. If the first character of input_string is not SP or ")", fail parsing.
			$inputString->expect([ ' ', ')' ], 'Items must be separated by SP or followed by ")" to indicate the end of the inner list (RFC 9651, 4.2.1.2, 3.5)');
		}

		// 4. The end of the Inner List was not found; fail parsing.
		throw new ParseException('End of inner list not found (RFC 9651, 4.2.1.2, 4)');
	}

	/**
	 * @param iterable<IParameterized<Item>> $members
	 * @return Countable&Traversable<int,IParameterized<Item>>
	 */
	private static function newInnerList(iterable $members): Countable&Traversable {
		return new InnerList($members);
	}

}
