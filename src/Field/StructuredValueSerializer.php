<?php

namespace Slendium\Http\Field;

use ArrayAccess;
use Countable;
use Exception;
use Traversable;

/**
 * Serializes PHP values into ASCII strings that represent structured values as defined by RFC 9651.
 *
 * Different serializers may have different levels of correctness in exchange for performance or
 * convenience. Some may return empty strings on errors. Some may truncate the results if the input
 * is too large while others may return an error if the input is too large.
 *
 * Note that due to PHP's handling of floating point numbers it may be necessary to use high-precision
 * {@see \BcMath\Number}'s.
 *
 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.1
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface StructuredValueSerializer {

	/**
	 * Serializes a list.
	 * @since 1.0
	 * @param (Countable&Traversable<mixed>)|array<mixed> $input
	 */
	public function serializeList((Countable&Traversable)|array $input): Exception|string;

	/**
	 * @since 1.0
	 * @param (ArrayAccess<mixed,mixed>&Countable&Traversable<mixed,mixed>)|array<mixed> $input
	 */
	public function serializeDictionary((ArrayAccess&Countable&Traversable)|array $input): Exception|string;

	/**
	 * Serializes a single item.
	 *
	 * Serializers MUST support all of the variants defined in `Slendium\Http\Field\Item`. They MUST
	 * also support values wrapped as a `Parameterized`.
	 *
	 * Serializers may choose how to interpret a PHP string (token, string, display string or binary
	 * sequence). The caller of the method can explicitly pass one of the predefined types if they
	 * need to be sure of the output. Serializers may also just reject plain strings.
	 *
	 * @since 1.0
	 */
	public function serializeItem(mixed $input): Exception|string;

}
