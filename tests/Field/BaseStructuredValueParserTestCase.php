<?php

namespace Slendium\HttpTests\Field;

use Exception,
	Traversable;

use PHPUnit\Framework\TestCase;

use Slendium\Http\Field\{
	Item,
	Parameterized,
};

abstract class BaseStructuredValueParserTestCase extends TestCase {

	const DIGIT = '0123456789';

	const ALPHA_PLUS_ASTERISK = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ*';

	/** @param Traversable<Parameterized<Item|Traversable<mixed>>> $list */
	protected static function asArrayWithoutParameters(Traversable $list): array {
		$out = [ ];
		foreach ($list as $key => $member) {
			if (!($member instanceof Parameterized)) {
				throw new Exception('Given list contained something other than a Parameterized');
			}
			if ($member->value instanceof Traversable) {
				$out[$key] = self::asArrayWithoutParameters($member->value);
			} else if ($member->value instanceof Item) {
				$out[$key] = $member->value->value;
			} else {
				throw new Exception('Given list contained something other than Traversable|Item');
			}
		}
		return $out;
	}

	// in subclasses, please stick to the order of RFC 9651: list, dictionary, integer, decimal,
	// string, token, byte sequence, boolean, date, display string

	/** Use with `self::asArrayWithoutParameters()` to get to the expected output format */
	public static function validListCases(): iterable {
		yield [ '1, 2, 3', [ 1, 2, 3 ] ];
		yield [ '1.0, 2.0, 3.0', [ 1.0, 2.0, 3.0 ] ];
		yield [ '"list", "of", "strings"', [ 'list', 'of', 'strings' ] ];
		yield [ 'list, of, tokens', [ 'list', 'of', 'tokens' ] ];
		yield [ '::, :UmFuZG9tIGJ5dGVzIPCfmILwn5iC8J+Ygg==:', [ '', 'Random bytes 😂😂😂' ] ];
		yield [ '?0, ?0, ?1', [ false, false, true ] ];
		yield [ '@-62135596800, @0, @253402214400', [ -62135596800, 0, 253402214400 ] ];
		yield [ '%"", %"%F0%9F%98%82%F0%9F%98%82"', [ '', '😂😂' ] ];
		yield [ '1, 2.0, "mixed", list, ::, ?1, @0, %""', [ 1, 2.0, 'mixed', 'list', '', true, 0, '' ] ];
		yield [ '()', [ [ ] ] ];
		yield [ '(),()', [ [ ], [ ] ] ];
		yield [ ' (  ),   (    )', [ [ ], [ ] ] ];
		yield [ '1, 2, (1 2)', [ 1, 2, [ 1, 2 ] ] ];
		yield [ '?1, (), (1 1.2 ?1 "string" token ::)', [ true, [ ], [ 1, 1.2, true, 'string', 'token', '' ] ] ];
	}

	/** Use with `self::asArrayWithoutParameters()` to get to the expected output format */
	public static function validDictionaryCases(): iterable {
		yield [ 'en="Test", nl="Test"', [ 'en' => 'Test', 'nl' => 'Test' ] ];
		yield [ 'a, b, c', [ 'a' => true, 'b' => true, 'c' => true ] ];
		yield [ 'a=(token1 token2)', [ 'a' => [ 'token1', 'token2' ] ] ];
	}

	public static function validIntegerCases(): iterable {
		for ($i = 0; $i < \strlen(self::DIGIT); $i += 1) {
			yield [ '-'.self::DIGIT[$i], -1*((int)self::DIGIT[$i]) ];
			yield [ self::DIGIT[$i], (int)self::DIGIT[$i] ];
		}
	}

	public static function validDecimalCases(): iterable {
		for ($i = 0; $i < \strlen(self::DIGIT); $i += 1) {
			yield [ '-'.self::DIGIT[$i].'.0', -1*((float)self::DIGIT[$i]) ];
			yield [ self::DIGIT[$i].'.0', (float)self::DIGIT[$i] ];
		}
		yield [ '1.123', 1.123 ]; // max 3 decimals
		yield [ '-123456789012.345', -123456789012.345 ]; // sign should not count towards the char limit
	}

	public static function validStringCases(): iterable {
		yield [ '""', '' ];
		yield [ '"valid string"', 'valid string' ];
		yield [ '"1234.5678 / (QWERTY)"', '1234.5678 / (QWERTY)' ];
		yield [ '"string with \\\\ backslash"', 'string with \\ backslash' ];
		yield [ '"string with \\" quote"', 'string with " quote' ];
	}

	public static function validTokenCases(): iterable {
		for ($i = 0; $i < \strlen(self::ALPHA_PLUS_ASTERISK); $i += 1) {
			yield [ self::ALPHA_PLUS_ASTERISK[$i].'est/:test1', self::ALPHA_PLUS_ASTERISK[$i].'est/:test1' ];
		}
	}

	public static function validByteSequenceCases(): iterable {
		yield [ ':UmFuZG9tIGJ5dGVzIPCfmILwn5iC8J+Ygg==:', 'Random bytes 😂😂😂' ];
		yield [ '::', '' ];
	}

	public static function validBooleanCases(): iterable {
		yield [ '?1', true ];
		yield [ '?0', false ];
	}

	public static function validDateCases(): iterable {
		yield [ '@-62135596800', -62_135_596_800 ]; // lowest valid value
		yield [ '@0', 0 ];
		yield [ '@1762451842', 1_762_451_842 ]; // time of writing
		yield [ '@253402214400', 253_402_214_400 ]; // higest valid value
	}

	public static function validDisplayStringCases(): iterable {
		yield [ '%""', '' ];
		yield [ '%"Testing %F0%9F%98%82%F0%9F%98%82"', 'Testing 😂😂' ];
	}

	public static function validItemCases(): iterable {
		foreach (self::validIntegerCases() as $case) {
			yield [ ...$case, Item\Integer::class ];
		}
		foreach (self::validDecimalCases() as $case) {
			yield [ ...$case, Item\Decimal::class ];
		}
		foreach (self::validStringCases() as $case) {
			yield [ ...$case, Item\String_::class ];
		}
		foreach (self::validTokenCases() as $case) {
			yield [ ...$case, Item\Token::class ];
		}
		foreach (self::validByteSequenceCases() as $case) {
			yield [ ...$case, Item\ByteSequence::class ];
		}
		foreach (self::validDateCases() as $case) {
			yield [ ...$case, Item\Date::class ];
		}
		foreach (self::validDisplayStringCases() as $case) {
			yield [ ...$case, Item\DisplayString::class ];
		}
	}

}
