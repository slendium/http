<?php

namespace Slendium\Http;

use Stringable;

/**
 * A generic HTTP field, more commonly known as a header.
 *
 * Has been named a "field" instead of a "header" to be more in line with [RFC 9110](https://www.rfc-editor.org/rfc/rfc9110.html).
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface Field extends Stringable {

	/**
	 * The field name, such as `content-type` or `:status`.
	 *
	 * Field names are defined as a `token` in the [HTTP 1.1 spec](https://datatracker.ietf.org/doc/html/rfc7230#section-3.2)
	 * which in turn is defined as [`1*tchar`](https://datatracker.ietf.org/doc/html/rfc7230#section-3.2.6),
	 * meaning they must be at least one character long.
	 *
	 * Since names are case-insensitive they are defined as `lowercase-string` to force consistent use.
	 *
	 * @since 1.0
	 * @var lowercase-string&non-empty-string
	 */
	public string $name { get; }

	/** @since 1.0 */
	public string $value { get; }

}
