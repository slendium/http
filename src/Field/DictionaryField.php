<?php

namespace Slendium\Http\Field;

use ArrayAccess;
use Countable;
use Traversable;

use Slendium\Http\Field;

/**
 * Interface that can be used by implementations to indicate the field value should be a dictionary.
 *
 * @since 1.0
 * @phpstan-import-type Dictionary from StructuredValueParser
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface DictionaryField extends Field {

	/**
	 * Parses the field value into a dictionary.
	 * @since 1.0
	 * @return Dictionary
	 */
	public function toDictionary(): ArrayAccess&Countable&Traversable;

}
