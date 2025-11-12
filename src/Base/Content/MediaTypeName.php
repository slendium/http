<?php

namespace Slendium\Http\Base\Content;

use Slendium\Http\Content\MediaTypeName as IMediaTypeName;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
class MediaTypeName implements IMediaTypeName {

	/** @since 1.0 */
	public function __construct(

		/**
		 * @override
		 * @var lowercase-string&non-empty-string
		 */
		public readonly string $name,

		/**
		 * @override
		 * @var (lowercase-string&non-empty-string)|null
		 */
		public readonly ?string $facet = null,

		/**
		 * @override
		 * @var (lowercase-string&non-empty-string)|null
		 */
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
