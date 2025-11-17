<?php

namespace Slendium\Http\Network;

use Override;

use Slendium\Http\Base\ParseException;

/**
 * @since 1.0
 * @phpstan-import-type Ipv4Octets from IpAddress
 * @author C. Fahner
 * @copyright Slendium 2025
 */
final class Ipv4Address extends IpAddress {

	/**
	 * @since 1.0
	 * @var Ipv4Octets
	 */
	public readonly array $octets; // @phpstan-ignore property.uninitializedReadonly

	#[Override]
	public static function fromString(string $input): self {
		$binary = \inet_pton($input);
		if ($binary === false || \strlen($binary) !== 4) {
			throw new ParseException('IPv4 address could not be parsed');
		}
		return IpAddress::V4(\array_values(\unpack('C*', $binary))); // @phpstan-ignore argument.type, argument.type (phpstan does not understand unpack)
	}

	#[Override]
	protected function onConstruct(mixed ...$args): void {
		$this->octets = $args[0]; // @phpstan-ignore assign.propertyType, property.readOnlyAssignNotInConstructor
	}

	/** @return lowercase-string&non-empty-string */
	public function __toString(): string {
		return \implode('.', $this->octets);
	}

}
