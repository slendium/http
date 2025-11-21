<?php

namespace Slendium\Http\Field\Item;

use Override;

use Slendium\Http\Field\Item as BaseItem;

/**
 * Decimals are numbers with an integer and a fractional component. The integer component has at most
 * 12 digits; the fractional component has at most three digits.
 *
 * https://www.rfc-editor.org/rfc/rfc9651.html#section-3.3.2
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class Decimal extends BaseItem {

	/** @override */
	public readonly float $value; // @phpstan-ignore property.uninitializedReadonly

	#[Override]
	protected function onConstruct(mixed ...$args): void {
		$this->value = $args[0]; // @phpstan-ignore assign.propertyType, property.readOnlyAssignNotInConstructor
	}

}
