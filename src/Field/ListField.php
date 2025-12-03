<?php

namespace Slendium\Http\Field;

use Countable;
use Traversable;

use Slendium\Http\Field;

/**
 * Interface that can be used by implementations to indicate the field value should be a list.
 *
 * @since 1.0
 * @phpstan-import-type BaseList from StructuredValueParser
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface ListField extends Field {

	/**
	 * Parses the field value into a list.
	 * @since 1.0
	 * @return BaseList
	 */
	public function toList(): Countable&Traversable;

}
