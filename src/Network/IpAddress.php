<?php

namespace Slendium\Http\Network;

use Stringable;
use Throwable;

/**
 * @since 1.0
 * @phpstan-type octet int<0,255>
 * @phpstan-type word int<0,65535>
 * @phpstan-type Ipv4Octets array{0:octet,1:octet,2:octet,3:octet}
 * @phpstan-type Ipv6Words array{0:word,1:word,2:word,3:word,4:word,5:word,6:word,7:word}
 * @author C. Fahner
 * @copyright Slendium 2025
 */
abstract class IpAddress implements Stringable {

	/**
	 * @since 1.0
	 * @param Ipv4Octets $octets
	 */
	public static function V4(array $octets): Ipv4Address {
		return new Ipv4Address($octets);
	}

	/**
	 * @since 1.0
	 * @param Ipv6Words $words
	 */
	public static function V6(array $words): Ipv6Address {
		return new Ipv6Address($words);
	}

	/**
	 * @since 1.0
	 * @throws Throwable When the given string was not a valid IP address
	 */
	public static function fromString(string $input): self {
		return \strpos($input, ':') === false
			? Ipv4Address::fromString($input)
			: Ipv6Address::fromString($input);
	}

	/** @internal */
	protected abstract function onConstruct(mixed ...$args): void;

	private final function __construct(mixed ...$args) {
		$this->onConstruct(...$args);
	}

}
