<?php

namespace Slendium\Http\Base;

use ArrayAccess;
use Countable;
use Override;
use Traversable;

use Slendium\Http\Url as IUrl;
use Slendium\Http\Base\ParseException;

/**
 * Basic URL implementation.
 *
 * This implementation explicitly rejects `file:///` URIs since they are not valid URLs, even though
 * PHP natively accepts them.
 *
 * @since 1.0
 * @author C. Fahner
 * @copyright Slendium 2025
 */
class Url implements IUrl {

	/** @since 1.0 */
	public static function fromString(string $input): self {
		if (\str_starts_with($input, 'file:///')) {
			throw new ParseException('URL could not be parsed');
		}
		$parsed = \parse_url($input);
		if ($parsed === false) {
			throw new ParseException('URL could not be parsed');
		}
		return new self(
			scheme: $parsed['scheme']
				?? (\str_starts_with($input, '//') ? '' : null),
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

	/** @return ArrayView<non-empty-string,array<mixed>|string> */
	private static function parseQuery(string $input): ArrayView {
		$parsed = [ ];
		\parse_str($input, $parsed);
		return new ArrayView($parsed); // @phpstan-ignore return.type (phpstan thinks the keys are int|string, but I don't think this is possible)
	}

	/** @since 1.0 */
	public function __construct(

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

		/** @var (ArrayAccess<non-empty-string,array<mixed>|string|null>&Countable&Traversable<non-empty-string,array<mixed>|string>)|null */
		#[Override]
		public readonly (ArrayAccess&Countable&Traversable)|null $query,

		#[Override]
		public readonly ?string $fragment,

	) { }

	public function __toString(): string {
		$result = '';
		if ($this->host !== null) {
			if ($this->scheme === '') {
				$result = '//';
			} else if ($this->scheme !== null) {
				$result = "{$this->scheme}://";
			}
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
			$result .= '?'.\http_build_query(\iterator_to_array($this->query));
		}
		if ($this->fragment !== null) {
			$result .= "#{$this->fragment}";
		}
		return $result;
	}

}
