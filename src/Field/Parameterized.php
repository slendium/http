<?php

namespace Slendium\Http\Field;

use ArrayAccess;
use Countable;
use Traversable;

/**
 * Wrapper for values that can be associated with HTTP parameters: `"; param1=value; param2=value; ..."`.
 *
 * @since 1.0
 * @template T
 * @author C. Fahner
 * @copyright Slendium 2025
 */
interface Parameterized {

	/**
	 * @since 1.0
	 * @var T
	 */
	public mixed $value { get; }

	/**
	 * The map of parameters associated with the value (usually an item or inner list).
	 *
	 * RFC 9651: "Implementations MUST provide access to Parameters both by index and by key. Specifications
	 * MAY use either means of accessing them."
	 *
	 * @since 1.0
	 * @var ArrayAccess<(non-empty-string&lowercase-string)|int<0,max>,?Item>&Countable&Traversable<non-empty-string&lowercase-string,Item>
	 */
	public ArrayAccess&Countable&Traversable $parameters { get; }

}
