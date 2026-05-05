<?php

namespace Slendium\HttpTests\Message;

use ArrayAccess;
use ArrayObject;
use Countable;
use IteratorAggregate;
use Override;
use Stringable;
use Traversable;

use Slendium\Http\Content\Structured;

/**
 * @internal
 * @implements IteratorAggregate<Stringable|string>
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class MockedStructuredBody implements IteratorAggregate, Structured {

	/** @var ArrayAccess<string,mixed>&Countable&Traversable<string,mixed> */
	#[Override]
	public readonly ArrayAccess&Countable&Traversable $root;

	public function __construct(

		/** @var iterable<Stringable|string> */
		private readonly iterable $body = [ ],

		/** @var array<string,mixed> */
		array $root = [ ],

	) {
		$this->root = new ArrayObject($root);
	}

	#[Override]
	public function getIterator(): Traversable {
		yield from $this->body;
	}

}
