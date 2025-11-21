<?php

namespace Slendium\Http\Field;

use DateTimeInterface;
use TypeError;

/**
 * Base class for all items.
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
abstract class Item {

	private static Item\Boolean $booleanTrue; // TODO replace with static property hook in 8.5

	private static Item\Boolean $booleanFalse; // TODO replace with static property hook in 8.5

	/**
	 * Gives access to the relevant PHP type.
	 * @since 1.0
	 */
	public abstract DateTimeInterface|string|float|int|bool $value { get; }

	/** @since 1.0 */
	public static function Integer(int $value): Item\Integer {
		return new Item\Integer($value);
	}

	/** @since 1.0 */
	public static function Decimal(float $value): Item\Decimal {
		return new Item\Decimal($value);
	}

	/** @since 1.0 */
	public static function String(string $value): Item\String_ {
		return new Item\String_($value);
	}

	/** @since 1.0 */
	public static function Token(string $value): Item\Token {
		return new Item\Token($value);
	}

	/** @since 1.0 */
	public static function ByteSequence(string $value): Item\ByteSequence {
		return new Item\ByteSequence($value);
	}

	/** @since 1.0 */
	public static function Boolean(bool $value): Item\Boolean {
		return $value
			? (self::$booleanTrue ??= new Item\Boolean(true))
			: (self::$booleanFalse ??= new Item\Boolean(false));
	}

	/** @since 1.0 */
	public static function Date(DateTimeInterface $value): Item\Date {
		return new Item\Date($value);
	}

	/** @since 1.0 */
	public static function DisplayString(string $value): Item\DisplayString {
		return new Item\DisplayString($value);
	}

	private function __construct(mixed ...$args) {
		$this->onConstruct(...$args);
	}

	/** @internal */
	protected abstract function onConstruct(mixed ...$args): void;

}
