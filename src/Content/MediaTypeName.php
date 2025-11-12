<?php

namespace Slendium\Http\Content;

use Stringable;

/**
 * The structure of a media type name.
 *
 * @see https://www.rfc-editor.org/rfc/rfc6838#section-4.2
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface MediaTypeName extends Stringable {

	/**
	 * The plain name, without facet or syntax suffix.
	 * @since 1.0
	 * @var non-empty-string
	 */
	public string $name { get; }

	/**
	 * The registration facet, if specified.
	 *
	 * The facet consists of all characters before the first ".".
	 *
	 * @since 1.0
	 * @var (lowercase-string&non-empty-string)|null
	 */
	public ?string $facet { get; }

	/**
	 * The structured syntax suffix, if specified.
	 *
	 * The structured syntax suffix consists of all characters after the last "+".
	 *
	 * @since 1.0
	 * @var (lowercase-string&non-empty-string)|null
	 */
	public ?string $syntax { get; }

}
