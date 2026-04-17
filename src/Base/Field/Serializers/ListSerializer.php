<?php

namespace Slendium\Http\Base\Field\Serializers;

use Slendium\Http\Base\Field\Parameterized;
use Slendium\Http\Base\Field\Parameters;
use Slendium\Http\Base\SerializeException;
use Slendium\Http\Field\Parameterized as IParameterized;

/**
 * @internal
 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.1.1
 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-4.1.1.1
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class ListSerializer {

	/** @param iterable<mixed> $input */
	public static function serialize9651(iterable $input): SerializeException|string {
		// 1. Let output be an empty string.
		// - we build a list<string> instead and later implode() it with the proper separator
		$output = [ ];

		// 2. For each (member_value, parameters) of input_list:
		foreach ($input as $member) {
			if (!($member instanceof IParameterized)) {
				$member = new Parameterized($member, new Parameters([ ]));
			}
			// 2.1. If member_value is an array, append the result of running Serializing an Inner List
			//      (Section 4.1.1.1) with (member_value, parameters) to output.
			// 2.2. Otherwise, append the result of running Serializing an Item (Section 4.1.3) with
			//      (member_value, parameters) to output.
			$serialized = \is_iterable($member->data)
				? self::serializeInnerList9651($member)
				: ItemSerializer::serialize9651($member);
			if (!\is_string($serialized)) {
				return $serialized; // Exception
			}
			$output[] = $serialized;
		}

		// 2.3. If more member_values remain in input_list:
		// 2.3.1. Append "," to output.
		// 2.3.2. Append a single SP to output.
		// 3. Return output.
		return \implode(', ', $output);
	}

	/** @param IParameterized<iterable<mixed>>|iterable<mixed> $input */
	public static function serializeInnerList9651(IParameterized|iterable $input): SerializeException|string {
		if (\is_iterable($input)) {
			$input = Parameterized::withoutParameters($input);
		}
		// The rule in Section 4.1 point 1 about empty lists does not mention inner lists, so we ignore the empty case
		// - we build a list<string> again and implode() and wrap it in "(" and ")" later
		$output = [ ];

		// 2. For each (member_value, parameters) of inner_list:
		foreach ($input->data as $member) {
			if (!($member instanceof IParameterized)) {
				$member = new Parameterized($member, new Parameters([ ]));
			}
			// 2.1. Append the result of running Serializing an Item (Section 4.1.3) with (member_value, parameters) to output.
			$serialized = ItemSerializer::serialize9651($member);
			if (!\is_string($serialized)) {
				return $serialized; // Exception
			}
			$output[] = $serialized;
		}

		// 1. Let output be the string "(".
		// 2.2. If more values remain in inner_list, append a single SP to output.
		// 3. Append ")" to output.
		$output = '('.\implode(' ', $output).')';

		// 4. Append the result of running Serializing Parameters (Section 4.1.1.2) with list_parameters to output.
		$serialized_parameters = ParametersSerializer::serialize9651($input->parameters);
		if (!\is_string($serialized_parameters)) {
			return $serialized_parameters; // Exception
		}
		$output .= $serialized_parameters;

		// 5. Return output.
		return $output;
	}

}
