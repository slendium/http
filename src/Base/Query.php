<?php

namespace Slendium\Http\Base;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use LogicException;
use Override;
use Traversable;

/**
 * @since 1.0
 * @implements ArrayAccess<non-empty-string,array<mixed>|string|null>
 * @implements IteratorAggregate<non-empty-string,array<mixed>|string>
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class Query implements ArrayAccess, Countable, IteratorAggregate {

	/** @since 1.0 */
	public function __construct(

		/** @var array<non-empty-string,array<mixed>|string> */
		private array $query = [ ],

	) { }

	#[Override]
	public function offsetExists(mixed $offset): bool {
		return isset($this->query[$offset]);
	}

	#[Override]
	public function offsetGet(mixed $offset): mixed {
		return $this->query[$offset] ?? null;
	}

	#[Override]
	public function offsetSet(mixed $offset, mixed $value): void {
		throw new LogicException('Query is immutable');
	}

	#[Override]
	public function offsetUnset(mixed $offset): void {
		throw new LogicException('Query is immutable');
	}

	#[Override]
	public function count(): int {
		return \count($this->query);
	}

	#[Override]
	public function getIterator(): Traversable {
		return new ArrayIterator($this->query); // @phpstan-ignore return.type (PHPStan loses specific array key types in constuctors)
	}

}
