<?php

namespace Slendium\Http\Base;

use ArrayAccess,
	ArrayIterator,
	Countable,
	IteratorAggregate,
	LogicException,
	Override,
	Traversable;

/**
 * @internal
 * @template TKey of array-key = array-key
 * @template TValue = mixed
 * @implements ArrayAccess<TKey,?TValue>
 * @implements IteratorAggregate<TKey,TValue>
 * @author C. Fahner
 * @copyright Slendium 2025
 */
class ArrayView implements ArrayAccess, Countable, IteratorAggregate {

	public function __construct(

		/** @var array<TKey,TValue> */
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
		throw new LogicException('ArrayView is immutable');
	}

	#[Override]
	public function offsetUnset(mixed $offset): void {
		throw new LogicException('ArrayView is immutable');
	}

	#[Override]
	public function count(): int {
		return \count($this->array);
	}

	#[Override]
	public function getIterator(): Traversable {
		return new ArrayIterator($this->array);
	}

}
