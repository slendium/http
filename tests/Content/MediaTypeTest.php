<?php

namespace Slendium\HttpTests\Content;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\Http\Base\ParseException;
use Slendium\Http\Base\Content\MediaType;

class MediaTypeTest extends TestCase {

	public function test_fromString_shouldRecognizeFacetAndSyntax() {
		// Act
		$sut = MediaType::fromString('image/x.svg+xml');

		// Assert
		$this->assertSame('image', $sut->major->name);
		$this->assertNull($sut->major->facet);
		$this->assertNull($sut->major->syntax);
		$this->assertSame('svg', $sut->minor->name);
		$this->assertSame('x', $sut->minor->facet);
		$this->assertSame('xml', $sut->minor->syntax);
	}

	public function test_fromString_shouldRecognizePlainTypes() {
		// Act
		$sut = MediaType::fromString('application/json');

		// Assert
		$this->assertSame('application', $sut->major->name);
		$this->assertSame('json', $sut->minor->name);
	}

	public static function invalidCases(): iterable {
		yield [ '' ];
		yield [ 'image' ];
		yield [ 'image/' ];
		yield [ 'image/facet+overlaps.syntax' ];
		yield [ 'image/facet.+follows-syntax' ];
		yield [ '/minor-only' ];
		yield [ 'text/plain:illegal-char' ];
		yield [ 'text/_illegal-first-char' ];
		yield [ 'text/.empty-facet' ];
		yield [ 'text/empty-suffix+' ];
	}

	#[DataProvider('invalidCases')]
	public function test_fromString_shouldThrow_whenInputInvalid(string $input) {
		// Assert
		$this->expectException(ParseException::class);

		// Act
		MediaType::fromString($input);
	}

	#[DataProvider('invalidCases')]
	public function test_tryFromString_shouldReturnNull_whenInputInvalid(string $input) {
		// Act
		$result = MediaType::tryFromString($input);

		// Assert
		$this->assertNull($result);
	}

	public function test___toString_shouldAddSlash() {
		// Arrange
		$sut = MediaType::fromNames('text', 'plain');

		// Act
		$result = (string)$sut;

		// Assert
		$this->assertSame('text/plain', $result);
	}

	public function test___toString_shouldIncludeFacetAndSyntax() {
		// Arrange
		$sut = MediaType::fromString('image/x.svg+xml');

		// Act
		$result = (string)$sut;

		// Assert
		$this->assertSame('image/x.svg+xml', $result);
	}

}
