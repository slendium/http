<?php

namespace Slendium\Http\Base;

use ArrayAccess;
use IteratorAggregate;
use LogicException;
use OutOfBoundsException;
use Override;
use Traversable;

use Slendium\Http\Field;

/**
 * @since 1.0
 * @implements ArrayAccess<?(non-empty-string&lowercase-string),list<Field>>
 * @implements IteratorAggregate<Field>
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class FieldSet implements ArrayAccess, IteratorAggregate {

	/** @var array<non-empty-string&lowercase-string,list<Field>> */
	private array $map = [ ];

	/**
	 * @since 1.0
	 * @param iterable<Field> $fields
	 */
	public function __construct(iterable $fields = [ ]) {
		foreach ($fields as $field) {
			$this->append($field);
		}
	}

	#[Override]
	public function offsetExists(mixed $offset): bool {
		return \is_string($offset) && isset($this->map[$offset]);
	}

	#[Override]
	public function offsetGet(mixed $offset): mixed {
		if (\is_string($offset) && isset($this->map[$offset])) {
			return $this->map[$offset];
		}

		throw new OutOfBoundsException("Offset `$offset` does not exist");
	}

	#[Override]
	public function offsetSet(mixed $offset, mixed $value): void {
		if ($offset === null) {
			foreach ($value as $field) {
				$this->append($field);
			}
			return;
		}

		throw new LogicException('Ambiguous operation, use the more explicit methods append() or replace()');
	}

	#[Override]
	public function offsetUnset(mixed $offset): void {
		if ($offset === null) {
			throw new LogicException('Cannot unset using NULL as offset');
		}

		unset($this->map[$offset]);
	}

	#[Override]
	public function getIterator(): Traversable {
		foreach ($this->map as $fields) {
			foreach ($fields as $field) {
				yield $field;
			}
		}
	}

	/**
	 * @since 1.0
	 * @return $this
	 */
	public function append(Field $field): self {
		if (!isset($this->map[$field->name])) {
			$this->map[$field->name] = [ ];
		}

		$this->map[$field->name][] = $field;
		return $this;
	}

	/**
	 * @since 1.0
	 * @return $this
	 */
	public function replace(Field $field): self {
		$this->map[$field->name] = [ $field ];
		return $this;
	}

}
