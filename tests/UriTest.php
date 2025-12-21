<?php

namespace Slendium\HttpTests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\Http\Error;
use Slendium\Http\Uri;

class UriTest extends TestCase {

	public function test_fromString_shouldHaveEverything_whenInputHasEverything() {
		// Act
		$sut = Uri::fromString('ftp://user:pass@test.example.com:8080/path?query=true#fragment');

		// Assert
		$this->assertSame('ftp', $sut->getScheme());
		$this->assertSame('user:pass', $sut->getUserInfo());
		$this->assertSame('user', $sut->getUsername());
		$this->assertSame('test.example.com', $sut->getHost());
		$this->assertSame(8080, $sut->getPort());
		$this->assertSame('/path', $sut->getPath());
		$this->assertSame(1, \count($sut->getQuery()));
		$this->assertSame('true', $sut->getQuery()['query']);
		$this->assertNull($sut->getQuery()['not_query']);
		$this->assertSame('fragment', $sut->getFragment());
	}

	public function test_fromString_shouldReturnNullScheme_whenInputSchemeRelative() {
		// Arrange
		$sut = Uri::fromString('//example.com');

		// Act
		$result = $sut->getScheme();

		// Assert
		$this->assertNull($result);
	}

	public static function unparseableUris(): iterable {
		yield [ 'http://example.com/foo bar' ];
		yield [ 'http://example.com/<foo>' ];
		yield [ 'http://example.com/%' ];
		yield [ 'http://example.com/%1' ];
		yield [ 'http://example.com/%qq' ];
		yield [ '0http://example.com' ];
		yield [ 'http*://example.com' ];
		yield [ 'http*://example.com/path\\with\\backslashes' ];
		yield [ '://example.com' ];
		yield [ '//example.com:invalid' ];
		yield [ '//example.com:-1' ];
		yield [ '//example.com##fragment' ];
	}

	#[DataProvider('unparseableUris')]
	public function test_fromString_shouldReturnError_whenUriUnparseable(string $input) {
		// Act
		$result = Uri::fromString($input);

		// Assert
		$this->assertInstanceOf(Error::class, $result);
	}

	public static function edgeCaseUris(): iterable {
		yield [ 'http+a-b.c://example.com' ];
		yield [ 'http://example.com:90000' ]; // RFC 3986 allows invalid port numbers too
		yield [ '//example.com//path??query' ];
		yield [ '//example.com#fragment?query' ];
	}

	#[DataProvider('edgeCaseUris')]
	public function test_fromString_shouldReturnUri_whenEdgeCaseGiven(string $input) {
		// Act
		$result = Uri::fromString($input);

		// Assert
		$this->assertInstanceOf(Uri::class, $result);
	}

	public function test_fromString_shouldAccountForEmptyQuery_whenInputHasEmptyQuery() {
		// Act
		$sut = Uri::fromString('//example.com?');

		// Assert
		$this->assertNull($sut->getScheme());
		$this->assertSame('example.com', $sut->getHost());
		$this->assertNotNull($sut->getQuery());
		$this->assertSame(0, \count($sut->getQuery()));
	}

	public function test_fromString_shouldAccountForMissingQuery_whenInputHasNoQuery() {
		// Act
		$sut = Uri::fromString('//example.com');

		// Assert
		$this->assertNull($sut->getScheme());
		$this->assertSame('example.com', $sut->getHost());
		$this->assertNull($sut->getQuery());
	}

	public function test_fromString_shouldAccountForEmptyFragment_whenInputHasEmptyFragment() {
		// Act
		$sut = Uri::fromString('/path#');

		// Assert
		$this->assertSame('/path', $sut->getPath());
		$this->assertSame('', $sut->getFragment());
	}

	public function test_fromString_shouldAccountForNullFragment_whenInputHasNoFragment() {
		// Act
		$sut = Uri::fromString('/path');

		// Assert
		$this->assertSame('/path', $sut->getPath());
		$this->assertNull($sut->getFragment());
	}

	public function test_fromString_shouldParseQueryArrays_whenInputUsesArraySyntax() {
		// Act
		$sut = Uri::fromString('/path?arr%5B%5D=1&arr%5B%5D=2&map%5Ba%5D=a&map%5Bb%5D%5B%5D=b1&map%5Bb%5D%5B%5D=b2');

		// Assert
		$this->assertSame('/path', $sut->getPath());
		$this->assertSame(2, \count($sut->getQuery()));
		$this->assertSame([ '1', '2' ], $sut->getQuery()['arr']);
		$this->assertSame([ 'a' => 'a', 'b' => [ 'b1', 'b2' ] ], $sut->getQuery()['map']);
	}

}
