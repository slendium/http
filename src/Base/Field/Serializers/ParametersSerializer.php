<?php

namespace Slendium\Http\Base\Field\Serializers;

use DateTimeInterface;
use BcMath\Number;

use Slendium\Http\Base\SerializeException;
use Slendium\Http\Field\Item;

/**
 * @internal
 * @see https://datatracker.ietf.org/doc/html/rfc9651#section-4.1.1.2
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class ParametersSerializer {

	/** @param iterable<string,Item|Number|DateTimeInterface|string|float|int|bool> $input */
	public static function serialize9651(iterable $input): SerializeException|string {
		// 1. Let output be an empty string.
		$output = '';

		// 2. For each param_key with a value of param_value in input_parameters:
		foreach ($input as $param_key => $param_value) {
			// 2.1. Append ";" to output.
			$output .= ';';
			// 2.2. Append the result of running Serializing a Key (Section 4.1.1.3) with param_key to output.
			$serial_key = KeySerializer::serialize9651($param_key);
			if (!\is_string($serial_key)) {
				return $serial_key; // Exception
			}
			$output .= $serial_key;
			// 2.3. If param_value is not Boolean true:
			if (!self::isBooleanTrue($param_value)) {
				// 2.3.1. Append "=" to output.
				// 2.3.2. Append the result of running Serializing a bare Item (Section 4.1.3.1) with param_value to output.
				$serial_item = ItemSerializer::serializeBareItem9651($param_value);
				if (!\is_string($serial_item)) {
					return $serial_item; // Exception
				}
				$output .= "=$serial_item";
			}
		}

		// 3. Return output.
		return $output;
	}

	private static function isBooleanTrue(mixed $value): bool {
		return $value === true || ($value instanceof Item\Boolean && $value->value === true);
	}

}
