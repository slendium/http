<?php

namespace Slendium\Http\Base\Field;

use Countable;
use IteratorAggregate;
use Override;
use SplFixedArray;
use Traversable;

use Slendium\Http\Field\Item;
use Slendium\Http\Field\Parameterized as IParameterized;

/**
 * @internal
 * @implements IteratorAggregate<int,IParameterized<(Countable&Traversable<int,IParameterized<Item>>)|Item>>
 * @author C. Fahner
 * @copyright Slendium 2025
 */
class List_ implements Countable, IteratorAggregate {

	/** @var SplFixedArray<IParameterized<(Countable&Traversable<int,IParameterized<Item>>)|Item>> */
	private readonly SplFixedArray $items;

	/** @param iterable<IParameterized<(Countable&Traversable<int,IParameterized<Item>>)|Item>> $items */
	public function __construct(iterable $items) {
		$this->items = SplFixedArray::fromArray(\array_values(\is_array($items)
			? $items
			: \iterator_to_array($items)
		));
	}

	#[Override]
	public function count(): int {
		return \count($this->items);
	}

	#[Override]
	public function getIterator(): Traversable {
		return $this->items; // @phpstan-ignore return.type (phpstan doesnt understand SplFixedArray very well)
	}

}
