<?php

namespace Slendium\Http\Field\Item;

use Override;

use Slendium\Http\Field\Item as BaseItem;

/**
 * When serialized in a textual HTTP field, a Byte Sequence is delimited with colons and encoded
 * using base64 (RFC 4648, Section 4).
 *
 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-3.3.5
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class ByteSequence extends BaseItem {

	/** @overide */
	public readonly string $value; // @phpstan-ignore property.uninitializedReadonly

	#[Override]
	protected function onConstruct(mixed ...$args): void {
		$this->value = $args[0]; // @phpstan-ignore assign.propertyType, property.readOnlyAssignNotInConstructor
	}

}
