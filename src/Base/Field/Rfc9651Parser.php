<?php

namespace Slendium\Http\Base\Field;

use ArrayAccess;
use Countable;
use NoDiscard;
use Override;
use SplFixedArray;
use Traversable;

use Slendium\Http\Field\Item;
use Slendium\Http\Field\Parameterized as IParameterized;
use Slendium\Http\Field\StructuredValueParser;
use Slendium\Http\Base\ArrayView;
use Slendium\Http\Base\ParseException;
use Slendium\Http\Base\StringConsumer;
use Slendium\Http\Base\Field\Parsers\DictionaryParser;
use Slendium\Http\Base\Field\Parsers\ItemParser;
use Slendium\Http\Base\Field\Parsers\ListParser;

/**
 * Structured value parser which strictly follows RFC 9651.
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
class Rfc9651Parser implements StructuredValueParser {

	/** @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2 */
	#[NoDiscard]
	private static function startConsuming(string $input): StringConsumer {
		// 1. Convert input_bytes into an ASCII string input_string; if conversion fails, fail parsing.
		for ($i = 0; $i < \strlen($input); $i += 1) {
			if (\ord($input[$i]) > 127) {
				throw new ParseException('Input was not a valid ASCII string (RFC 9651, 4.2, 1)');
			}
		}

		$input = new StringConsumer($input);

		// 2. Discard any leading SP characters from input_string.
		while ($input->peek(1) === ' ') {
			$input->discard(1);
		}

		return $input;
	}

	/** @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2 */
	private static function endConsuming(StringConsumer $inputString): void {
		// 6. Discard any leading SP characters from input_string.
		while ($inputString->peek(1) === ' ') {
			$inputString->discard(1);
		}

		// 7. If input_string is not empty, fail parsing.
		if ($inputString->hasMore) {
			throw new ParseException('Unexpected characters after parsing (RFC 9651, 4.2, 7)');
		}
	}

	#[Override]
	public function parseList(string $input): Countable&Traversable {
		$input = self::startConsuming($input);
		$list = ListParser::parse9651($input);
		self::endConsuming($input);
		return $list;
	}

	#[Override]
	public function parseDictionary(string $input): ArrayAccess&Countable&Traversable {
		$input = self::startConsuming($input);
		$dictionary = DictionaryParser::parse9651($input);
		self::endConsuming($input);
		return $dictionary;
	}

	#[Override]
	public function parseItem(string $input): IParameterized {
		$input = self::startConsuming($input);
		$item = ItemParser::parse9651($input);
		self::endConsuming($input);
		return $item;
	}

}
