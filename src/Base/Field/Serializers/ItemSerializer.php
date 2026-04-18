<?php

namespace Slendium\Http\Base\Field\Serializers;

use DateTimeInterface;
use BcMath\Number;

use Slendium\Http\Base\SerializeException;
use Slendium\Http\Base\Field\ReadOnlyParameterized;
use Slendium\Http\Field\Item;
use Slendium\Http\Field\Parameterized;

/**
 * @internal
 * @see https://datatracker.ietf.org/doc/html/rfc9651#section-4.1.3
 * @see https://datatracker.ietf.org/doc/html/rfc9651#section-4.1.3.1
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class ItemSerializer {

	/** @see https://datatracker.ietf.org/doc/html/rfc9651#section-4.1.3 */
	public static function serialize9651(mixed $input): SerializeException|string {
		if (!($input instanceof Parameterized)) {
			$input = ReadOnlyParameterized::withoutParameters($input);
		}

		// 1. Let output be an empty string.
		$output = '';

		// 2. Append the result of running Serializing a Bare Item (Section 4.1.3.1) with bare_item to output.
		$bare_item = self::serializeBareItem9651($input->data);
		if (!\is_string($bare_item)) {
			return $bare_item; // Exception
		}
		$output .= $bare_item;

		// 3. Append the result of running Serializing Parameters (Section 4.1.1.2) with item_parameters to output.
		$parameters = ParametersSerializer::serialize9651($input->parameters);
		if (!\is_string($parameters)) {
			return $parameters; // Exception
		}
		$output .= $parameters;

		// 4. Return output.
		return $output;
	}

	/** @see https://datatracker.ietf.org/doc/html/rfc9651#section-4.1.3.1 */
	public static function serializeBareItem9651(mixed $input): SerializeException|string {
		// 1. If input_item is an Integer, return the result of running Serializing an Integer (Section 4.1.4) with input_item.
		if (\is_int($input) || $input instanceof Item\Integer) {
			return IntegerSerializer::serialize9651($input);
		}

		// 2. If input_item is a Decimal, return the result of running Serializing a Decimal (Section 4.1.5) with input_item.
		if (\is_float($input) || $input instanceof Number || $input instanceof Item\Decimal) {
			return DecimalSerializer::serialize9651($input);
		}

		// 3. If input_item is a String, return the result of running Serializing a String (Section 4.1.6) with input_item.
		if ($input instanceof Item\String_) {
			return StringSerializer::serialize9651($input);
		}

		// 4. If input_item is a Token, return the result of running Serializing a Token (Section 4.1.7) with input_item.
		if ($input instanceof Item\Token) {
			return TokenSerializer::serialize9651($input);
		}

		// 5. If input_item is a Byte Sequence, return the result of running Serializing a Byte Sequence (Section 4.1.8) with input_item.
		if ($input instanceof Item\ByteSequence) {
			return ByteSequenceSerializer::serialize9651($input);
		}

		// 6. If input_item is a Boolean, return the result of running Serializing a Boolean (Section 4.1.9) with input_item.
		if (\is_bool($input) || $input instanceof Item\Boolean) {
			return BooleanSerializer::serialize9651($input);
		}

		// 7. If input_item is a Date, return the result of running Serializing a Date (Section 4.1.10) with input_item.
		if ($input instanceof DateTimeInterface || $input instanceof Item\Date) {
			return DateSerializer::serialize9651($input);
		}

		// 8. If input_item is a Display String, return the result of running Serializing a Display String (Section 4.1.11) with input_item.
		if ($input instanceof Item\DisplayString) {
			return DisplayStringSerializer::serialize9651($input);
		}

		// 9. Otherwise, fail serialization.
		return new SerializeException('Unserializable item (RFC 9651, 4.1.3.1, 9)');
	}

}
