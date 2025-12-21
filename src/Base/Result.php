<?php

namespace Slendium\Http\Base;

use Closure;
use LogicException;
use Throwable;

/**
 * @internal
 * @template TResult
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class Result {

	public bool $hasSucceeded {
		get => $this->thrown === null;
	}

	public bool $hasFailed {
		get => $this->thrown !== null;
	}

	/**
	 * Invokes the given closure and returns a `Maybe` with a result if no exceptions are thrown.
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
	 * Shorthand for `Result::try(...)->getResultOrNull()`.
	 * @template T
	 * @param Closure():T $call
	 * @return ?T
	 */
	public static function tryOrNull(Closure $call): mixed {
		return self::try($call)->getResultOrNull();
	}

	private function __construct(

		/** @var ?TResult */
		public readonly mixed $result,

		public readonly ?Throwable $thrown,

	) { }

	/**
	 * Returns the result if `$this->hasSucceeded && $this->result !== null`.
	 * @return TResult
	 */
	public function getResult(): mixed {
		if ($this->hasSucceeded && $this->result !== null) {
			return $this->result;
		}
		throw new LogicException('Result object is not in a state where it is valid to call getResult()');
	}

	/**
	 * Returns the throwable if `$this->hasFailed && $this->thrown !== null`.
	 * @return Throwable
	 */
	public function getThrowable(): Throwable {
		if ($this->hasFailed && $this->thrown !== null) {
			return $this->thrown;
		}
		throw new LogicException('Result object is not in a state where it is valid to call getThrown()');
	}

	/** @return ?TResult */
	public function getResultOrNull(): mixed {
		return $this->thrown === null
			? $this->result
			: null;
	}

}
