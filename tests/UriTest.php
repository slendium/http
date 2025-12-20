<?php

namespace Slendium\HttpTests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\Http\Error;
use Slendium\Http\Base\ParseException;
use Slendium\Http\Base\Uri;

class UriTest extends TestCase {

	public function test_fromString_shouldHaveEverything_whenInputHasEverything() {
		// Act
		$sut = Uri::fromString('ftp://user:pass@test.example.com:8080/path?query=true#fragment');

		// Assert
		$this->assertSame('ftp', $sut->scheme);
		$this->assertSame('user', $sut->userInfo);
		$this->assertSame('test.example.com', $sut->host);
		$this->assertSame(8080, $sut->port);
		$this->assertSame('/path', $sut->path);
		$this->assertSame(1, \count($sut->query));
		$this->assertSame('true', $sut->query['query']);
		$this->assertNull($sut->query['not_query']);
		$this->assertSame('fragment', $sut->fragment);
	}

	public function test_fromString_shouldReturnNullScheme_whenInputSchemeRelative() {
		// Arrange
		$sut = Uri::fromString('//example.com');

		// Act
		$result = $sut->scheme;

		// Assert
		$this->assertNull($result);
	}

	public static function unparseableUris(): iterable {
		yield [ 'http://' ];
		yield [ 'http://?query=true' ];
		yield [ 'http://#fragment' ];
		yield [ 'http:///' ];
	}

	#[DataProvider('unparseableUris')]
	public function test_fromString_shouldReturnError_whenUriUnparseable(string $input) {
		// Act
		$result = Uri::fromString($input);

		// Assert
		$this->assertInstanceOf(Error::class, $result);
	}

	public function test_fromString_shouldAccountForEmptyQuery_whenInputHasEmptyQuery() {
		// Act
		$sut = Uri::fromString('//example.com?');

		// Assert
		$this->assertNull($sut->scheme);
		$this->assertSame('example.com', $sut->host);
		$this->assertNotNull($sut->query);
		$this->assertSame(0, \count($sut->query));
	}

	public function test_fromString_shouldAccountForMissingQuery_whenInputHasNoQuery() {
		// Act
		$sut = Uri::fromString('//example.com');

		// Assert
		$this->assertNull($sut->scheme);
		$this->assertSame('example.com', $sut->host);
		$this->assertNull($sut->query);
	}

	public function test_fromString_shouldAccountForEmptyFragment_whenInputHasEmptyFragment() {
		// Act
		$sut = Uri::fromString('/path#');

		// Assert
		$this->assertSame('/path', $sut->path);
		$this->assertSame('', $sut->fragment);
	}

	public function test_fromString_shouldAccountForNullFragment_whenInputHasNoFragment() {
		// Act
		$sut = Uri::fromString('/path');

		// Assert
		$this->assertSame('/path', $sut->path);
		$this->assertNull($sut->fragment);
	}

	public function test_fromString_shouldParseQueryArrays_whenInputUsesArraySyntax() {
		// Act
		$sut = Uri::fromString('/path?arr[]=1&arr[]=2&map[a]=a&map[b][]=b1&map[b][]=b2');

		// Assert
		$this->assertSame('/path', $sut->path);
		$this->assertSame(2, \count($sut->query));
		$this->assertSame([ '1', '2' ], $sut->query['arr']);
		$this->assertSame([ 'a' => 'a', 'b' => [ 'b1', 'b2' ] ], $sut->query['map']);
	}

}
