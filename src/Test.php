<?php

namespace PhpTruth;

class Test {
	public function isIncomplete($message = '') {
		throw new TestIncomplete($message);
	}

	public function isSkipped($message = '') {
		throw new TestSkipped($message);
	}
}
