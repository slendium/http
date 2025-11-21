<?php

namespace Slendium\Http\Field\Item;

use DateTimeInterface,
	Override;

use Slendium\Http\Field\Item as BaseItem;

/**
 * Dates have a data model that is similar to Integers, representing a (possibly negative) delta in
 * seconds from 1970-01-01T00:00:00Z, excluding leap seconds. Accordingly, their serialization in textual
 * HTTP fields is similar to that of Integers, distinguished from them with a leading "@".
 *
 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-3.3.7
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class Date extends BaseItem {

	/** @override */
	public readonly DateTimeInterface $value; // @phpstan-ignore property.uninitializedReadonly

	#[Override]
	protected function onConstruct(mixed ...$args): void {
		$this->value = $args[0]; // @phpstan-ignore assign.propertyType, property.readOnlyAssignNotInConstructor
	}

}
