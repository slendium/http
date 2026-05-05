<?php

namespace Slendium\HttpTests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\Http\Network\{
	IpAddress,
	Ipv4Address,
};
use Slendium\Http\Base\ParseException;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2025-2026
 */
class Ipv4AddressTest extends TestCase {

	public static function octetsAndStrings(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ [ 0, 0, 0, 0 ], '0.0.0.0' ];
		yield [ [ 127, 0, 0, 1 ], '127.0.0.1' ];
		yield [ [ 255, 255, 255, 255 ], '255.255.255.255' ];
	}

	#[DataProvider('octetsAndStrings')]
	public function test___toString_shouldMatchStringRepresentation_whenInvoked(array $octets, string $representation): void {
		$sut = IpAddress::V4($octets);

		$result = (string)$sut;

		$this->assertSame($representation, $result);
	}

	#[DataProvider('octetsAndStrings')]
	public function test_fromString_shouldReturn_whenInvokedWithValidString(array $octets, string $input): void {
		$result = Ipv4Address::fromString($input);

		$this->assertSame($octets, $result->octets);
	}

	public static function invalidStrings(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ '' ];
		yield [ '1.1' ];
		yield [ '-1.-1.-1.-1' ];
		yield [ '127.0.0.256' ];
		yield [ '1.0.0.0.1' ];
		yield [ '::ffff' ];
	}

	#[DataProvider('invalidStrings')]
	public function test_fromString_shouldReturnException_whenInvokedWithInvalidString(string $input): void {
		$result = Ipv4Address::fromString($input);

		$this->assertInstanceOf(ParseException::class, $result);
	}

}
