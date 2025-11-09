<?php

namespace Slendium\Http;

use ArrayAccess,
	Countable,
	Stringable,
	Traversable;

/**
 * A URL pointing to an HTTP resource.
 *
 * Property names were chosen to match the PHP-native `parse_url()` function for consistency.
 *
 * Since sending passwords in URL's is deprecated it was not included as a its own property. You can
 * append the `$user` property with a `:` to indicate an empty password (this is still allowed).
 *
 * @see https://www.rfc-editor.org/rfc/rfc3986.html
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface Url extends Stringable {

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
	public ?string $user { get; }

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
