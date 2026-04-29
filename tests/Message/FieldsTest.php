<?php

namespace Slendium\HttpTests\Message;

use PHPUnit\Framework\TestCase;

use Slendium\Http\Base\FieldSet;
use Slendium\Http\Base\Field as ReadOnlyField;
use Slendium\Http\Message\Fields;

/**
 * @internal
 * @author C. Fahner
 * @copyright Slendium 2026
 */
class FieldsTest extends TestCase {

	public function test_contains_shouldReturnExpectedResult(): void {
		$fields = new FieldSet([ new ReadOnlyField('content-type', 'text/plain') ]);

		$trueResult = Fields::contains($fields, 'content-type');
		$falseResult = Fields::contains($fields, 'link');

		$this->assertTrue($trueResult);
		$this->assertFalse($falseResult);
	}

	public function test_getFirst_shouldReturnExpectedResult(): void {
		$firstContentType = 'text/plain';
		$fields = new FieldSet([
			new ReadOnlyField('content-type', $firstContentType),
			new ReadOnlyField('content-type', 'text/html')
		]);

		$contentTypeResult = Fields::getFirst($fields, 'content-type')->value;
		$nullResult = Fields::getFirst($fields, 'accept');

		$this->assertSame($firstContentType, $contentTypeResult);
		$this->assertNull($nullResult);
	}

	public function test_getAll_shouldReturnExpectedResult_whenFieldExists(): void {
		$fields = new FieldSet([
			new ReadOnlyField('content-type', 'text/plain'),
			new ReadOnlyField('content-type', 'text/html')
		]);

		$result = [ ];
		foreach (Fields::getAll($fields, 'content-type') as $field) {
			$result[] = $field->value;
		}

		$this->assertSame([ 'text/plain', 'text/html' ], $result);
	}

	public function test_getAll_shouldReturnEmptyIterable_whenFieldDoesNotExist(): void {
		$fields = new FieldSet([
			new ReadOnlyField('content-type', 'text/plain'),
			new ReadOnlyField('content-type', 'text/html')
		]);

		$result = [ ];
		foreach (Fields::getAll($fields, 'accept') as $field) {
			$result[] = $field->value;
		}

		$this->assertSame([ ], $result);
	}

}
