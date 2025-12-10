<?php

namespace Slendium\Http\Field\Item;

use Override;

use Slendium\Http\Field\Item as BaseItem;

/**
 * Integers have a range of -999,999,999,999,999 to 999,999,999,999,999 inclusive (i.e., up to fifteen
 * digits, signed), for IEEE 754 compatibility.
 *
 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-3.3.1
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class Integer extends BaseItem {

	#[Override]
	public readonly int $value; // @phpstan-ignore property.uninitializedReadonly

	#[Override]
	protected function onConstruct(mixed ...$args): void {
		$this->value = $args[0]; // @phpstan-ignore assign.propertyType, property.readOnlyAssignNotInConstructor
	}

}
