<?php

namespace PhpTruth;

/**
 * 
 */
class Assert {
	/** How many assertions have been made. */
	public static $assertionCount = 0;

	/** What are we testing */
	protected $subject;

	/** What the subject is named. Can be overridden for more detail. */
	protected $name;

	/** Whether the assertion check should be true or false */
	protected $expectedResult = true;

	/** Have any assertions failed? */
	protected $failure = false;

	/** What message should we reported once we're done evaluating */
	protected $failureMessage = '';

	/** @param mixed $subject */
	public function __construct($subject) {
		$this->subject = $subject;
		if (is_object($subject)) {
			$this->name = get_class($subject);
		} else {
			$this->name = gettype($subject);
		}
	}

	/**
	 * Set a custom name for the assertion subject.
	 * @param string $name
	 * @return \Truth\Assert
	 */
	public function named($name) {
		$this->name = $name;
		return $this;
	}

	/** Empty array, stdClass, string, false, 0, null */
	public function isEmpty() {
		if ($this->subject instanceof \stdClass) {
			foreach ($this->subject as $value) {
				$this->logResult(false, __FUNCTION__, 'empty');
			}
		} else {
			$this->logResult(empty($this->subject), __FUNCTION__, 'empty');
		}
		return $this;
	}

	/** Count of array. */
	public function hasSize($expectedValue) {
		$this->logResult(count($this->subject) === $expectedValue, __FUNCTION__, $expectedValue);
		return $this;
	}

	/** Size of a string */
	public function hasLength($expectedValue) {
		$this->logResult(strlen($this->subject) === $expectedValue, __FUNCTION__, $expectedValue);
		return $this;
	}

	/** String or array contains a substring or value. */
	public function contains($expectedValue) {
		$this->logResult($this->checkContains($expectedValue), __FUNCTION__, $expectedValue);
		return $this;
	}

	/** Array or object contains a key or property. */
	public function containsKey($expectedValue) {
		$this->logResult(array_key_exists($expectedValue, $this->subject), __FUNCTION__, $expectedValue);
		return $this;
	}

	/** Array or object contains a specific key=>value or property=>value */
	public function containsEntry($expectedKey, $expectedValue) {
		if (is_array($this->subject) || $this->subject instanceof \ArrayAccess) {
			$this->logResult(array_key_exists($expectedKey, $this->subject) && $this->subject[$expectedKey] === $expectedValue, __FUNCTION__, func_get_args());
		} elseif (is_object($this->subject)) {
			$this->logResult(isset($this->subject->$expectedKey) && $this->subject->$expectedKey === $expectedValue, __FUNCTION__, func_get_args());
		} else {
			$this->logResult(false, __FUNCTION__, func_get_args());
		}
		return $this;
	}

	/** Reset expected result to be valid. */
	public function is() {
		$this->expectedResult = true;
		return $this;
	}

	/** Set expected result to be inverse (isEmpty becomes isNotEmpty) */
	public function isNot() {
		$this->expectedResult = false;
		return $this;
	}

	/** An array or string contains all of the parameters specified */
	public function containsAllOf() {
		foreach (func_get_args() as $expectedValue) {
			$this->logResult($this->checkContains($expectedValue), __FUNCTION__, $expectedValue);
		}
		return $this;
	}

//	public function isA($expectedValue) {
//		if (gettype($this->subject) === $expectedValue) {
//			$this->logResult(true, __FUNCTION__, $expectedValue);
//		} elseif (is_object($this->subject)) {
//			$this->logResult($this->subject instanceof $expectedValue, __FUNCTION__, $expectedValue);
//		} else {
//			$this->logResult(false, __FUNCTION__, $expectedValue);
//		}
//		return $this;
//	}

	/** Loosely equals a value */
	public function isEqualTo($expectedValue) {
		$this->logResult($this->subject == $expectedValue, __FUNCTION__, $expectedValue);
		return $this;
	}

	/** Exactly equals a value */
	public function isIdenticalTo($expectedValue) {
		$this->logResult($this->subject === $expectedValue, __FUNCTION__, $expectedValue);
		return $this;
	}

	/** Does not loosely equal a value */
	public function isNotEqualTo($expectedValue) {
		$this->logResult($this->subject != $expectedValue, __FUNCTION__, $expectedValue);
		return $this;
	}

	/** Does not exactly equal a value */
	public function isNotIdenticalTo($expectedValue) {
		$this->logResult($this->subject !== $expectedValue, __FUNCTION__, $expectedValue);
		return $this;
	}

	public function isGreaterThan($expectedValue) {
		$this->logResult($this->subject > $expectedValue, __FUNCTION__, $expectedValue);
		return $this;
	}

	public function isLessThan($expectedValue) {
		$this->logResult($this->subject < $expectedValue, __FUNCTION__, $expectedValue);
		return $this;
	}

	/** AKA isLessThanOrEqualTo() */
	public function isAtMost($expectedValue) {
		$this->logResult($this->subject <= $expectedValue, __FUNCTION__, $expectedValue);
		return $this;
	}

	/** AKA isGreaterThanOrEqualTo() */
	public function isAtLeast($expectedValue) {
		$this->logResult($this->subject >= $expectedValue, __FUNCTION__, $expectedValue);
		return $this;
	}

	/** == true */
	public function isTruthy() {
		$this->logResult((bool)$this->subject, __FUNCTION__, null);
		return $this;
	}

	/** == false */
	public function isFalsy() {
		$this->logResult(!$this->subject, __FUNCTION__, null);
		return $this;
	}

	/** Verifies that the subject, when invoked, throws an exception. Pass a callback or closure. */
	public function throwsException($exceptionType = \Exception::class) {
		try {
			call_user_func($this->subject);
		} catch (\Exception $e) {
			$this->logResult($e instanceof $exceptionType, __FUNCTION__, true);
			return;
		}
		$this->logResult(false, __FUNCTION__, false);
	}

	/** When the assertion goes out of scope then check the final result and short-circuit the test with an exception. */
	public function __destruct() {
		if ($this->failure) {
			throw new AssertionFailure($this->failureMessage);
		}
	}

	/**
	 * Checks if a value exists on the subject as an array or object
	 *
	 * @param mixed $expectedValue
	 * @return boolean
	 */
	protected function checkContains($expectedValue) {
		if (is_array($this->subject) || $this->subject instanceof \ArrayAccess) {
			return array_search($expectedValue, $this->subject, true) !== false;
		} elseif (is_string($this->subject)) {
			return strpos($this->subject, $expectedValue) !== false;
		}
		return false;
	}

	/**
	 * Check if the assertion condition is valid or build a failure message to be triggered later.
	 *
	 * @param boolean $pass
	 * @param string $assertion
	 * @param mixed $expectedValue
	 */
	protected function logResult($pass, $assertion, $expectedValue) {
		self::$assertionCount++;
		$this->failure = $this->failure || ($pass !== $this->expectedResult);
		if ($pass !== $this->expectedResult) {
			$this->failureMessage = 'Assertion failed: assertThat(' . var_export($this->subject, true) . ')->' . ($this->expectedResult ? '' : 'isNot()->') . $assertion . '(' . var_export($expectedValue, true) . ')';
		}
	}
}
