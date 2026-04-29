<?php

namespace Slendium\Http;

use Stringable;

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
	 * Implementations may provide an `ArrayAccess|array` value that maps field names to an iterable
	 * of all fields with that name for improved lookup performance.
	 *
	 * See {@see Message\Fields} to extract information from this property and {@see \Slendium\Http\Base\FieldSet}
	 * for a default implementation.
	 *
	 * @since 1.0
	 * @var iterable<Field>
	 */
	public iterable $headers { get; }

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
	 * @var ?iterable<Stringable|string>
	 */
	public ?iterable $body { get; }

	/**
	 * The message trailer fields.
	 *
	 * Implementations may provide an `ArrayAccess|array` value that maps field names to an iterable
	 * of all fields with that name for improved lookup performance.
	 *
	 * See {@see Message\Fields} to extract information from this property and {@see \Slendium\Http\Base\FieldSet}
	 * for a default implementation.
	 *
	 * @since 1.0
	 * @var iterable<Field>
	 */
	public iterable $trailers { get; }

}
