<?php

namespace Slendium\HttpTests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\Http\Network\{
	IpAddress,
	Ipv4Address,
};
use Slendium\Http\Base\ParseException;

class Ipv4AddressTest extends TestCase {

	public static function octetsAndStrings(): iterable {
		yield [ [ 0, 0, 0, 0 ], '0.0.0.0' ];
		yield [ [ 127, 0, 0, 1 ], '127.0.0.1' ];
		yield [ [ 255, 255, 255, 255 ], '255.255.255.255' ];
	}

	#[DataProvider('octetsAndStrings')]
	public function test___toString_shouldMatchStringRepresentation_whenInvoked(array $octets, string $representation): void {
		// Arrange
		$sut = IpAddress::V4($octets);

		// Act
		$result = (string)$sut;

		// Assert
		$this->assertSame($representation, $result);
	}

	#[DataProvider('octetsAndStrings')]
	public function test_fromString_shouldReturn_whenInvokedWithValidString(array $octets, string $input) {
		// Act
		$result = Ipv4Address::fromString($input);

		// Assert
		$this->assertSame($octets, $result->octets);
	}

	public static function invalidStrings(): iterable {
		yield [ '' ];
		yield [ '1.1' ];
		yield [ '-1.-1.-1.-1' ];
		yield [ '127.0.0.256' ];
		yield [ '1.0.0.0.1' ];
		yield [ '::ffff' ];
	}

	#[DataProvider('invalidStrings')]
	public function test_fromString_shouldThrow_whenInvokedWithInvalidString(string $input) {
		// Assert
		$this->expectException(ParseException::class);

		// Act
		Ipv4Address::fromString($input);
	}

}
