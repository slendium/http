<?php

namespace Slendium\Http\Content;

use Stringable;

/**
 * A media type, a.k.a. MIME-type or content type.
 *
 * The RFC specifies both the main type and the subtype as a `restricted-name`. Therefor, this interface
 * also defines the main type ("major") and subtype ("minor") as the same PHP type, despite the main
 * type in practice never having a facet or syntax suffix.
 *
 * The property names "major" (main type) and "minor" (subtype) were chosen to avoid awkward referenced
 * names. Consider the property `public MediaType $type`. Accessing the main or subtype through the
 * `$type` property would then look something like `$obj->type->main` and `$obj->type->subtype`.
 *
 * @see https://www.rfc-editor.org/rfc/rfc6838
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface MediaType extends Stringable {

	/**
	 * The main type, such as `"image"` or `"text"`.
	 * @since 1.0
	 */
	public MediaTypeName $major { get; }

	/**
	 * The subtype, such as `"png"`, `"plain"` or `"x.example+xml"`.
	 * @since 1.0
	 */
	public MediaTypeName $minor { get; }

}
