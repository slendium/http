<?php

namespace Slendium\Http\Base\Content;

use Override;

use Slendium\Http\Content\MediaType as IMediaType;
use Slendium\Http\Base\ParseException;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
class MediaType implements IMediaType {

	/**
	 * Converts a string into a media type.
	 *
	 * RFC 6838 does not specify what to do if the facet and the structured syntax suffix parts of a name
	 * overlap like so: `image/a+b.c`. Technically the facet consists of all characters before the first
	 * "." and the syntax suffix is all characters after the last "+", meaning that in the example the facet
	 * would be "a+b" and the syntax suffix would be "b.c". An exception is thrown in this case.
	 *
	 * @since 1.0
	 * @throws ParseException When parsing failed
	 */
	public static function fromString(string $input): self {
		return MediaTypeParser::parseString($input);
	}

	/**
	 * @since 1.0
	 * @param lowercase-string&non-empty-string $main
	 * @param lowercase-string&non-empty-string $subtype
	 */
	public static function fromNames(string $main, string $subtype): self {
		return new self(new MediaTypeName($main), new MediaTypeName($subtype));
	}

	/** @since 1.0 */
	public function __construct(

		#[Override]
		public readonly MediaTypeName $major,

		#[Override]
		public readonly MediaTypeName $minor,

	) { }

	public function __toString(): string {
		return "{$this->major}/{$this->minor}";
	}

}
