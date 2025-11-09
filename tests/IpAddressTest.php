<?php

namespace Slendium\HttpTests;

use PHPUnit\Framework\TestCase;

use Slendium\Http\Base\IpAddress;

class IpAddressTest extends TestCase {

	public function test_version_shouldBe4_whenIpv4() {
		// Arrange
		$sut = new IpAddress('127.0.0.1');

		// Act
		$result = $sut->version;

		// Assert
		$this->assertSame(4, $result);
	}

	public function test_version_shouldBe6_whenIpv6() {
		// Arrange
		$sut = new IpAddress('::1');

		// Act
		$result = $sut->version;

		// Assert
		$this->assertSame(6, $result);
	}

}
