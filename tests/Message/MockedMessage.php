<?php

namespace Slendium\HttpTests\Message;

use Override;
use Stringable;

use Slendium\Http\Field;
use Slendium\Http\Message;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class MockedMessage implements Message {

	public function __construct(

		/** @var iterable<Field> */
		#[Override]
		public readonly iterable $headers = [ ],

		/** @var iterable<Stringable|string> */
		#[Override]
		public readonly iterable $body = [ ],

		/** @var iterable<Field> */
		#[Override]
		public readonly iterable $trailers = [ ],

	) { }

}
