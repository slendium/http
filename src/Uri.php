<?php

namespace Slendium\Http;

use ArrayAccess;
use Closure;
use Countable;
use Stringable;
use Traversable;

use Uri\Rfc3986\Uri as Rfc3986Uri;

use Slendium\Http\Base\ParseError;
use Slendium\Http\Base\Query;
use Slendium\Http\Base\Result;

/**
 * An RFC 3986 URI.
 *
 * Since the native RFC 3986 Uri object does not support accessing individual query parameters, this
 * library uses its own abstraction. The methods on this object are very similar to the native Uri
 * object to facilitate upgrading to it later if features are added that make it a better fit. A follow-up
 * RFC is [under discussion](https://wiki.php.net/rfc/uri_followup) which does include support for query
 * parameters. However, at the time of writing this implementation seems to be mutable. This would
 * EITHER mean that every part of the program that uses them can modify the original object OR that
 * a new object is created at every point of use. Neither of these cases are ideal.
 *
 * However, if a native RFC-compliant query parameter parser becomes available it should be used in
 * favor of the `$_QUERY` superglobal. For now, query parameters are implemented using the native
 * functions `parse_str` and `http_build_query`.
 *
 * Since the "password" option is deprecated by RFC 3986, it was not included in this object. An empty
 * password is still allowed and can be indicated by suffixing the "userInfo" property with ':'.
 *
 * @see https://www.rfc-editor.org/rfc/rfc3986.html
 *
 * @since 1.0
 * @phpstan-type QueryParams ArrayAccess<non-empty-string,array<mixed>|string|null>&Countable&Stringable&Traversable<non-empty-string,array<mixed>|string>
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class Uri {

	/** @since 1.0 */
	public static function fromString(string $input): Error|self {
		$result = Result::try(static fn() => new Rfc3986Uri($input));
		if ($result->hasFailed) {
			return new ParseError('Could not parse URI: '.$result->getThrowable()->getMessage());
		}
		$uri = $result->getResult();
		return new self($uri, $uri->getQuery() !== null
			? Query::fromString($uri->getQuery())
			: null
		);
	}

	/**
	 * @since 1.0
	 * @param ?int<0,max> $port
	 * @param ?QueryParams $query
	 */
	public static function create(
		?string $scheme = null,
		?string $userInfo = null,
		?string $host = null,
		?int $port = null,
		string $path = '',
		(ArrayAccess&Countable&Stringable&Traversable)|null $query = null,
		?string $fragment = null,
	): Error|self {
		$result = Result::try(static function() use ($scheme, $userInfo, $host, $port, $path, $query, $fragment) {
			return new Rfc3986Uri('')
				->withScheme($scheme)
				->withUserInfo($userInfo)
				->withHost($host)
				->withPort($port)
				->withPath($path)
				->withQuery($query)
				->withFragment($fragment);
		});
		return $result->hasSucceeded
			? new self($result->getResult(), $query)
			: new ParseError('Could not create URI: '.$result->getThrowable()->getMessage());
	}

	private function __construct(

		private Rfc3986Uri $uri,

		/** @var ?QueryParams */
		private (ArrayAccess&Countable&Stringable&Traversable)|null $query,

	) { }

	/** @since 1.0 */
	public function getScheme(): ?string {
		return $this->uri->getScheme();
	}

	/** @since 1.0 */
	public function withScheme(?string $scheme): ?self {
		return $this->withUriOrNull(fn() => $this->uri->withScheme($scheme));
	}

	/** @since 1.0 */
	public function getUserInfo(): ?string {
		return $this->uri->getUserInfo();
	}

	/** @since 1.0 */
	public function getUsername(): ?string {
		return $this->uri->getUsername();
	}

	/** @since 1.0 */
	public function withUserInfo(?string $userInfo): ?self {
		return $this->withUriOrNull(fn() => $this->uri->withUserInfo($userInfo));
	}

	/** @since 1.0 */
	public function getHost(): ?string {
		return $this->uri->getHost();
	}

	/** @since 1.0 */
	public function withHost(?string $host): ?self {
		return $this->withUriOrNull(fn() => $this->uri->withHost($host));
	}

	/**
	 * @since 1.0
	 * @return ?int<0,65535>
	 */
	public function getPort(): ?int {
		$port = $this->uri->getPort();
		return $port === null || ($port >= 0 && $port <= 65535)
			? $port
			: null;
	}

	/**
	 * @since 1.0
	 * @param ?int<0,max> $port
	 */
	public function withPort(?int $port): ?self {
		return $this->withUriOrNull(fn() => $this->uri->withPort($port));
	}

	/** @since 1.0 */
	public function getPath(): string {
		return $this->uri->getPath();
	}

	/** @since 1.0 */
	public function withPath(string $path): ?self {
		return $this->withUriOrNull(fn() => $this->uri->withPath($path));
	}

	/**
	 * The query data encoded in the URL.
	 *
	 * A count of 0 indicates a single `?` while `NULL` indicates no query was present.
	 *
	 * Values are allowed to be lists/arrays to account for query strings such as `?map[a]=3&map[b]=2`.
	 *
	 * The `__toString()` implementation MUST return the canonical representation according to RFC 3986,
	 * without any `?` prefix.
	 *
	 * Query arguments are case-sensitive according to [RFC 3986](https://www.rfc-editor.org/rfc/rfc3986#section-6.2.2.1).
	 *
	 * @since 1.0
	 * @return ?QueryParams
	 */
	public function getQuery(): (ArrayAccess&Countable&Stringable&Traversable)|null {
		return $this->query;
	}

	/**
	 * @since 1.0
	 * @param QueryParams|string|null $query
	 */
	public function withQuery((ArrayAccess&Countable&Stringable&Traversable)|string|null $query): self {
		return \clone($this, [
			'query' => \is_string($query)
				? Query::fromString($query)
				: $query
		]);
	}

	/**
	 * The hash/fragment part of the URL, without the `#`.
	 *
	 * Can be an empty string to indicate the fragment was empty, i.e. a single `#`
	 *
	 * @since 1.0
	 */
	public function getFragment(): ?string {
		return $this->uri->getFragment();
	}

	/** @since 1.0 */
	public function withFragment(?string $fragment): ?self {
		return $this->withUriOrNull(fn() => $this->uri->withFragment($fragment));
	}

	/** @param Closure():Rfc3986Uri $witherCall */
	private function withUriOrNull(Closure $witherCall): ?self {
		$newUri = Result::tryOrNull($witherCall);
		return $newUri !== null
			? \clone($this, [ 'uri' => $newUri ])
			: null;
	}

}
