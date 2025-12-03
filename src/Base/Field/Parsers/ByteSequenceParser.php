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
final class ByteSequenceParser {

	/** @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2.7 */
	public static function parse9651(StringConsumer $inputString): Item\ByteSequence {
		// 1. If the first character of input_string is not ":", fail parsing.
		if ($inputString->peek(1) !== ':') {
			throw new ParseException('First character of a byte sequence must be ":" (RFC 9651, 4.2.7, 1)');
		}

		// 2. Discard the first character of input_string.
		$inputString->discard(1);

		// Step 3 can be found at the end of the method

		// 4. Let b64_content be the result of consuming content of input_string up to but not including the first instance of the character ":".
		// 5. Consume the ":" character at the beginning of input_string.
		$b64Content = '';
		while ($inputString->hasMore) {
			$char = $inputString->consume(1);
			if ($char === ':') {
				// 6. If b64_content contains a character not included in ALPHA, DIGIT, "+", "/", and "=", fail parsing.
				// 7. Let binary_content be the result of base64-decoding [RFC4648] b64_content, synthesizing padding if necessary (note the requirements about recipient behavior below). If base64 decoding fails, parsing fails.
				$binaryContent = \base64_decode($b64Content, strict: true);
				if ($binaryContent === false) {
					throw new ParseException('Base64 decoding of byte sequence failed (RFC 9651, 4.2.7, 6/7)');
				}
				return Item::ByteSequence($binaryContent);
			} else {
				$b64Content .= $char;
			}
		}

		// 3. If there is not a ":" character before the end of input_string, fail parsing.
		throw new ParseException('Reached end of input before end of byte sequence (RFC 9651, 4.2.7, 3)');
	}

}
