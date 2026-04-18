<?php

namespace Slendium\HttpTests\Field;

use DateTime;
use DateTimeInterface;
use Exception;
use BcMath\Number;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Slendium\Http\Base\Field\Parameters;
use Slendium\Http\Base\Field\ReadOnlyParameterized;
use Slendium\Http\Base\Field\Rfc9651Serializer;
use Slendium\Http\Field\Item;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class Rfc9651SerializerTest extends TestCase {

	public static function validDictionaryCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ [ 'test' => 3, 'bool' => true, 'list' => [ 1, 2 ] ], 'test=3, bool, list=(1 2)' ];
		yield [ [ 'test' => new ReadOnlyParameterized(1, new Parameters([ 'param' => 1.0 ])), 'bool' => false ], 'test=1;param=1.0, bool=?0' ];
		yield [ [ 'test' => new ReadOnlyParameterized([ 1, 2 ], new Parameters([ 'ok' => true, 'other' => 1 ])) ], 'test=(1 2);ok;other=1' ];
	}

	/** @param (ArrayAccess<mixed,mixed>&Countable&Traversable<mixed,mixed>)|array<mixed> $input */
	#[DataProvider('validDictionaryCases')]
	public function test_serializeDictionary_shouldReturnExpectedValue((ArrayAccess&Countable&Traversable)|array $input, string $expectedResult): void {
		$sut = new Rfc9651Serializer;

		$result = $sut->serializeDictionary($input);

		$this->assertSame($expectedResult, $result);
	}

	public static function invalidDictionaryCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ [ ] ];
		yield [ [ 'key' => 'unsupported value' ] ];
	}

	/** @param (ArrayAccess<mixed,mixed>&Countable&Traversable<mixed,mixed>)|array<mixed> $input */
	#[DataProvider('invalidDictionaryCases')]
	public function test_serializeDictionary_shouldReturnError_whenInvokedWithInvalidValue((ArrayAccess&Countable&Traversable)|array $input): void {
		$sut = new Rfc9651Serializer;

		$result = $sut->serializeDictionary($input);

		$this->assertInstanceOf(Exception::class, $result);
	}

	public static function validListCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ [ 1 ], '1' ];
		yield [ [ 1, 2 ], '1, 2' ];
		yield [ [ new ReadOnlyParameterized(1, new Parameters([ 'test' => 1 ])), 2 ], '1;test=1, 2' ];
		yield [ [ 1, 2, [ 3, 4 ] ], '1, 2, (3 4)' ];
		yield [ [ 1, new ReadOnlyParameterized([ 2, 3 ], new Parameters([ 'param' => Item::Token('ok') ])) ], '1, (2 3);param=ok' ];
	}

	/** @param (Countable&Traversable<mixed>)|array<mixed> $input */
	#[DataProvider('validListCases')]
	public function test_serializeList_shouldReturnExpectedValue((Countable&Traversable)|array $input, string $expectedResult): void {
		$sut = new Rfc9651Serializer;

		$result = $sut->serializeList($input);

		$this->assertSame($expectedResult, $result);
	}

	public static function invalidListCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ [ ] ];
		yield [ [ 'ambiguous item' ] ];
	}

	#[DataProvider('invalidListCases')]
	public function test_serializeList_shouldReturnException_whenInvokedWithInvalidCase(iterable $input): void {
		$sut = new Rfc9651Serializer;

		$result = $sut->serializeList($input);

		$this->assertInstanceOf(Exception::class, $result);
	}

	public static function validItemCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ -999999999999999, '-999999999999999' ];
		yield [ 0, '0' ];
		yield [ 999999999999999, '999999999999999' ];
		yield [ Item::Integer(-999999999999999), '-999999999999999' ];
		yield [ Item::Integer(0), '0' ];
		yield [ Item::Integer(999999999999999), '999999999999999' ];
		yield [ new Number('-999999999999.999'), '-999999999999.999' ];
		yield [ new Number('0'), '0.0' ];
		yield [ new Number('999999999999.999'), '999999999999.999' ];
		yield [ new Number('0.12345'), '0.123' ];
		yield [ Item::Decimal(0.5), '0.5' ];
		yield [ Item::Decimal(0.0), '0.0' ];
		yield [ Item::Decimal(-0.5), '-0.5' ];
		yield [ 999999999999.999, '999999999999.999' ];
		yield [ 0.001, '0.001' ];
		yield [ -0.001, '-0.001' ];
		yield [ 0.0, '0.0' ]; // should not be 0.000
		yield [ 0.03, '0.03' ]; // should not be 0.030
		yield [ 0.0435, '0.044' ]; // rounding mode should be "half even"
		yield [ 0.0425, '0.042' ]; // rounding mode should be "half even"
		yield [ -0.0435, '-0.044' ]; // rounding mode should be "half even"
		yield [ -0.0425, '-0.042' ]; // rounding mode should be "half even"
		yield [ Item::String(''), '""' ];
		yield [ Item::String('test'), '"test"' ];
		yield [ Item::String('\\EscapeMe"'), '"\\\\EscapeMe\\""' ];
		yield [ Item::Token(''), '' ];
		yield [ Item::Token('test/abc'), 'test/abc' ];
		yield [ Item::Token('*/:okay'), '*/:okay' ];
		yield [ Item::ByteSequence("Transmit \0 NUL bytes! 😀"), ':VHJhbnNtaXQgACBOVUwgYnl0ZXMhIPCfmIA=:' ];
		yield [ true, '?1' ];
		yield [ false, '?0' ];
		yield [ Item::Boolean(true), '?1' ];
		yield [ Item::Boolean(false), '?0' ];
		yield [ new DateTime('@-123'), '@-123' ];
		yield [ new DateTime('@0'), '@0' ];
		yield [ new DateTime('@1776407218'), '@1776407218' ];
		yield [ Item::Date(new DateTime('@0')), '@0' ];
		yield [ Item::DisplayString(''), '%""' ];
		yield [ Item::DisplayString('Ûnicode! 😀'), '%"%c3%9bnicode! %f0%9f%98%80"' ];
		yield [ new ReadOnlyParameterized(1, new Parameters([ 'answer' => 67 ])), '1;answer=67' ];
		yield [ new ReadOnlyParameterized(1.0, new Parameters([ 'answer' => true ])), '1.0;answer' ];
		yield [ new ReadOnlyParameterized(true, new Parameters([ 'answer' => false ])), '?1;answer=?0' ];
	}

	#[DataProvider('validItemCases')]
	public function test_serializeItem_shouldReturnExpectedValue_whenInvokedWithValidInput(mixed $input, string $expectedResult): void {
		$sut = new Rfc9651Serializer;

		$result = $sut->serializeItem($input);

		$this->assertSame($expectedResult, $result);
	}

	public static function invalidItemCases(): iterable { // @phpstan-ignore missingType.iterableValue
		yield [ null ];
		yield [ '' ];
		yield [ 'any-string-is-ambiguous-and-therefore-disallowed' ];
		yield [ -9999999999999999 ];
		yield [ 9999999999999999 ];
		yield [ new Number('-10000000000000000.0') ];
		yield [ new Number('10000000000000000.0') ];
		yield [ -10000000000000000.0 ];
		yield [ 10000000000000000.0 ];
		yield [ Item::String("ok\0") ];
		yield [ Item::String("ok\x1F") ];
		yield [ Item::String("ok\x7F") ];
		yield [ Item::String("ok\xFF") ];
		yield [ Item::Token('1invalid') ];
		yield [ Item::Token("invalid\0") ];
		yield [ Item::Token('invalid"') ];
		yield [ Item::DisplayString("\xf0\x28\x8c\xbc") ]; // invalid unicode, should fail at second octet
		yield [ Item::DisplayString("\xf8\xa1\xa1\xa1\xa1") ]; // valid sequence but not unicode
		yield [ new ReadOnlyParameterized(1, new Parameters([ '1invalid' => 25 ])) ];
		yield [ new ReadOnlyParameterized(true, new Parameters([ '' => 'hi' ])) ];
		yield [ new ReadOnlyParameterized(1.0, new Parameters([ 'invalid!' => 'ok' ])) ];
	}

	#[DataProvider('invalidItemCases')]
	public function test_serializeItem_shouldReturnException_whenInvokedWithInvalidInput(mixed $input): void {
		$sut = new Rfc9651Serializer;

		$result = $sut->serializeItem($input);

		$this->assertInstanceOf(Exception::class, $result);
	}

}
