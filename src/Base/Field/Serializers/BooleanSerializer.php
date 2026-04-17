<?php

namespace Slendium\Http\Base\Field\Serializers;

use Slendium\Http\Field\Item;

/**
 * @internal
 * @see https://datatracker.ietf.org/doc/html/rfc9651#section-4.1.9
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class BooleanSerializer {

	public static function serialize9651(Item\Boolean|bool $input): string {
		if ($input instanceof Item\Boolean) {
			$input = $input->value;
		}

		// 1. If input_boolean is not a boolean, fail serialization.
		// only booleans can reach this point

		// 2. Let output be an empty string.
		// 3. Append "?" to output.
		// 4. If input_boolean is true, append "1" to output.
		// 5. If input_boolean is false, append "0" to output.
		// 6. Return output.
		return $input
			? '?1'
			: '?0';
	}

}
