<?php

namespace Slendium\Http\Base\Field\Serializers;

use Slendium\Http\Base\Field\ReadOnlyParameterized;
use Slendium\Http\Base\SerializeException;
use Slendium\Http\Field\Item;
use Slendium\Http\Field\Parameterized;

/**
 * @internal
 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.1.2
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class DictionarySerializer {

	/** @param iterable<mixed> $input */
	public static function serialize9651(iterable $input): SerializeException|string {
		// 1. Let output be an empty string.
		$output = [ ];

		// 2. For each member_key with a value of (member_value, parameters) in input_dictionary:
		foreach ($input as $member_key => $member) {
			$member = $member instanceof Parameterized
				? $member
				: ReadOnlyParameterized::withoutParameters($member);
			$serial_member = '';
			// 2.1. Append the result of running Serializing a Key (Section 4.1.1.3) with member's member_key to output.
			$serial_key = KeySerializer::serialize9651($member_key);
			if (!\is_string($serial_key)) {
				return $serial_key; // Exception
			}
			$serial_member .= $serial_key;
			// 2.2. If member_value is Boolean true:
			if (self::isBooleanTrue($member->data)) {
				// 2.2.1. Append the result of running Serializing Parameters (Section 4.1.1.2) with parameters to output.
				$serial_params = ParametersSerializer::serialize9651($member->parameters);
				if (!\is_string($serial_params)) {
					return $serial_params; // Exception
				}
				$serial_member .= $serial_params;
			// 2.3. Otherwise:
			} else {
				// 2.3.1. Append "=" to output.
				$serial_member .= '=';
				// 2.3.2. If member_value is an array, append the result of running Serializing an Inner
				//        List (Section 4.1.1.1) with (member_value, parameters) to output.
				// 2.3.3. Otherwise, append the result of running Serializing an Item (Section 4.1.3)
				//        with (member_value, parameters) to output.
				$serial_value = \is_iterable($member->data)
					? ListSerializer::serializeInnerList9651($member)
					: ItemSerializer::serialize9651($member);
				if (!\is_string($serial_value)) {
					return $serial_value; // Exception
				}
				$serial_member .= $serial_value;
			}
			$output[] = $serial_member;
		}

		// 2.4. If more members remain in input_dictionary:
		// 2.4.1. Append "," to output.
		// 2.4.2. Append a single SP to output.
		// 3. Return output.
		return \implode(', ', $output);
	}

	private static function isBooleanTrue(mixed $value): bool {
		return $value === true || ($value instanceof Item\Boolean && $value->value === true);
	}

}
