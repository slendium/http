<?php

namespace Slendium\HttpTests;

use PHPUnit\Framework\TestCase;

use Slendium\Http\ReadOnlyField;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class ReadOnlyFieldTest extends TestCase {

	public function test___toString_shouldAddColon_whenCalled(): void {
		$sut = new ReadOnlyField('content-type', 'text/plain');

		$result = (string)$sut;

		$this->assertSame('content-type: text/plain', $result);
	}

}
