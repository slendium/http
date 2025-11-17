<?php

namespace Slendium\HttpTests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\Http\Network\{
	IpAddress,
	Ipv4Address,
	Ipv6Address,
};

class IpAddressTest extends TestCase {

	public static function ipv4Versus6DiscriminatorCases(): iterable {
		yield [ '127.0.0.1', Ipv4Address::class ];
		yield [ 'fe80::a10', Ipv6Address::class ];
		yield [ '::ffff:127.0.0.1', Ipv6Address::class ];
	}

	#[DataProvider('ipv4Versus6DiscriminatorCases')]
	public function test_fromString_shouldDistinguishBetweenIpv4AndIpv6_whenInvokedWith4Or6(string $input, string $expectedClass): void {
		// Act
		$result = IpAddress::fromString($input);

		// Assert
		$this->assertInstanceOf($expectedClass, $result);
	}

}
