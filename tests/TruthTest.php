<?php

namespace PhpTruth;

class TruthTest {

	public function testContains() {
		assertThat('string')->contains('ring');
		assertThat(function() {
			assertThat('string')->contains('ruf');
		})->throwsException(\PhpTruth\AssertionFailure::class);

		assertThat(['a'])->contains('a');
		assertThat(function() {
			assertThat(['a'])->contains('b');
		})->throwsException(\PhpTruth\AssertionFailure::class);
	}

	public function testContainsAllOf() {
		assertThat(['a', 'b', 'c'])->containsAllOf('a', 'c');
		assertThat(function() {
			assertThat(['a', 'b', 'c'])->containsAllOf('a', 'd');
		})->throwsException(\PhpTruth\AssertionFailure::class);
	}

	/** @dataProvider objectProvider */
	public function testContainsEntry($input) {
		assertThat($input)->containsEntry('abc', 123);
		assertThat(function() use ($input) {
			assertThat($input)->containsEntry('abc', 321);
		})->throwsException(\PhpTruth\AssertionFailure::class);
	}

	/** @dataProvider objectProvider */
	public function testContainsKey($input) {
		assertThat($input)->containsKey('abc');
		assertThat(function() use ($input) {
			assertThat($input)->containsKey('asd');
		})->throwsException(\PhpTruth\AssertionFailure::class);
	}

	public function testHasLength() {
		assertThat('abc')->hasLength(3);
		assertThat('')->hasLength(0);
		assertThat(123)->hasLength(3);
	}

	public function testHasSize() {
		assertThat([])->hasSize(0);
		assertThat(['a'])->hasSize(1);
		assertThat(['a', 'b'])->hasSize(2);
		assertThat(['a', 'b' => [1, 2, 3]])->hasSize(2);

		assertThat(function() {
			assertThat(['abc'])->hasSize(3);
		})->throwsException(\PhpTruth\AssertionFailure::class);
	}

	public function testIsAtLeast() {
		assertThat(3)->isAtLeast(3);
		assertThat(99)->isAtLeast(3);
		assertThat(3.3)->isAtLeast(3);
		assertThat(function() {
			assertThat(2)->isAtLeast(3);
		})->throwsException(\PhpTruth\AssertionFailure::class);
	}

	public function testIsAtMost() {
		assertThat(24)->isAtMost(50);
		assertThat(50)->isAtMost(50);
		assertThat(3.5)->isAtMost(50);
		assertThat(function() {
			assertThat(99)->isAtMost(50);
		})->throwsException(\PhpTruth\AssertionFailure::class);

		assertThat(24)->isNot()->isAtMost(2);
	}

	/** @dataProvider emptyProvider */
	public function testIsEmpty($empty, $notEmpty) {
		assertThat($empty)->isEmpty();
		assertThat(function() use ($notEmpty) {
			assertThat($notEmpty)->isEmpty();
		})->throwsException(\PhpTruth\AssertionFailure::class);
	}

	public function testIsEqualTo() {
		$a = [0, '', false, null, 0.0];
		foreach ($a as $a1) {
			foreach ($a as $a2) {
				assertThat($a1)->isEqualTo($a2);
			}
		}
		assertThat(1)->isEqualTo(1.0);

		assertThat(function() {
			assertThat(1.5)->isEqualTo(2);
		})->throwsException(\PhpTruth\AssertionFailure::class);
	}

	public function testIsIdenticalTo() {
		assertThat('abc')->isIdenticalTo('abc');
		assertThat('123')->isIdenticalTo('123');
		assertThat(function() {
			assertThat(123)->isIdenticalTo('123');
		})->throwsException(\PhpTruth\AssertionFailure::class);
	}

	public function testIsFalsy() {
		$a = [0, '', false, null, 0.0];
		foreach ($a as $a1) {
			assertThat($a1)->isFalsy();
		}

		$b = [1, '1', true, 1.0, -1];
		foreach ($b as $b1) {
			assertThat(function() use ($b1) {
				assertThat($b1)->isFalsy();
			})->throwsException(\PhpTruth\AssertionFailure::class);
		}
	}

	public function testIsGreaterThan() {
		assertThat(0)->isGreaterThan(-1);
		assertThat(5.5)->isGreaterThan(5);
		assertThat(-5)->isGreaterThan(-10);

		assertThat(function() {
			assertThat(123)->isGreaterThan(321);
		})->throwsException(\PhpTruth\AssertionFailure::class);
	}

	public function testIsLessThan() {
		assertThat(-1)->isLessThan(0);
		assertThat(5)->isLessThan(5.5);
		assertThat(-10)->isLessThan(-5);

		assertThat(function() {
			assertThat(321)->isLessThan(123);
		})->throwsException(\PhpTruth\AssertionFailure::class);
	}

	public function testIsNotEqualTo() {
		assertThat(1)->isNotEqualTo(-1);
		assertThat('abc')->isNotEqualTo('xyz');
		assertThat([])->isNotEqualTo((object)[]);
		assertThat(['abc'])->isNotEqualTo(['xyz']);

		assertThat(function() {
			assertThat('abc')->isNotEqualTo('abc');
		})->throwsException(\PhpTruth\AssertionFailure::class);
	}

	public function testIsTruthy() {
		$a = [1, '1', true, 1.0, -1];
		foreach ($a as $a1) {
			assertThat($a1)->isTruthy();
		}

		$b = [0, '', false, null, 0.0];
		foreach ($b as $b1) {
			assertThat(function() use ($b1) {
				assertThat($b1)->isTruthy();
			})->throwsException(\PhpTruth\AssertionFailure::class);
		}
	}

	public function testThrowsException() {
		// Thrown
		assertThat(function() {
			throw new \InvalidArgumentException();
		})->throwsException();

		// None thrown
		assertThat(function() {
			assertThat(function() {
				// do nothing.
			})->throwsException();
		})->throwsException(\PhpTruth\AssertionFailure::class);

		// Wrong sub-type thrown
		assertThat(function() {
			assertThat(function() {
				throw new \Exception();
			})->throwsException(\InvalidArgumentException::class);
		})->throwsException(\PhpTruth\AssertionFailure::class);
	}

	public function emptyProvider() {
		return [
			[[], ['a']],
			[(object)[], (object)['a']],
			['', 'abc'],
			[0, 123],
			[false, true],
			[null, new self()],
		];
	}

	public function objectProvider() {
		return [
			[['abc' => 123]],
			[(object)['abc' => 123]],
		];
	}
}
