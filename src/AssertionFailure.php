<?php

namespace PhpTruth;

class AssertionFailure extends \Exception {
	protected $assertionTrace;

	public function getAssertionTrace() {
		$output = [];
		foreach ($this->getTrace() as $k => $trace) {
			if ($k === 0) {
				continue;
			}
			if (isset($trace['class']) && $trace['class'] === 'Truth\TestRunner') {
				break;
			}
			$output[] = '#' . ($k - 1) . ' ' . (isset($trace['file']) ? $trace['file'] . '(' . $trace['line'] . ')' : '[internal function]') . ': ' . (isset($trace['class']) ? $trace['class'] . $trace['type'] : '') . $trace['function'] . '()';
//			break;
		}
		return "\t" . implode("\n\t", $output);
	}
}
