<?php

namespace Slendium\Http\Base\Field;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use LogicException;
use Override;
use Traversable;

use Slendium\Http\Field\Item;
use Slendium\Http\Field\Parameterized as IParameterized;

/**
 * @internal
 * @implements ArrayAccess<non-empty-string&lowercase-string,?IParameterized<(Countable&Traversable<int,IParameterized<Item>>)|Item>>
 * @implements IteratorAggregate<non-empty-string&lowercase-string,IParameterized<(Countable&Traversable<int,IParameterized<Item>>)|Item>>
 * @author C. Fahner
 * @copyright Slendium 2025
 */
class Dictionary implements ArrayAccess, Countable, IteratorAggregate {

	public function __construct(

		/** @var array<(non-empty-string&lowercase-string),IParameterized<(Countable&Traversable<int,IParameterized<Item>>)|Item>> */
		private readonly array $array,

	) { }

	#[Override]
	public function offsetExists(mixed $offset): bool {
		return isset($this->array[$offset]);
	}

	#[Override]
	public function offsetGet(mixed $offset): mixed {
		return $this->array[$offset] ?? null;
	}

	#[Override]
	public function offsetSet(mixed $offset, mixed $value): void {
		throw new LogicException('Dictionary is immutable');
	}

	#[Override]
	public function offsetUnset(mixed $offset): void {
		throw new LogicException('Dictionary is immutable');
	}

	#[Override]
	public function count(): int {
		return \count($this->array);
	}

	#[Override]
	public function getIterator(): Traversable {
		return new ArrayIterator($this->array); // @phpstan-ignore return.type (ArrayIterator seems to destroy the lowercase&non-empty nature of the string keys)
	}

}
