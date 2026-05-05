<?php

namespace Slendium\Http\Message;

use ArrayAccess;
use ArrayObject;
use Countable;
use Traversable;

use Slendium\Http\Content\Structured;
use Slendium\Http\Message;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class BodyArgs {

	/** @since 1.0 */
	public static function get(Message $source, string $key): mixed {
		return self::getAll($source)[$key] ?? null;
	}

	/**
	 * @since 1.0
	 * @return ArrayAccess<string,mixed>&Countable&Traversable<string,mixed>
	 */
	public static function getAll(Message $source): ArrayAccess&Countable&Traversable {
		return $source->body instanceof Structured
			? $source->body->root
			: new ArrayObject([ ]);
	}

	private function __construct() { }

}
