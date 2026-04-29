<?php

namespace Slendium\Http\Message;

use Slendium\Http\Field;
use Slendium\Http\Message;

/**
 * Identical to {@see Fields}, except these methods operate exclusively on the `$headers` property
 * of a {@see Message}.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Headers {

	/**
	 * @since 1.0
	 * @param lowercase-string&non-empty-string $name
	 */
	public static function contains(Message $message, string $name): bool {
		return Fields::contains($message->headers, $name);
	}

	/**
	 * @since 1.0
	 * @param lowercase-string&non-empty-string $name
	 */
	public static function getFirst(Message $message, string $name): ?Field {
		return Fields::getFirst($message->headers, $name);
	}

	/**
	 * @since 1.0
	 * @param lowercase-string&non-empty-string $name
	 * @return iterable<Field>
	 */
	public static function getAll(Message $message, string $name): iterable {
		return Fields::getAll($message->headers, $name);
	}

	private function __construct() { }

}
