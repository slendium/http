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
final class DisplayStringParser {

	/** @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2.10 */
	public static function parse9651(StringConsumer $inputString): Item\DisplayString {
		// 1. If the first two characters of input_string are not "%" followed by DQUOTE, fail parsing.
		if ($inputString->peek(2) !== '%"') {
			throw new ParseException('Display string must start with %" (RFC 9651, 4.2.10, 1)');
		}

		// 2. Discard the first two characters of input_string.
		$inputString->discard(2);

		// 3. Let byte_array be an empty byte array.
		$byteArray = '';

		// 4. While input_string is not empty:
		while ($inputString->hasMore) {
			// 4.1. Let char be the result of consuming the first character of input_string.
			$char = $inputString->consume(1);
			// 4.2. If char is in the range %x00-1f or %x7f-ff (i.e., it is not in VCHAR or SP), fail parsing.
			if (HttpChar::isControl($char) || \ord($char) >= 127) {
				throw new ParseException('Strings must only consist of VCHAR or SP (RFC 9651, 4.2.10, 4.2)');
			}
			// 4.3. If char is "%":
			if ($char === '%') {
				// 4.3.1. Let octet_hex be the result of consuming two characters from input_string. If there are not two characters, fail parsing.
				$octetHex = $inputString->consume(2);
				// 4.3.2. If octet_hex contains characters outside the range %x30-39 or %x61-66 (i.e., it is not in 0-9 or lowercase a-f), fail parsing.
				// 4.3.3. Let octet be the result of hex decoding octet_hex (Section 8 of [RFC4648]).
				$octet = @\hex2bin($octetHex);
				if ($octet === false) {
					throw new ParseException('Percent-encoding should only contain the characters 0-9 or a-f (RFC 9651, 4.2.10, 4.3.2)');
				}
				// 4.3.4. Append octet to byte_array.
				$byteArray .= $octet;
			// 4.4. If char is DQUOTE:
			} else if ($char === '"') {
				// 4.4.1. Let unicode_sequence be the result of decoding byte_array as a UTF-8 string (Section 3 of [UTF8]). Fail parsing if decoding fails.
				if (!\mb_check_encoding($byteArray, 'utf-8')) {
					throw new ParseException('Display string contained an invalid unicode sequence (RFC 9651, 4.2.10, 4.4.1)');
				}
				// 4.4.2. Return unicode_sequence.
				return Item::DisplayString($byteArray);
			// 4.5. Otherwise, if char is not "%" or DQUOTE:
			} else {
				// 4.5.1. Let byte be the result of applying ASCII encoding to char.
				// 4.5.2. Append byte to byte_array.
				$byteArray .= $char;
			}
		}
		// 5. Reached the end of input_string without finding a closing DQUOTE; fail parsing.
		throw new ParseException('Reached end of input without find a closing DQUOTE (RFC 9651, 4.2.10, 5)');
	}

}
