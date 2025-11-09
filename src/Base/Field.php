<?php

namespace Slendium\Http\Base;

use Slendium\Http\Field as IField;

/**
 * A basic field implementation.
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
class Field implements IField {

	/** @since 1.0 */
	public function __construct(

		/** @override */
		public readonly string $name,

		/** @override */
		public readonly string $value,

	) { }

	#[Override]
	public final function __toString(): string {
		return "{$this->name}: {$this->value}";
	}

}
