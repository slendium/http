<?php

namespace Slendium\Http\Field\Item;

use Override;

use Slendium\Http\Field\Item as BaseItem;

/**
 * When serialized in a textual HTTP field, a Boolean is indicated with a leading "?" character followed
 * by a "1" for a true value or "0" for false.
 *
 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-3.3.6
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class Boolean extends BaseItem {

	/** @override */
	public readonly bool $value; // @phpstan-ignore property.uninitializedReadonly

	#[Override]
	protected function onConstruct(mixed ...$args): void {
		$this->value = $args[0]; // @phpstan-ignore assign.propertyType, property.readOnlyAssignNotInConstructor
	}

}
