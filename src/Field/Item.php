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

	/**
	 * Attempts to return the item as an integer (casts floats and numeric strings).
	 * @since 1.0
	 */
	public function asInt(): ?int {
		if (\is_int($this->value) || \is_float($this->value)) {
			return (int)$this->value;
		} else if ($this instanceof Item\String_ && \is_numeric($this->value)) {
			return (int)$this->value;
		}
		return null;
	}

	/**
	 * Attempts to return the item as a float (casts ints and numeric strings).
	 * @since 1.0
	 */
	public function asFloat(): ?float {
		if (\is_int($this->value) || \is_float($this->value)) {
			return (float)$this->value;
		} else if ($this instanceof Item\String_ && \is_numeric($this->value)) {
			return (float)$this->value;
		}
		return null;
	}

	/**
	 * Attempts to return the item as a string value (number, string, token or display strings).
	 * @since 1.0
	 */
	public function asString(): ?string {
		if ($this instanceof Item\String_ || $this instanceof Item\Token || $this instanceof Item\DisplayString) {
			return $this->value;
		} else if (\is_int($this->value) || is_float($this->value)) {
			return (string)$this->value;
		}
		return null;
	}

	/**
	 * Attempts to return the item as a binary string.
	 * @since 1.0
	 */
	public function asBinaryString(): ?string {
		return $this instanceof Item\ByteSequence
			? $this->value
			: null;
	}

	/**
	 * Attempts to return the item as a boolean value.
	 * @since 1.0
	 */
	public function asBool(): ?bool {
		if (\is_bool($this->value)) {
			return $this->value;
		} else if (\is_int($this->value)) {
			return $this->value !== 0;
		} else if (\is_float($this->value)) {
			return $this->value !== 0.0;
		}
		return null;
	}

	/**
	 * Attempts to return the item as a date.
	 * @since 1.0
	 */
	public function asDate(): ?DateTimeInterface {
		return $this->value instanceof DateTimeInterface
			? $this->value
			: null;
	}

	/** @internal */
	protected abstract function onConstruct(mixed ...$args): void;

}
