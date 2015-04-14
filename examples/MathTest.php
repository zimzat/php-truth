<?php

class MathTest {

	public function testObject() {
		assertThat(['a'])->contains('a');
	}

//	public function testMeaningOfLife() {
//		test()->isIncomplete('42');
//	}

//	public function testInfinity() {
//		test()->isSkipped('Incalculable');
//	}

	public function testRound() {
		assertThat(round(pi()))->isEqualTo(3);
	}

	public function testCeil() {
		assertThat(ceil(3.0001))->isEqualTo(4);
	}

	public function testFloor() {
		assertThat(floor(3.999))->isEqualTo(3);
	}

	/**
	 * @dataProvider provideAddition
	 */
	public function testAdd($a, $b, $c) {
		assertThat($a + $b)->isEqualTo($c);
	}

	/**
	 * @dataProvider provideDivision
	 */
	public function testDiv($a, $b, $c) {
		assertThat($a / $b)->isEqualTo($c);
	}

	public function provideAddition() {
		return [
			[1, 1, 2],
			[2, 2, 4],
			[-4, 4, 0],
			[-2, 1, -1],
			[2.5, 5.0, 7.5],
		];
	}

	public function provideDivision() {
		return [
			[1, 1, 1],
			[3, 3, 1],
			[6, 3, 2],
			[1, 2, 0.5],
		];
	}

	public function testMin() {
		assertThat(min(-1, 0, 4, -2))->isEqualTo(-2);
	}

	public function testMax() {
		assertThat(max(-1, 0, 4, -2))->isEqualTo(4);
	}
}
