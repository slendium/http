<?php

namespace Slendium\Http;

use Override;

use Slendium\Http\Field;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
readonly class ReadOnlyField implements Field {

	/** @since 1.0 */
	public function __construct(

		/** @var lowercase-string&non-empty-string */
		#[Override]
		public string $name,

		#[Override]
		public string $value,

	) { }

	#[Override]
	public final function __toString(): string {
		return "{$this->name}: {$this->value}";
	}

}
