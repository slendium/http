<?php

namespace Slendium\Http\Base;

use Override;

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

		/** @var lowercase-string&non-empty-string */
		#[Override]
		public readonly string $name,

		#[Override]
		public readonly string $value,

	) { }

	#[Override]
	public final function __toString(): string {
		return "{$this->name}: {$this->value}";
	}

}
