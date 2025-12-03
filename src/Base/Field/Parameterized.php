<?php

namespace Slendium\Http\Base\Field;

use ArrayAccess;
use Countable;
use Override;
use Traversable;

use Slendium\Http\Field\Item;
use Slendium\Http\Field\Parameterized as IParameterized;

/**
 * @since 1.0
 * @template T
 * @implements IParameterized<T>
 * @author C. Fahner
 * @copyright Slendium 2025
 */
class Parameterized implements IParameterized {

	/**
	 * Creates a new parameterized but return-hints the interface to help static analyzers.
	 * @template TValue
	 * @param TValue $value
	 * @param ArrayAccess<(non-empty-string&lowercase-string)|int<0,max>,?Item>&Countable&Traversable<non-empty-string&lowercase-string,Item> $parameters
	 * @return IParameterized<TValue>
	 */
	public static function newInterface(mixed $value, ArrayAccess&Countable&Traversable $parameters): IParameterized {
		return new self($value, $parameters);
	}

	/** @since 1.0 */
	public function __construct(

		/**
		 * @override
		 * @var T
		 */
		public readonly mixed $value,

		/**
		 * @override
		 * @var ArrayAccess<(non-empty-string&lowercase-string)|int<0,max>,?Item>&Countable&Traversable<non-empty-string&lowercase-string,Item>
		 */
		public readonly ArrayAccess&Countable&Traversable $parameters,

	) { }

}
