<?php

namespace Slendium\Http\Base;

use ArrayAccess;
use Countable;
use Override;
use Stringable;
use Traversable;

use Slendium\Http\Error;
use Slendium\Http\Uri as IUri;
use Slendium\Http\Base\ParseError;

/**
 * RFC 3986 URI implementation.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
class Uri implements IUri {

	/** @since 1.0 */
	public static function fromString(string $input): Error|self {
		$parsed = \parse_url($input);
		if ($parsed === false) {
			return new ParseError('URI could not be parsed');
		}
		return new self(
			scheme: isset($parsed['scheme']) && $parsed['scheme'] !== ''
				? $parsed['scheme']
				: null,
			userInfo: $parsed['user']
				?? null,
			host: isset($parsed['host']) && $parsed['host'] !== ''
				? $parsed['host']
				: null,
			port: $parsed['port']
				?? null,
			path: isset($parsed['path']) && $parsed['path'] !== ''
				? $parsed['path']
				: null,
			query: isset($parsed['query'])
				? self::parseQuery($parsed['query'])
				: null,
			fragment: $parsed['fragment']
				?? null,
		);
	}

	/** @return ArrayAccess<non-empty-string,array<mixed>|string|null>&Countable&Stringable&Traversable<non-empty-string,array<mixed>|string> */
	private static function parseQuery(string $input): ArrayAccess&Countable&Stringable&Traversable {
		$parsed = [ ];
		\parse_str($input, $parsed);
		return new Query($parsed); // @phpstan-ignore argument.type, return.type (phpstan thinks the keys are int|string, but I don't think this is possible, also there seems to be an error with ArrayAccess<TValue> which are identical??)
	}

	/** @since 1.0 */
	public function __construct(

		/** @var ?non-empty-string */
		#[Override]
		public readonly ?string $scheme,

		#[Override]
		public readonly ?string $userInfo,

		/** @var ?non-empty-string */
		#[Override]
		public readonly ?string $host,

		/** @var ?int<0,65535> */
		#[Override]
		public readonly ?int $port,

		/** @var ?non-empty-string */
		#[Override]
		public readonly ?string $path,

		/** @var (ArrayAccess<non-empty-string,array<mixed>|string|null>&Countable&Stringable&Traversable<non-empty-string,array<mixed>|string>)|null */
		#[Override]
		public readonly (ArrayAccess&Countable&Stringable&Traversable)|null $query,

		#[Override]
		public readonly ?string $fragment,

	) { }

	public function __toString(): string {
		$result = '';
		if ($this->host !== null) {
			if ($this->scheme !== null) {
				$result = "{$this->scheme}:";
			}
			$result .= '//';
			if ($this->userInfo !== null) {
				$result .= "{$this->userInfo}@";
			}
			$result .= $this->host;
			if ($this->port !== null) {
				$result .= $this->port;
			}
		}
		if ($this->path !== null) {
			$result .= $this->path;
		}
		if ($this->query !== null) {
			$result .= "?{$this->query}";
		}
		if ($this->fragment !== null) {
			$result .= "#{$this->fragment}";
		}
		return $result;
	}

}
