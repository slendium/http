<?php

namespace Slendium\HttpTests;

use Throwable;

use PHPUnit\Framework\TestCase;

use Slendium\Http\Base\ParseException;
use Slendium\Http\Base\StringConsumer;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2025-2026
 */
final class StringConsumerTest extends TestCase {

	public function test_hasMore_shouldBeTrue_whenInputNotEmpty(): void {
		$sut = new StringConsumer('test');

		$sut->discard(1);

		$this->assertTrue($sut->hasMore);
	}

	public function test_hasMore_shouldBeFalse_whenInputEmptied(): void {
		// Arrange
		$sut = new StringConsumer('test');

		// Act
		$sut->discard(3);

		// Assert
		$this->assertTrue($sut->hasMore);

		// Act
		$sut->discard(1);

		// Assert
		$this->assertFalse($sut->hasMore);
	}

	public function test_consume_shouldReturn_whenInputNotEmpty(): void {
		$sut = new StringConsumer('test');

		$result = $sut->consume(1);

		$this->assertSame('t', $result);
		$this->assertTrue($sut->hasMore);
	}

	public function test_consume_shouldThrow_whenInputEmpty(): void {
		// Arrange
		$sut = new StringConsumer('');

		// Assert
		$this->expectException(ParseException::class);

		// Act
		(void)$sut->consume(1);
	}

	public function test_consume_shouldThrow_whenLengthExceeded(): void {
		// Arrange
		$sut = new StringConsumer('test');

		// Assert
		$this->expectException(ParseException::class);

		// Act
		(void)$sut->consume(2);
		(void)$sut->consume(3);
	}

	public function test_consume_shouldBeSequential_whenCalledOneByOne(): void {
		// Arrange
		$sut = new StringConsumer('12345');

		for ($i = 1; $i <= 5; $i += 1) {
			// Assert
			$this->assertTrue($sut->hasMore);
			// Act
			$result = $sut->consume(1);
			// Assert
			$this->assertSame("$i", $result);
		}

		// Assert
		$this->assertFalse($sut->hasMore);
	}

	public function test_discard_shouldNotThrow_whenStringHasMore(): void {
		$sut = new StringConsumer('test');

		while ($sut->hasMore) {
			$sut->discard(1);
		}

		$this->expectNotToPerformAssertions();
	}

	public function test_discard_shouldThrow_whenStringHasNoMore(): void {
		// Arrange
		$sut = new StringConsumer('test');

		// Act
		$sut->discard(4);

		// Assert
		$this->assertFalse($sut->hasMore);
		$this->expectException(ParseException::class);

		// Act
		$sut->discard(1);
	}

	public function test_discardSpaces_shouldDiscardSpacesAtStart(): void {
		$sut = new StringConsumer('   test');

		$sut->discardSpaces();

		$this->assertSame('t', $sut->peek(1));
	}

	public function test_peek_shouldReturnString_whenCharsRemaining(): void {
		$sut = new StringConsumer('test');

		$result = $sut->peek(1);

		$this->assertSame('t', $result);
	}

	public function test_peek_shouldReturnString_withMultipleChars(): void {
		$sut = new StringConsumer('test');

		$result = $sut->peek(2);

		$this->assertSame('te', $result);
	}

	public function test_peek_shouldReturnEmpty_whenStringEmptied(): void {
		$sut = new StringConsumer('test');

		$sut->discard(4);
		$result = $sut->peek(1);

		$this->assertSame('', $result);
	}

	public function test_peek_shouldMatchConsume_whileStringHasMore(): void {
		// Arrange
		$sut = new StringConsumer("abc'\n\t\r:*~XYZ\0".\chr(0x02));

		while ($sut->hasMore) {
			// Act
			$result = $sut->peek(1);

			// Assert
			$this->assertSame($sut->consume(1), $result);
		}
	}

	public function test_peekEquals_shouldReturnTrue_whenAnyCharMatches(): void {
		$sut = new StringConsumer('test');

		$result = $sut->peekEquals('x', 'y', 'z', 't');

		$this->assertTrue($result);
	}

	public function test_peekEquals_shouldReturnFalse_whenNoCharMatches(): void {
		$sut = new StringConsumer('test');

		$result = $sut->peekEquals('x', 'u', 'p');

		$this->assertFalse($result);
	}

	public function test_expect_throws_whenNoCharMatches(): void {
		// Arrange
		$sut = new StringConsumer('test');

		// Assert
		$this->expectException(ParseException::class);

		// Act
		$sut->expect([ 'x', 'y', 'z' ], 'error message');
	}

	public function test_rewind_shouldNotThrow_whenNotAtTheStart(): void {
		// Arrange
		$sut = new StringConsumer('test');
		$sut->discard(2);

		// Act
		$sut->rewind(1);

		// Assert
		$this->assertSame('e', $sut->peek(1));

		// Act
		$sut->rewind(1);

		// Assert
		$this->assertSame('t', $sut->peek(1));
	}

	public function test_rewind_shouldThrow_whenGoingBeyondTheStart(): void {
		// Arrange
		$sut = new StringConsumer('test');
		$sut->discard(2);
		$sut->rewind(2);

		// Assert
		$this->expectException(Throwable::class);

		// Act
		$sut->rewind(1);
	}

}
