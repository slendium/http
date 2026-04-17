<?php

namespace Slendium\Http\Base\Field;

use ArrayAccess;
use Countable;
use DateTimeInterface;
use Exception;
use Override;
use Traversable;
use BcMath\Number;

use Slendium\Http\Base\SerializeException;
use Slendium\Http\Field\Item;
use Slendium\Http\Field\Parameterized as IParameterized;
use Slendium\Http\Field\StructuredValueSerializer;

/**
 *
 *
 * Serializes {@see Number}'s to decimals.
 *
 * @since 1.0
 * @see https://datatracker.ietf.org/doc/html/rfc9651#section-4.1
 * @author C. Fahner
 * @copyright Slendium 2025
 */
class Rfc9651Serializer implements StructuredValueSerializer {

	/** @param (Countable&Traversable<mixed>)|array<mixed> $input */
	#[Override]
	public function serializeList((Countable&Traversable)|array $input): Exception|string {
		// Section 4.1-1: If the structure is a Dictionary or List and its value is empty (i.e., it
		// has no members), do not serialize the field at all (i.e., omit both the field-name and field-value).
		// - thus this method should never be called with an empty input
		return \count($input) > 0
			? Serializers\ListSerializer::serialize9651($input)
			: new SerializeException('List structures are not allowed to be empty (RFC 9651 4.1, 1)');
	}

	/** @param (ArrayAccess<mixed,mixed>&Countable&Traversable<mixed,mixed>)|array<mixed> $input */
	#[Override]
	public function serializeDictionary((ArrayAccess&Countable&Traversable)|array $input): Exception|string {
		// Section 4.1-1: If the structure is a Dictionary or List and its value is empty (i.e., it
		// has no members), do not serialize the field at all (i.e., omit both the field-name and field-value).
		// - thus this method should never be called with an empty input
		return \count($input) > 0
			? Serializers\DictionarySerializer::serialize9651($input)
			: new SerializeException('Dictionary structures are not allowed to be empty (RFC 9651 4.1, 1)');
	}

	#[Override]
	public function serializeItem(mixed $input): Exception|string {
		if (\is_string($input)) {
			return new SerializeException('Ambiguous input, PHP strings have no clear structured value analogue');
		}

		// https://datatracker.ietf.org/doc/html/rfc9651#section-4.1.3
		return Serializers\ItemSerializer::serialize9651($input);
	}

}
