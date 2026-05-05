<?php

namespace Slendium\HttpTests\Message;

use PHPUnit\Framework\TestCase;

use Slendium\Http\Message\BodyArgs;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class BodyArgsTest extends TestCase {

	public function test_get_shouldReturnExpectedResult(): void {
		// Arrange
		$existingKey = 'exists';
		$existingValue = 67;
		$sut = new MockedMessage(body: new MockedStructuredBody(root: [ $existingKey => $existingValue ]));

		// Act
		// Assert
		$this->assertSame($existingValue, BodyArgs::get($sut, $existingKey));
		$this->assertNull(BodyArgs::get($sut, 'does_not_exist'));
	}

	public function test_getAll_shouldReturnExpectedResult(): void {
		$sut = new MockedMessage(body: new MockedStructuredBody(root: [ 'key' => 'value' ]));

		$result = BodyArgs::getAll($sut);

		$this->assertSame(1, \count($result));
	}

}
