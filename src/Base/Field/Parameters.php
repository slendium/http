<?php

namespace Slendium\Http\Base\Field;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use LogicException;
use Override;
use SplFixedArray;
use Traversable;

use Slendium\Http\Field\Item;

/**
 * @internal
 * @implements ArrayAccess<(lowercase-string&non-empty-string)|int<0,max>,?Item>
 * @implements IteratorAggregate<lowercase-string&non-empty-string,Item>
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class Parameters implements ArrayAccess, Countable, IteratorAggregate {

	/** @var array<lowercase-string&non-empty-string,Item> */
	private readonly array $byKey;

	/** @var SplFixedArray<Item> */
	private readonly SplFixedArray $byIndex;

	#[Override]
	public function offsetExists(mixed $offset): bool {
		return match(true) {
			\is_int($offset) => isset($this->byIndex[$offset]),
			default => isset($this->byKey[$offset])
		};
	}

	#[Override]
	public function offsetGet(mixed $offset): mixed {
		if (\is_int($offset) && isset($this->byIndex[$offset])) {
			return $this->byIndex[$offset]; // @phpstan-ignore return.type (phpstan doesnt understand SplFixedArray key type)
		}
		return \is_string($offset) && isset($this->byKey[$offset])
			? $this->byKey[$offset]
			: null;
	}

	#[Override]
	public function offsetSet(mixed $offset, mixed $value): void {
		throw new LogicException('Parsed parameters are immutable');
	}

	#[Override]
	public function offsetUnset(mixed $offset): void {
		throw new LogicException('Parsed parameters are immutable');
	}

	#[Override]
	public function count(): int {
		return \count($this->byKey);
	}

	#[Override]
	public function getIterator(): Traversable {
		return new ArrayIterator($this->byKey); // @phpstan-ignore return.type (array key information seems to get lost)
	}

	/** @param array<lowercase-string&non-empty-string,Item> $values */
	public function __construct(array $values) {
		$byKey = [ ];
		$byIndex = new SplFixedArray(\count($values));
		$i = 0;
		foreach ($values as $key => $item) {
			$byKey[$key] = $item;
			$byIndex[$i] = $item;
			$i += 1;
		}
		$this->byKey = $byKey;
		$this->byIndex = $byIndex; // @phpstan-ignore assign.propertyType (does not recognize that SplFixedArray only contains Items)
	}

}
