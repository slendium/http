<?php

namespace Slendium\Http\Base;

use Override;

use Slendium\Http\Error;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class ParseError implements Error {

	public function __construct(

		/** @var non-empty-string */
		#[Override]
		public readonly string $message

	) { }

}
