<?php

namespace Slendium\HttpTests;

use PHPUnit\Framework\TestCase;

use Slendium\Http\Base\Field;

class FieldTest extends TestCase {

	public function test___toString_shouldAddColon_whenCalled() {
		// Arrange
		$sut = new Field('content-type', 'text/plain');

		// Act
		$result = (string)$sut;

		// Assert
		$this->assertTrue(\str_starts_with($result, 'content-type:'));
	}

}
