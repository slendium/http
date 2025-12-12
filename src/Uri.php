<?php

namespace Slendium\Http;

use ArrayAccess;
use Countable;
use Stringable;
use Traversable;

/**
 * A URI pointing to an online resource.
 *
 * Property names were chosen to match the PHP-native `Rfc3986\Uri` function for consistency. For
 * security reasons the `__toString()` implementation MUST return the canonical RFC 3986 representation
 * and it is therefore recommended to use the native type as a backing value.
 *
 * Since sending passwords in URL's is deprecated it was not included as its own property. You can
 * append the `$userInfo` property with a `:` to indicate an empty password (this is still allowed).
 *
 * The PHP 8.5+ native `Uri` type did not fit all use cases of this library, so this separate
 * interface was kept with the release of PHP 8.5. Most notably it does not have a way to access or
 * edit individual query parameters (since these are not well defined by standards). Additionally
 * it keeps the deprecated "password" option around (for valid reasons).
 *
 * @see https://www.rfc-editor.org/rfc/rfc3986.html
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface Uri extends Stringable {

	/**
	 * The scheme, such as `http`.
	 *
	 * Can be `NULL` to indicate absense of any scheme, can be an empty string to indicate a scheme-
	 * relative url such as `//example.com`.
	 *
	 * @since 1.0
	 */
	public ?string $scheme { get; }

	/** @since 1.0 */
	public ?string $userInfo { get; }

	/**
	 * @since 1.0
	 * @var ?non-empty-string
	 */
	public ?string $host { get; }

	/**
	 * @since 1.0
	 * @var ?int<0,65535>
	 */
	public ?int $port { get; }

	/**
	 * The path, starting with `/`.
	 *
	 * @since 1.0
	 * @var non-empty-string
	 */
	public ?string $path { get; }

	/**
	 * The query data encoded in the URL.
	 *
	 * A count of 0 indicates a single `?` while `NULL` indicates no query was present.
	 *
	 * Values are allowed to be lists/arrays to account for query strings such as `?map[a]=3&map[b]=2`.
	 *
	 * The `__toString()` implementation MUST return the canonical representation according to RFC 3986.
	 *
	 * Reminder that query keys are case-sensitive according to [RFC 3986](https://www.rfc-editor.org/rfc/rfc3986#section-6.2.2.1).
	 *
	 * @since 1.0
	 * @var (ArrayAccess<non-empty-string,array<mixed>|string|null>&Countable&Traversable<non-empty-string,array<mixed>|string>)|null
	 */
	public (ArrayAccess&Countable&Traversable)|null $query { get; }

	/**
	 * The hash/fragment part of the URL, without the `#`.
	 *
	 * Can be an empty string to indicate the fragment was empty, i.e. a single `#`
	 *
	 * @since 1.0
	 */
	public ?string $fragment { get; }

}
