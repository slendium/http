<?php

namespace Slendium\HttpTests\Message;

use PHPUnit\Framework\TestCase;

use Slendium\Http\Message\QueryArgs;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class QueryArgsTest extends TestCase {

	public function test_get_shouldReturnExpectedResult(): void {
		// Arrange
		$existingKey = 'exists';
		$existingValue = 67;
		$sut = new MockedMessage(headers: [ new MockedPathField('/', [ $existingKey => $existingValue ]) ]);

		// Act
		// Assert
		$this->assertSame($existingValue, QueryArgs::get($sut, $existingKey));
		$this->assertNull(QueryArgs::get($sut, 'does_not_exist'));
	}

	public function test_getAll_shouldReturnExpectedResult(): void {
		$sut = new MockedMessage(headers: [ new MockedPathField('/', [ 'key' => 'value' ]) ]);

		$result = QueryArgs::getAll($sut);

		$this->assertSame(1, \count($result));
	}

}
