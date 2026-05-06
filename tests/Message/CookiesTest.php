<?php

namespace Slendium\HttpTests\Message;

use PHPUnit\Framework\TestCase;

use Slendium\Http\Message\Cookies;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class CookiesTest extends TestCase {

	public function test_get_shouldReturnExpectedResult(): void {
		$sut = [ new MockedCookieField([ 'test' => '123' ]) ];

		$result = Cookies::get($sut, 'test');

		$this->assertSame('123', $result);
		$this->assertNull(Cookies::get($sut, 'does_not_exist'));
	}

	public function test_getAll_shouldReturnExpectedResult(): void {
		$sut = [ new MockedCookieField([ 'a' => '67', 'b' => '69' ]) ];

		$result = Cookies::getAll($sut);

		$this->assertSame(2, \count($result));
		$this->assertSame('67', $result['a']);
		$this->assertSame('69', $result['b']);
	}

}
