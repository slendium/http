<?php

namespace Slendium\HttpTests\Content;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\Http\Base\ParseException;
use Slendium\Http\Content\ReadOnlyMediaType;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class MediaTypeTest extends TestCase {

	public function test_fromString_shouldRecognizeFacetAndSyntax(): void {
		$sut = ReadOnlyMediaType::fromString('image/x.svg+xml');

		$this->assertSame('image', $sut->major->name);
		$this->assertNull($sut->major->facet);
		$this->assertNull($sut->major->syntax);
		$this->assertSame('svg', $sut->minor->name);
		$this->assertSame('x', $sut->minor->facet);
		$this->assertSame('xml', $sut->minor->syntax);
	}

	public function test_fromString_shouldRecognizePlainTypes(): void {
		$sut = ReadOnlyMediaType::fromString('application/json');

		$this->assertSame('application', $sut->major->name);
		$this->assertSame('json', $sut->minor->name);
	}

	public static function invalidCases(): iterable { // @phpstan-ignore missingType.iterableValue
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
	public function test_fromString_shouldReturnException_whenInputInvalid(string $input): void {
		$result = ReadOnlyMediaType::fromString($input);

		$this->assertInstanceOf(ParseException::class, $result);
	}

	public function test___toString_shouldAddSlash(): void {
		$sut = ReadOnlyMediaType::fromNames('text', 'plain');

		$result = (string)$sut;

		$this->assertSame('text/plain', $result);
	}

	public function test___toString_shouldIncludeFacetAndSyntax(): void {
		$sut = ReadOnlyMediaType::fromString('image/x.svg+xml');

		$result = (string)$sut;

		$this->assertSame('image/x.svg+xml', $result);
	}

}
