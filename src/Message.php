<?php

namespace Slendium\Http;

use ArrayAccess,
	Countable,
	Stringable,
	Traversable;

/**
 * A generic HTTP message.
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface Message {

	/**
	 * The message header fields.
	 *
	 * Mutable implementations MUST implement `ArrayAccess::offsetSet()` as follows:
	 * * If `$offset` is `NULL`, `$value` MUST be a `Field` to be appended to the list
	 * * If `$offset` is a `string` and `$value` is `NULL`, all headers by that name MUST be removed
	 *
	 * @since 1.0
	 * @var ArrayAccess<?(non-empty-string&lowercase-string),?Field>&Countable&Traversable<Field>
	 */
	public ArrayAccess&Countable&Traversable $headers { get; }

	/**
	 * The message body as a stream of parts, if any.
	 *
	 * If the body is structured data (such as form data, JSON or CBOR), implementations should return
	 * an object that implements the `Structured` interface. Uploaded files should be included in a
	 * `Structured` object associated with the names of the form inputs used to upload them. See the
	 * `UploadedFile` and `UploadFailure` types.
	 *
	 * Implementations should parse form data inputs with with names such as `example[key]` as an
	 * object associated with the name "example" that has a value under the key "key". Uploaded
	 * files should also be included in this structure in the same way.
	 *
	 * @since 1.0
	 * @var (Stringable&Traversable<Stringable|string>)|null
	 */
	public (Stringable&Traversable)|null $body { get; }

	/**
	 * The message trailer fields.
	 *
	 * Mutable implementations MUST implement `ArrayAccess::offsetSet()` as follows:
	 * * If `$offset` is `NULL`, `$value` MUST be a `Field` to be appended to the list
	 * * If `$offset` is a `string` and `$value` is `NULL`, all headers by that name MUST be removed
	 *
	 * @since 1.0
	 * @var ArrayAccess<?(non-empty-string&lowercase-string),?Field>&Countable&Traversable<Field>
	 */
	public ArrayAccess&Countable&Traversable $trailers { get; }

}
