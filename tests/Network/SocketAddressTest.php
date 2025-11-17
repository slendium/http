<?php

namespace Slendium\HttpTests;

use PHPUnit\Framework\TestCase;

use Slendium\Http\Network\{
	IpAddress,
	SocketAddress,
};

class SocketAddressTest extends TestCase {

	public function test___toString_shouldEncloseIpv6_whenIpv6() {
		// Arrange
		$sut = new SocketAddress(IpAddress::V6([ 0xff40, 0, 0, 0, 0, 0, 0, 0x4a0 ]), 8080);

		// Act
		$result = (string)$sut;

		// Assert
		$this->assertSame('[ff40::4a0]:8080', $result);
	}

	public function test___toString_shouldNotEncloseIpv4_whenIpv4() {
		// Arrange
		$sut = new SocketAddress(IpAddress::V4([ 127, 0, 0, 1 ]), 8080);

		// Act
		$result = (string)$sut;

		// Assert
		$this->assertSame('127.0.0.1:8080', $result);
	}

}
