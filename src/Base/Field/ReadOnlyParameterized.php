<?php

namespace Slendium\Http\Base\Field;

use ArrayAccess;
use Countable;
use Override;
use Traversable;

use Slendium\Http\Field\Item;
use Slendium\Http\Field\Parameterized;

/**
 * @since 1.0
 * @template T
 * @implements Parameterized<T>
 * @author C. Fahner
 * @copyright Slendium 2025
 */
readonly class ReadOnlyParameterized implements Parameterized {

	/**
	 * Creates a new parameterized but return-hints the interface to help static analyzers.
	 * @template TValue
	 * @param TValue $data
	 * @param ArrayAccess<(non-empty-string&lowercase-string)|int<0,max>,?Item>&Countable&Traversable<non-empty-string&lowercase-string,Item> $parameters
	 * @return Parameterized<TValue>
	 */
	public static function newInterface(mixed $data, ArrayAccess&Countable&Traversable $parameters): Parameterized {
		return new self($data, $parameters);
	}

	/**
	 * Creates a new `Parameterized` without an empty parameter list.
	 * @template TValue
	 * @param TValue $data
	 * @return Parameterized<TValue>
	 */
	public static function withoutParameters(mixed $data): Parameterized {
		return new self($data, new Parameters([ ]));
	}

	/** @since 1.0 */
	public function __construct(

		/** @var T */
		 #[Override]
		public mixed $data,

		/** @var ArrayAccess<(non-empty-string&lowercase-string)|int<0,max>,?Item>&Countable&Traversable<non-empty-string&lowercase-string,Item> */
		#[Override]
		public ArrayAccess&Countable&Traversable $parameters,

	) { }

}
