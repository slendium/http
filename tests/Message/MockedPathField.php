<?php

namespace Slendium\HttpTests\Message;

use ArrayAccess;
use ArrayObject;
use Countable;
use Exception;
use Override;
use Traversable;

use Slendium\Http\Content\Structured;
use Slendium\Http\Field;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class MockedPathField implements Field, Structured {

	#[Override]
	public string $name {
		get => ':path';
	}

	#[Override]
	public string $value {
		get => $this->path.'?'.\http_build_query($this->query);
	}

	#[Override]
	public ArrayAccess&Countable&Traversable $root {
		get => new ArrayObject($this->query);
	}

	public function __construct(

		private readonly string $path = '/',

		/** @var array<string,mixed> */
		private readonly array $query = [ ],

	) { }

	public function __toString(): string {
		return "{$this->name}: {$this->value}";
	}

}
