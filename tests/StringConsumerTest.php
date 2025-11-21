<?php

namespace Slendium\HttpTests;

use Throwable;

use PHPUnit\Framework\TestCase;

use Slendium\Http\Base\{
	ParseException,
	StringConsumer,
};

final class StringConsumerTest extends TestCase {

	public function test_hasMore_shouldBeTrue_whenInputNotEmpty() {
		// Arrange
		$sut = new StringConsumer('test');

		// Act
		$sut->discard(1);

		// Assert
		$this->assertTrue($sut->hasMore);
	}

	public function test_hasMore_shouldBeFalse_whenInputEmptied() {
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

	public function test_consume_shouldReturn_whenInputNotEmpty() {
		// Arrange
		$sut = new StringConsumer('test');

		// Act
		$result = $sut->consume(1);

		// Assert
		$this->assertSame('t', $result);
		$this->assertTrue($sut->hasMore);
	}

	public function test_consume_shouldThrow_whenInputEmpty() {
		// Arrange
		$sut = new StringConsumer('');

		// Assert
		$this->expectException(ParseException::class);

		// Act
		$sut->consume(1);
	}

	public function test_consume_shouldThrow_whenLengthExceeded() {
		// Arrange
		$sut = new StringConsumer('test');

		// Assert
		$this->expectException(ParseException::class);

		// Act
		$sut->consume(2);
		$sut->consume(3);
	}

	public function test_consume_shouldBeSequential_whenCalledOneByOne() {
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

	public function test_discard_shouldNotThrow_whenStringHasMore() {
		// Arrange
		$sut = new StringConsumer('test');

		// Act
		while ($sut->hasMore) {
			$sut->discard(1);
		}

		// Assert
		$this->expectNotToPerformAssertions();
	}

	public function test_discard_shouldThrow_whenStringHasNoMore() {
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

	public function test_discardSpaces_shouldDiscardSpacesAtStart() {
		// Arrange
		$sut = new StringConsumer('   test');

		// Act
		$sut->discardSpaces();

		// Assert
		$this->assertSame('t', $sut->peek(1));
	}

	public function test_peek_shouldReturnString_whenCharsRemaining() {
		// Arrange
		$sut = new StringConsumer('test');

		// Act
		$result = $sut->peek(1);

		// Assert
		$this->assertSame('t', $result);
	}

	public function test_peek_shouldReturnString_withMultipleChars() {
		// Arrange
		$sut = new StringConsumer('test');

		// Act
		$result = $sut->peek(2);

		// Assert
		$this->assertSame('te', $result);
	}

	public function test_peek_shouldReturnEmpty_whenStringEmptied() {
		// Arrange
		$sut = new StringConsumer('test');

		// Act
		$sut->discard(4);
		$result = $sut->peek(1);

		// Assert
		$this->assertSame('', $result);
	}

	public function test_peek_shouldMatchConsume_whileStringHasMore() {
		// Arrange
		$sut = new StringConsumer("abc'\n\t\r:*~XYZ\0".\chr(0x02));

		while ($sut->hasMore) {
			// Act
			$result = $sut->peek(1);

			// Assert
			$this->assertSame($sut->consume(1), $result);
		}
	}

	public function test_peekEquals_shouldReturnTrue_whenAnyCharMatches() {
		// Arrange
		$sut = new StringConsumer('test');

		// Act
		$result = $sut->peekEquals('x', 'y', 'z', 't');

		// Assert
		$this->assertTrue($result);
	}

	public function test_peekEquals_shouldReturnFalse_whenNoCharMatches() {
		// Arrange
		$sut = new StringConsumer('test');

		// Act
		$result = $sut->peekEquals('x', 'u', 'p');

		// Assert
		$this->assertFalse($result);
	}

	public function test_rewind_shouldNotThrow_whenNotAtTheStart() {
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

	public function test_rewind_shouldThrow_whenGoingBeyondTheStart() {
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
