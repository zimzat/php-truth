<?php

/**
 * @param mixed $subject
 * @return \PhpTruth\Assert
 */
function assertThat($subject) {
	return new \PhpTruth\Assert($subject);
}

/**
 * @return \PhpTruth\Test
 */
function test() {
	return new \PhpTruth\Test();
}
