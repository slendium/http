<?php

namespace Slendium\Http\Network;

use Override;

use Slendium\Http\Base\{
	ParseException,
	SerializeException,
};

/**
 * An IP version 6 network address.
 *
 * @see https://www.rfc-editor.org/rfc/rfc4291.html
 * @see https://www.rfc-editor.org/rfc/rfc5952#section-4
 *
 * @since 1.0
 * @phpstan-import-type Ipv6Words from IpAddress
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class Ipv6Address extends IpAddress {

	private const IPV4_MAP_PREFIX = '::ffff:';

	/**
	 * @since 1.0
	 * @var Ipv6Words
	 */
	public readonly array $words; // @phpstan-ignore property.uninitializedReadonly

	#[Override]
	public static function fromString(string $input): self {
		if (\strpos($input, '.') !== false) {
			return self::fromIpv4Mapping($input);
		}
		$binary = \inet_pton($input);
		if ($binary === false || \strlen($binary) !== 16) {
			throw new ParseException('IPv6 address could not be parsed');
		}
		return IpAddress::V6(\array_values(\unpack('n*', $binary))); // @phpstan-ignore argument.type, argument.type (PHPStan does not know unpack)
	}

	private static function fromIpv4Mapping(string $input): self {
		if (!\str_starts_with($input, self::IPV4_MAP_PREFIX)) {
			throw new ParseException('IPv6-IPv4 mapping address must start with "::ffff:" followed by four octets');
		}
		$ipv4 = Ipv4Address::fromString(\substr($input, \strlen(self::IPV4_MAP_PREFIX)));
		return IpAddress::V6([ // @phpstan-ignore argument.type (phpstan turns array_pad into an unshaped array)
			...\array_pad([ ], 5, 0),
			0xffff,
			($ipv4->octets[0] << 8) + $ipv4->octets[1],
			($ipv4->octets[2] << 8) + $ipv4->octets[3],
		]);
	}

	#[Override]
	protected function onConstruct(mixed ...$args): void {
		$this->words = $args[0]; // @phpstan-ignore assign.propertyType, property.readOnlyAssignNotInConstructor
	}

	/** @return lowercase-string&non-empty-string */
	public function __toString(): string {
		return \inet_ntop(\pack('n*', ...$this->words)); // @phpstan-ignore return.type (phpstan does not understand pack)
	}

}
