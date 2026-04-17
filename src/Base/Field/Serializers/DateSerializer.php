<?php

namespace Slendium\Http\Base\Field\Serializers;

use DateTimeInterface;

use Slendium\Http\Field\Item;

/**
 * @internal
 * @see https://datatracker.ietf.org/doc/html/rfc9651#section-4.1.10
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class DateSerializer {

	public static function serialize9651(Item\Date|DateTimeInterface $input): string {
		if ($input instanceof Item\Date) {
			$input = $input->value;
		}

		// 1. Let output be "@".
		// 2. Append to output the result of running Serializing an Integer with input_date (Section 4.1.4).
		// 3. Return output.
		return '@'.IntegerSerializer::serialize9651($input->getTimestamp());
	}

}
