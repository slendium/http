<?php

namespace Slendium\Http\Field\Item;

use Override;

use Slendium\Http\Field\Item as BaseItem;

/**
 * Display Strings are similar to Strings, in that they consist of zero or more characters, but they
 * allow Unicode scalar values (i.e., all Unicode code points except for surrogates), unlike Strings.
 *
 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-3.3.8
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class DisplayString extends BaseItem {

	#[Override]
	public readonly string $value; // @phpstan-ignore property.uninitializedReadonly

	#[Override]
	protected function onConstruct(mixed ...$args): void {
		$this->value = $args[0]; // @phpstan-ignore assign.propertyType, property.readOnlyAssignNotInConstructor
	}

}
