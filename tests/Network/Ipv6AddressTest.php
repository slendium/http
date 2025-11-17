<?php

namespace Slendium\HttpTests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\Http\Network\{
	IpAddress,
	Ipv6Address,
};
use Slendium\Http\Base\ParseException;

class Ipv6AddressTest extends TestCase {

	public static function validIpv6Inputs(): iterable {
		yield [ [ 0, 0, 0, 0, 0, 0, 0, 0 ], '::' ];
		yield [ [ 0, 0, 0, 0, 0, 0, 0, 1 ], '::1' ];
		yield [ [ 0, 0, 0, 0, 0, 0xffff, 0xc0a8, 0x0101 ], '::ffff:192.168.1.1' ];
		yield [ [ 0, 0, 0, 0, 0, 0xffff, 0xc0a8, 0x0101 ], '::ffff:c0a8:101' ];
		yield [ [ 0x2600, 0, 0, 0, 0, 0, 0, 0 ], '2600::' ];
		yield [ [ 0x2600, 0, 0, 0, 0, 0, 0, 0xfe80 ], '2600::fe80' ];
		yield [ [ 0x2340, 0x0425, 0x2ca1, 0x0, 0x0, 0x0560, 0x5673, 0x23b5 ], '2340:0425:2ca1:0000:0000:0560:5673:23b5' ];
	}

	#[DataProvider('validIpv6Inputs')]
	public function test_fromString_shouldReturn_whenInputValid(array $expectedWords, string $representation) {
		// Act
		$result = Ipv6Address::fromString($representation)->words;

		// Assert
		$this->assertSame($expectedWords, $result);
	}

	public static function invalidIpv6Inputs(): iterable {
		yield [ '' ];
		yield [ '2501::fe40::f3a2' ]; // more than one "::" shortener is not allowed
		yield [ '0425:2ca1:0000:0000:0560:5673:23b5' ]; // 7 octets
		yield [ '284d:2340:0425:2ca1:0000:0000:0560:5673:23b5' ]; // 9 octets
		yield [ '2340:0425:2ca1:0000.1:0000:0560:5673:23b5' ]; // decimal number
		yield [ '2340:0425:2ca1:zzzz:zzzz:0560:5673:23b5' ]; // invalid chars
		yield [ '2340:0425:2ca1:abc_:abc_:0560:5673:23b5' ]; // invalid chars
	}

	#[DataProvider('invalidIpv6Inputs')]
	public function test_fromString_shouldThrow_whenInputInvalid(string $input) {
		// Assert
		$this->expectException(ParseException::class);

		// Act
		Ipv6Address::fromString($input);
	}

	public static function canonicalIpv6(): iterable {
		yield [ [ 0, 0, 0, 0, 0, 0, 0, 0 ], '::' ];
		yield [ [ 0, 0, 0, 0, 0, 0, 0, 0xfe80 ], '::fe80' ];
		yield [ [ 0xfe80, 0, 0, 0, 0, 0, 0, 0 ], 'fe80::' ];
		yield [ [ 0x1234, 0, 0, 0, 0, 0, 0, 0xff ], '1234::ff' ];
		yield [ [ 0x2340, 0x0425, 0x2ca1, 0x1, 0x1, 0x0560, 0x5673, 0x23b5 ], '2340:425:2ca1:1:1:560:5673:23b5' ];
	}

	#[DataProvider('canonicalIpv6')]
	public function test___toString_shouldReturnCanonicalForm_whenInputValid(array $octets, string $expectedCanonicalForm) {
		// Arrange
		$sut = IpAddress::V6($octets);

		// Act
		$result = (string)$sut;

		// Assert
		$this->assertSame($expectedCanonicalForm, $result);
	}

}
