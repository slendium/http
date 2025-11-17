<?php

namespace Slendium\Http;

use Closure,
	Throwable;

/**
 * Holds either a value or an exception that was thrown trying to obtain the value.
 * @since 1.0
 * @template TResult
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class Maybe {

	/** @since 1.0 */
	public bool $hasSucceeded {
		get => $this->thrown === null;
	}

	/** @since 1.0 */
	public bool $hasFailed {
		get => $this->thrown !== null;
	}

	/**
	 * Invokes the given closure and returns a `Maybe` with a result if no exceptions are thrown.
	 * @since 1.0
	 * @template T
	 * @param Closure():T $call
	 * @return self<T>
	 */
	public static function try(Closure $call): self {
		$result = null;
		$throwable = null;
		try {
			$result = $call();
		} catch (Throwable $t) {
			$throwable = $t;
		}
		return new self($result, $throwable);
	}

	/**
	 * Shorthand for `Maybe::try(...)->toResultOrNull()`.
	 * @since 1.0
	 * @template T
	 * @param Closure():T $call
	 * @return ?T
	 */
	public static function tryOrNull(Closure $call): mixed {
		return self::try($call)->toResultOrNull();
	}

	private function __construct(

		/**
		 * @since 1.0
		 * @var ?TResult
		 */
		public readonly mixed $result,

		/** @since 1.0 */
		public readonly ?Throwable $thrown,

	) { }

	/**
	 * Returns the result if nothing was thrown, `NULL` otherwise.
	 * @since 1.0
	 * @return ?TResult
	 */
	public function toResultOrNull(): mixed {
		return $this->thrown === null
			? $this->result
			: null;
	}

}
