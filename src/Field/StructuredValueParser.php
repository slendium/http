<?php

namespace Slendium\Http\Field;

use ArrayAccess;
use Countable;
use Throwable;
use Traversable;

/**
 * Parses strings into the supported structured values defined by RFC 9651.
 *
 * Different parsers may have different levels of strictness or apply different error recovery methods.
 *
 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.2
 *
 * @since 1.0
 * @phpstan-type InnerList Countable&Traversable<int,Parameterized<Item>>
 * @phpstan-type BaseItem Parameterized<InnerList|Item>
 * @phpstan-type BaseList Countable&Traversable<int,BaseItem>
 * @phpstan-type Dictionary (ArrayAccess<non-empty-string&lowercase-string,?BaseItem>)&Countable&Traversable<non-empty-string&lowercase-string,BaseItem>
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface StructuredValueParser {

	/**
	 * @since 1.0
	 * @throws Throwable When parsing fails
	 * @return BaseList
	 */
	public function parseList(string $input): Countable&Traversable;

	/**
	 * @since 1.0
	 * @throws Throwable When parsing fails
	 * @return Dictionary
	 */
	public function parseDictionary(string $input): ArrayAccess&Countable&Traversable;

	/**
	 * @since 1.0
	 * @throws Throwable When parsing fails
	 * @return Parameterized<Item>
	 */
	public function parseItem(string $input): Parameterized;

}
