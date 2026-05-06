<?php

namespace Slendium\HttpTests\Message;

use ArrayObject;
use Override;
use LogicException;

use Slendium\Http\Content\Structured;
use Slendium\Http\Field;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class MockedCookieField implements Field, Structured {

	#[Override]
	public string $name {
		get => 'cookie';
	}

	#[Override]
	public string $value {
		get => throw new LogicException('Unexpected access to the raw value of mocked cookie field');
	}

	#[Override]
	public readonly ArrayObject $root;

	public function __construct(array $cookies) {
		$this->root = new ArrayObject($cookies);
	}

	public function __toString(): string {
		throw new LogicException('Unexpected access to stringified representation of mocked cookie field');
	}

}
