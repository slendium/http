<?php

namespace Slendium\HttpTests\Field;

use ArrayAccess;
use Countable;
use DateTimeInterface;
use Traversable;

use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\Http\Field\Parameterized;
use Slendium\Http\Base\ParseException;
use Slendium\Http\Base\Field\Rfc9651Parser;

class Rfc9651ParserTest extends BaseStructuredValueParserTestCase {

	#[DataProvider('validListCases')]
	public function test_parseList_shouldReturnExpectedRawValues(string $input, array $expectedValues) {
		// Arrange
		$sut = new Rfc9651Parser;

		// Act
		$result = self::asArrayWithoutParameters($sut->parseList($input));
		$result = \array_map(
			static fn($value) => $value instanceof DateTimeInterface
				? $value->getTimestamp()
				: $value,
			$result
		);

		// Assert
		$this->assertSame($expectedValues, $result);
	}

	public function test_parseList_shouldContainParameters_whenInvokedWithParameters() {
		// Arrange
		$input = 'token;a=1;b=2;c, (1 2 3); q="9"';
		$sut = new Rfc9651Parser;

		// Act
		$result = \iterator_to_array($sut->parseList($input));

		// Assert
		foreach ($result as $tuple) {
			$this->assertInstanceOf(Parameterized::class, $tuple);
		}
		// should be the same instances
		$this->assertSame($result[0]->parameters[0], $result[0]->parameters['a']);
		$this->assertSame($result[0]->parameters[1], $result[0]->parameters['b']);
		$this->assertSame($result[0]->parameters[2], $result[0]->parameters['c']);
		$this->assertSame($result[1]->parameters[0], $result[1]->parameters['q']);
	}

	#[DataProvider('validDictionaryCases')]
	public function test_parseDictionary_shouldReturnExpectedAssoc(string $input, array $expectedAssoc) {
		// Arrange
		$sut = new Rfc9651Parser;

		// Act
		$result = self::asArrayWithoutParameters($sut->parseDictionary($input));

		// Assert
		$this->assertSame($expectedAssoc, $result);
	}

	#[DataProvider('validItemCases')]
	public function test_parseItem_shouldReturnExpectedType(string $input, mixed $expectedValue, string $expectedType) {
		// Arrange
		$sut = new Rfc9651Parser;

		// Act
		$result = $sut->parseItem($input)->value;
		$resultValue = $result->value instanceof DateTimeInterface
			? $result->value->getTimestamp()
			: $result->value;

		// Assert
		$this->assertInstanceOf($expectedType, $result);
		$this->assertSame($expectedValue, $resultValue);
	}

	public static function invalidItemCases(): iterable {
		yield [ '2 1' ];
		yield [ '1234567890123456' ];
		yield [ '1.' ];
		yield [ '1234567890123.456' ];
		yield [ '1234567890.1234' ];
		yield [ '-1234567890123.456' ];
		yield [ 'string "' ];
		yield [ '" string' ];
		yield [ '"valid string with \\ incomplete escape in the middle"' ];
		yield [ '"valid string with incomplete escape at the end\\"' ];
		yield [ '"'.\chr(0x02).'"' ];
		yield [ '"'.\chr(0xff).'"' ];
		yield [ ':UmFuZG9tIGJ5dGVzIP*fmILwn5iC8J+Ygg==:' ]; // contains non base64 char
		yield [ ':UmFuZG9tIGJ5dGVzIPCfmILwn5iC8J+Ygg==' ]; // no ending ':'
		yield [ ':' ]; // no chars, no ending
		yield [ '?' ];
		yield [ '?2' ];
		yield [ '@' ];
		yield [ '@1.0' ];
		yield [ '@.01' ];
		yield [ '@-1.0' ];
		yield [ '@1.' ];
		yield [ '%' ];
		yield [ '%"' ]; // empty, never closed
		yield [ '%"%f"' ]; // incomplete percent encoded char
		yield [ '%"%F0%9F%98%82' ]; // valid, never closed
		yield [ "%\"\x02\"" ]; // control characters not allowed
		yield [ "%\"\xff\"" ]; // char outside of vchar+sp range
		yield [ '%"%xy"' ]; // invalid chars in percent encoding
		yield [ '%"%f0%28%8c%bc"' ]; // invalid unicode, should fail at second octet
		yield [ '%"%f8%a1%a1%a1%a1"' ]; // valid sequence but not unicode
		yield [ '[unknown/type]' ];
	}

	#[DataProvider('invalidItemCases')]
	public function test_parseItem_shouldThrow_whenInvokedWithInvalidType(string $input) {
		// Arrange
		$sut = new Rfc9651Parser;

		// Assert
		$this->expectException(ParseException::class);

		// Act
		$sut->parseItem($input);
	}

}
