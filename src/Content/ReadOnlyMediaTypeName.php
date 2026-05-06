<?php

namespace Slendium\Http\Content;

use Override;

use Slendium\Http\Content\MediaTypeName;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025-2026
 */
class ReadOnlyMediaTypeName implements MediaTypeName {

	/** @since 1.0 */
	public function __construct(

		/** @var lowercase-string&non-empty-string */
		#[Override]
		public readonly string $name,

		/** @var (lowercase-string&non-empty-string)|null */
		#[Override]
		public readonly ?string $facet = null,

		/** @var (lowercase-string&non-empty-string)|null */
		#[Override]
		public readonly ?string $syntax = null,

	) { }

	/** @return non-empty-string */
	public function __toString(): string {
		$result = '';
		if ($this->facet !== null) {
			$result .= "{$this->facet}.";
		}
		$result .= $this->name;
		if ($this->syntax !== null) {
			$result .= "+{$this->syntax}";
		}
		return $result;
	}

}
