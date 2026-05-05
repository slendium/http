<?php

namespace Slendium\HttpTests\Base;

use Exception;
use OutOfBoundsException;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Slendium\Http\Base\FieldSet;
use Slendium\Http\ReadOnlyField;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
final class FieldSetTest extends TestCase {

	public function test_offsetExists_shouldReturnExpectedResult(): void {
		// Arrange
		$sut = new FieldSet([ new ReadOnlyField('cookie', 'value=a') ]);

		// Act
		// Assert
		$this->assertTrue(isset($sut['cookie']));
		$this->assertFalse(isset($sut['accept']));
	}

	public function test_offsetGet_shouldReturnListWithExpectedResult(): void {
		$field = new ReadOnlyField('cookie', 'value=1');
		$sut = new FieldSet([ $field ]);

		$result = $sut['cookie'];

		$this->assertSame([ $field ], $result);
	}

	public static function offsetGetThrowCasesOnEmptySet(): iterable { // @phpstan-ignore missingType.iteratorValue
		yield [ null ];
		yield [ '' ];
		yield [ 'content-type' ];
	}

	#[DataProvider('offsetGetThrowCasesOnEmptySet')]
	public function test_offsetGet_shouldThrowOutOfBoundsException_whenOffsetInvalid(?string $offset): void {
		// Arrange
		$sut = new FieldSet;

		// Assert
		$this->expectException(OutOfBoundsException::class);

		// Act
		$sut[$offset];
	}

	public function test_offsetSet_shouldOnlySupportAppendOperation(): void {
		// Arrange
		$sut = new FieldSet;
		$field = new ReadOnlyField('cookie', 'value=1');

		// Act
		$sut[] = [ $field ];

		// Assert
		$this->assertSame([ $field ], $sut['cookie']);

		// Assert
		$this->expectException(Exception::class);

		// Act
		$sut['content-type'] = new ReadOnlyField('content-type', 'text/plain');
	}

	public function test_offsetUnset_shouldRemoveAllHeadersWithTheSameName(): void {
		$sut = new FieldSet([
			new ReadOnlyField('link', '<https://example.com/3>; rel="next"'),
			new ReadOnlyField('link', '<https://example.com/1>; rel="prev"')
		]);

		unset($sut['link']);

		$this->assertFalse(isset($sut['link']));
	}

	public function test_offsetUnset_shouldThrow_whenOffsetNull(): void {
		// Arrange
		$sut = new FieldSet;

		// Assert
		$this->expectException(Exception::class);

		// Act
		unset($sut[null]);
	}

	public function test_getIterator_shouldTraverseAllFields(): void {
		$sut = new FieldSet([
			new ReadOnlyField('link', '<https://example.com/3>; rel="next"'),
			new ReadOnlyField('link', '<https://example.com/1>; rel="prev"'),
			new ReadOnlyField('content-type', 'text/plain')
		]);

		$result = \count(\iterator_to_array($sut, preserve_keys: false));

		$this->assertSame(3, $result);
	}

	public function test_replace_shouldRemovePreviousFields(): void {
		$replacementLink = '<https://example.com/1>; rel="prev"';
		$sut = new FieldSet([ new ReadOnlyField('link', '<https://example.com/3>; rel="next"') ]);

		$sut->replace(new ReadOnlyField('link', $replacementLink));

		$this->assertSame($replacementLink, $sut['link'][0]->value);
	}

}
