<?php

namespace Slendium\Http\Message;

use ArrayAccess;
use ArrayObject;
use Countable;
use Traversable;

use Slendium\Http\Content\Structured;
use Slendium\Http\Field;
use Slendium\Http\Message;

/**
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Cookies {

	/**
	 * @since 1.0
	 * @param Message|iterable<Field> $source
	 */
	public static function get(Message|iterable $source, string $name): ?string {
		$cookies = self::getAll($source);

		return isset($cookies[$name]) && \is_scalar($cookies[$name])
			? (string)$cookies[$name]
			: null;
	}

	/**
	 * @since 1.0
	 * @param Message|iterable<Field> $source
	 * @return ArrayAccess<string,mixed>&Countable&Traversable<string,mixed>
	 */
	public static function getAll(Message|iterable $source): ArrayAccess&Countable&Traversable {
		if ($source instanceof Message) {
			$source = $source->headers;
		}

		$cookie = Fields::getFirst($source, 'cookie');
		return $cookie instanceof Structured
			? $cookie->root
			: new ArrayObject([ ]);
	}

	private function __construct() { }

}
