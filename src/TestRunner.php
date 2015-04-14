<?php

namespace PhpTruth;

class TestRunner {
	protected $testCount = 0;

	protected $tests = [];

	protected $failures = [];

	protected $incompletes = [];

	protected $skips = [];

	public function __construct($tests) {
		if (!is_array($tests)) {
			$this->tests = [$tests];
		} else {
			$this->tests = $tests;
		}
	}

	public function run() {
		$this->failures = [];
		$initialAssertionCount = Assert::$assertionCount;
		$this->start = microtime(true);

		array_walk_recursive($this->tests, [$this, 'runTest']);

		echo "\n\n";
		printf('Time: %.3fs Memory: %.2fMB', microtime(true) - $this->start, memory_get_peak_usage() / 1024 / 1024);

		echo "\n\n", 'Attempted ', Assert::$assertionCount - $initialAssertionCount, ' assertions across ', $this->testCount, ' tests.', "\n";
		foreach (['failed' => $this->failures, 'skipped' => $this->skips, 'ignored' => $this->incompletes] as $name => $result) {
			if (empty($result)) {
				continue;
			}

			echo "\n", ucfirst($name), ' ', count($result), ':', "\n";
			foreach ($result as $test => $failure) {
				echo $test, "\n", $failure, "\n";
			}
		}
		echo "\n";
	}

	protected function runTest($test) {
		if (is_string($test)) {
			$test = new $test();
		}

		foreach (get_class_methods($test) as $method) {
			if (strpos($method, 'test') !== 0) {
				continue;
			}

			$this->testCount++;

			$repeat = [[]];

			$annotations = [];
			$rm = new \ReflectionMethod($test, $method);
			preg_match_all('#^[\s/*]*@(\S+)\s*(.*?)[\s/*]*$#m', $rm->getDocComment(), $annotations, PREG_SET_ORDER);
			foreach ($annotations as $annotation) {
				if ($annotation[1] === 'dataProvider') {
					$repeat = $test->{$annotation[2]}();
				}
			}

			try {
				foreach ($repeat as $args) {
					if (method_exists($test, 'setUp')) {
						$test->setUp();
					}
					$rm->invokeArgs($test, $args);
					if (method_exists($test, 'tearDown')) {
						$test->tearDown();
					}
					echo '.';
				}
			} catch (TestIncomplete $e) {
				echo 'I';
				$this->incompletes[$this->getTestSignature($test, $rm, $args)] = $e->getMessage();
			} catch (TestSkipped $e) {
				echo 'S';
				$this->skips[$this->getTestSignature($test, $rm, $args)] = $e->getMessage();
			} catch (AssertionFailure $e) {
				echo 'F';
				$this->failures[$this->getTestSignature($test, $rm, $args)] = $e->getMessage() . "\n" . $e->getAssertionTrace();
			} catch (\Exception $e) {
				echo 'E';
				$this->failures[$this->getTestSignature($test, $rm, $args)] = 'Uncaught Exception: ' . $e->getMessage() . "\n\t" . explode("\n", $e->getTraceAsString())[0];
			}
		}
	}

	protected function getTestSignature($test, \ReflectionMethod $rm, $args) {
		$params = [];
		foreach ($rm->getParameters() as $rp) {
			/* @var $rp \ReflectionParameter */
			$params[] = $rp->getName() . '=' . var_export($args[$rp->getPosition()], true);
		}

		return get_class($test) . '->' . $rm->getName() . '(' . implode(', ', $params) . ')';
	}
}
