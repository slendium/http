<?php

namespace Slendium\Http\Content;

use Slendium\Http\Base\HttpChar;
use Slendium\Http\Base\ParseException;
use Slendium\Http\Base\StringConsumer;
use Slendium\Http\Content\MediaType;
use Slendium\Http\Content\MediaTypeName;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2025-2026
 */
class MediaTypeParser {

	/** @see https://www.rfc-editor.org/rfc/rfc6838#section-4.2 */
	public static function parseString(string $inputString): ParseException|MediaType {
		$inputString = new StringConsumer(\trim($inputString));
		$major = self::parseName($inputString);
		if ($major instanceof ParseException) {
			return $major;
		}

		if ($inputString->peek(1) !== '/') {
			return new ParseException('Main type and subtype of media type must be separated by a "/" (RFC 6838 4.2)');
		}

		$inputString->discard(1);
		$minor = self::parseName($inputString);
		if ($minor instanceof ParseException) {
			return $minor;
		}

		return new ReadOnlyMediaType($major, $minor);
	}

	private static function parseName(StringConsumer $inputString): ParseException|MediaTypeName {
		$firstChar = $inputString->peek(1);
		if ($firstChar === '' || !HttpChar::isAlpha($firstChar) && !HttpChar::isDigit($firstChar)) {
			return new ParseException('First character of a media type name must be ALPHA or DIGIT (RFC 6838, 4.2)');
		}

		$validated = $inputString->consume(1);
		while ($inputString->hasMore && $inputString->peek(1) !== '/') {
			$char = $inputString->consume(1);
			if (!HttpChar::isAlpha($char)
				&& !HttpChar::isDigit($char)
				&& $char !== '!'
				&& $char !== '#'
				&& $char !== '$'
				&& $char !== '&'
				&& $char !== '-'
				&& $char !== '^'
				&& $char !== '_'
				&& $char !== '.'
				&& $char !== '+'
			) {
				return new ParseException('Characters in a media type name must be ALPHA or DIGIT or "!#$&-^_.+" (RFC 6838, 4.2)');
			}
			$validated .= $char;
		}
		$validated = \strtolower($validated);

		$facetEnd = \strpos($validated, '.');
		$syntaxStart = \strrpos($validated, '+');
		if ($facetEnd !== false && $syntaxStart !== false && $facetEnd > $syntaxStart) {
			return new ParseException('Ambiguous situation, facet and syntax suffix overlap (not clarified by RFC 6838)');
		}

		$name = \substr(
			string: $validated,
			offset: $facetEnd !== false ? ($facetEnd + 1) : 0,
			length: $syntaxStart !== false ? $syntaxStart - \strlen($validated) : null
		);
		if ($name === '') {
			return new ParseException('Media type name is empty or only consists of a facet and syntax suffix');
		}

		$facet = $facetEnd !== false ? \substr($validated, 0, $facetEnd) : null;
		$syntax = $syntaxStart !== false ? \substr($validated, $syntaxStart + 1) : null;
		if ($syntax === '') {
			return new ParseException('Structured syntax suffix was empty');
		}

		return new ReadOnlyMediaTypeName(
			name: $name,
			facet: $facet, // @phpstan-ignore argument.type (facet cannot be empty string since first char must always be alpha or digit)
			syntax: $syntax,
		);
	}

}
