<?php

namespace Slendium\Http\Content;

use ArrayAccess,
	Countable,
	Traversable;

/**
 * Indicates that a value received over HTTP can be interpreted as a (nested) key-value map.
 *
 * For example, when expecting form data or JSON/CBOR input, a consumer of the message could simply
 * check if the message body is `Structured` instead of manually checking the content type and
 * writing/invoking their own parsers. This same logic can be applied to headers, such as the cookie
 * header or any of the JSON-based or dictionary-based headers.
 *
 * @since 1.0
 * @template TKey = string
 * @template TValue = mixed
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface Structured {

	/**
	 * The root object that provides access to the parsed data.
	 * @since 1.0
	 * @var ArrayAccess<TKey,?TValue>&Countable&Traversable<TKey,TValue>
	 */
	public ArrayAccess&Countable&Traversable $root { get; }

}
