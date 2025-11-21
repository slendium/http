<?php

namespace Slendium\Http\Field\Item;

use Override;

use Slendium\Http\Field\Item as BaseItem;

/**
 * Strings are zero or more printable ASCII [RFC0020] characters (i.e., the range %x20 to %x7E). Note
 * that this excludes tabs, newlines, carriage returns, etc.
 *
 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-3.3.3
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class String_ extends BaseItem {

	/** @override */
	public readonly string $value; // @phpstan-ignore property.uninitializedReadonly

	#[Override]
	protected function onConstruct(mixed ...$args): void {
		$this->value = $args[0]; // @phpstan-ignore assign.propertyType, property.readOnlyAssignNotInConstructor
	}

}
