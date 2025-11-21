<?php

namespace Slendium\Http\Field\Item;

use Override;

use Slendium\Http\Field\Item as BaseItem;

/**
 * Tokens are short textual words that begin with an alphabetic character or "*", followed by zero to
 * many token characters, which are the same as those allowed by the "token" ABNF rule defined in
 * [RFC 9110](https://www.rfc-editor.org/rfc/rfc9110.html) plus the ":" and "/" characters.
 *
 * @see https://www.rfc-editor.org/rfc/rfc9651.html#section-3.3.4
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class Token extends BaseItem {

	/** @override */
	public readonly string $value; // @phpstan-ignore property.uninitializedReadonly

	#[Override]
	protected function onConstruct(mixed ...$args): void {
		$this->value = $args[0]; // @phpstan-ignore assign.propertyType, property.readOnlyAssignNotInConstructor
	}

}
