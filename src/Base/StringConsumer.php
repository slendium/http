<?php

namespace Slendium\Http\Base;

use LogicException,
	NoDiscard;

/**
 * Consumes an ASCII string one (or more) character(s) at a time.
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class StringConsumer {

	public bool $hasMore {
		get => $this->index < $this->length;
	}

	/** @var int<0,max> */
	private int $index = 0;

	private readonly int $length;

	public function __construct(

		private readonly string $string,

	) {
		$this->length = \strlen($string);
	}

	public function __toString(): string {
		return \substr($this->string, $this->index);
	}

	/**
	 * @throws ParseException When consuming more characters than remain in the string
	 * @param int<1,max> $count
	 * @return non-empty-string
	 */
	#[NoDiscard]
	public function consume(int $count): string {
		$end = $this->index + $count;
		if ($end > $this->length) {
			throw new ParseException("Unexpected end of string, expected $count more bytes");
		}
		$result = $count === 1
			? $this->string[$this->index]
			: \substr($this->string, $this->index, $count);
		$this->index = $end;
		return $result; // @phpstan-ignore return.type (will never return empty string since we throw if end > len and count is always >= 1)
	}

	/**
	 * Explicit discard method, to prevent unnecessary consume calls.
	 * @throws ParseException When discarding past the end of the string
	 * @param int<1,max> $count
	 */
	public function discard(int $count): void {
		$end = $this->index + $count;
		if ($end > $this->length) {
			throw new ParseException("Unexpected end of string, expected $count more bytes");
		}
		$this->index = $end;
	}

	/** Discards as long as the next character is a space (OWS: aka optional whitespace, zero or more spaces). */
	public function discardSpaces(): void {
		while ($this->peek(1) === ' ') {
			$this->discard(1);
		}
	}

	/** @param int<1,max> $count */
	#[NoDiscard]
	public function peek(int $count): string {
		return ($this->index + $count) <= $this->length
			? ($count === 1
				? $this->string[$this->index]
				: \substr($this->string, $this->index, $count))
			: '';
	}

	/** @param int<1,max> $amount */
	public function rewind(int $amount): void {
		$targetIndex = $this->index - $amount;
		if ($targetIndex < 0) {
			throw new LogicException('Unexpected rewind beyond the start of the string');
		}
		$this->index = $targetIndex;
	}

}
