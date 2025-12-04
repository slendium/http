<?php

namespace Slendium\HttpTests;

use PHPUnit\Framework\TestCase;

use Slendium\Http\Base\Networked;
use Slendium\Http\Network\IpAddress;
use Slendium\Http\Network\SocketAddress;

class NetworkedTest extends TestCase {

	public function test___construct_shouldNotError_whenCalled() {
		// Arrange
		$address = new SocketAddress(IpAddress::V4([ 127, 0, 0, 1 ]), 80);
		$payload = 'test';

		// Assert
		$this->expectNotToPerformAssertions();

		// Act
		new Networked(address: $address, payload: $payload);
	}

}
