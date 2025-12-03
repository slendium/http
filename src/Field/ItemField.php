<?php

namespace Slendium\Http\Field;

use Slendium\Http\Field;
use Slendium\Http\Field\Parameterized;
use Slendium\Http\Field\Item;

/**
 * Interface that can be used by implementations to indicate the field value should be an item.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface ItemField extends Field {

	/**
	 * Parses the field value into an item.
	 * @since 1.0
	 * @return Parameterized<Item>
	 */
	public function toItem(): Parameterized;

}
