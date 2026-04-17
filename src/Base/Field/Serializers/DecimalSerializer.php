<?php

namespace Slendium\Http\Base\Field\Serializers;

use RoundingMode;
use BcMath\Number;

use Slendium\Http\Base\SerializeException;
use Slendium\Http\Field\Item;

/**
 * @internal
 * @see https://datatracker.ietf.org/doc/html/rfc9651#section-4.1.5
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class DecimalSerializer {

	public static function serialize9651(Item\Decimal|Number|float $input): SerializeException|string {
		if ($input instanceof Item\Decimal) {
			$input = $input->value;
		}

		// BcMath\Number does not support scientific notation, which PHP uses for floats beyond a certain size
		if (\is_float($input)) {
			$input = new Number(\sprintf('%0.9f', $input));
		}

		// 1. If input_decimal is not a decimal number, fail serialization.
		// 2. If input_decimal has more than three significant digits to the right of the decimal
		//    point, round it to three decimal places, rounding the final digit to the nearest value,
		//    or to the even value if it is equidistant.
		$input = $input->round(3, RoundingMode::HalfEven);

		// 3. If input_decimal has more than 12 significant digits to the left of the decimal point after rounding, fail serialization.
		if ($input->compare('999999999999.999') > 0 || $input->compare('-999999999999.999') < 0) {
			return new SerializeException('Decimal can\'t have more than 12 digits left of the decimal point (RFC 9651, 4.1.5-3)');
		}

		// 4. Let output be an empty string.
		// 5. If input_decimal is less than (but not equal to) 0, append "-" to output.
		// 6. Append input_decimal's integer component represented in base 10 (using only decimal digits) to output; if it is zero, append "0".
		// 7. Append "." to output.
		// 8. If input_decimal's fractional component is zero, append "0" to output.
		// 9. Otherwise, append the significant digits of input_decimal's fractional component represented
		//    in base 10 (using only decimal digits) to output.

		// BcMath Number basically does all of the above, except it enforces precision
		// So zeroes need to be trimmed and one needs to be added back if none are left
		$output = \rtrim($input->value, '0');
		if ($output[\strlen($output) - 1] === '.') {
			$output .= '0';
		}

		// 10. Return output.
		return $output;
	}

}
