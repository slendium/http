<?php

namespace Slendium\Http\Content;

use Exception;
use Override;

use Slendium\Http\Content\MediaType;
use Slendium\Http\Content\MediaTypeName;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025-2026
 */
class ReadOnlyMediaType implements MediaType {

	/**
	 * Converts a string into a media type.
	 *
	 * RFC 6838 does not specify what to do if the facet and the structured syntax suffix parts of a name
	 * overlap like so: `image/a+b.c`. Technically the facet consists of all characters before the first
	 * "." and the syntax suffix is all characters after the last "+", meaning that in the example the facet
	 * would be "a+b" and the syntax suffix would be "b.c". An exception is thrown in this case.
	 *
	 * @since 1.0
	 */
	public static function fromString(string $input): Exception|MediaType {
		return MediaTypeParser::parseString($input);
	}

	/**
	 * @since 1.0
	 * @param lowercase-string&non-empty-string $main
	 * @param lowercase-string&non-empty-string $subtype
	 */
	public static function fromNames(string $main, string $subtype): self {
		return new self(new ReadOnlyMediaTypeName($main), new ReadOnlyMediaTypeName($subtype));
	}

	/** @since 1.0 */
	public function __construct(

		#[Override]
		public readonly MediaTypeName $major,

		#[Override]
		public readonly MediaTypeName $minor,

	) { }

	/** @return non-empty-string */
	public function __toString(): string {
		return "{$this->major}/{$this->minor}";
	}

}
