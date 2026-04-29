<?php

namespace Slendium\Http\Message;

use ArrayAccess;
use InvalidArgumentException;

use Slendium\Http\Field;

/**
 * Contains accessor methods for HTTP message header and trailer collections.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class Fields {

	/**
	 * @since 1.0
	 * @param iterable<Field> $fields
	 * @param lowercase-string&non-empty-string $name
	 */
	public static function contains(iterable $fields, string $name): bool {
		return self::getFirst($fields, $name) !== null;
	}

	/**
	 * @since 1.0
	 * @param iterable<Field> $fields
	 * @param lowercase-string&non-empty-string $name
	 */
	public static function getFirst(iterable $fields, string $name): ?Field {
		if (self::isAssociative($fields)) {
			foreach (self::iterateOffsetResult($fields[$name] ?? [ ]) as $field) {
				return $field;
			}
			return null;
		}

		foreach ($fields as $field) {
			if ($field->name === $name) {
				return $field;
			}
		}
		return null;
	}

	/**
	 * @since 1.0
	 * @param iterable<Field> $fields
	 * @param lowercase-string&non-empty-string $name
	 * @return iterable<Field>
	 */
	public static function getAll(iterable $fields, string $name): iterable {
		if (self::isAssociative($fields)) {
			yield from self::iterateOffsetResult($fields[$name] ?? [ ]);
			return;
		}

		foreach ($fields as $field) {
			if ($field->name === $name) {
				yield $field;
			}
		}
	}

	/** @return iterable<Field> */
	private static function iterateOffsetResult(mixed $result): iterable {
		if (!\is_iterable($result)) {
			$result = [ $result ];
		}

		foreach ($result as $item) {
			if ($item instanceof Field) {
				yield $item;
			} else {
				throw new InvalidArgumentException('Message headers/trailers may only contain Fields, not '.\get_debug_type($item));
			}
		}
	}

	/**
	 * @template T
	 * @param iterable<T> $value
	 * @phpstan-assert-if-true ArrayAccess<mixed,T>|array<T> $value
	 */
	private static function isAssociative(iterable $value): bool {
		return \is_array($value) && !\array_is_list($value) || $value instanceof ArrayAccess;
	}

	private function __construct() { }

}
