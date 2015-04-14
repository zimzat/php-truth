<?php

require_once __DIR__ . '/../vendor/autoload.php';

$tests = [];
if ($argc > 1 && is_file($argv[1])) {
	$b = get_declared_classes();
	require_once $argv[1];
	$tests = array_merge($tests, array_diff(get_declared_classes(), $b));
} else {
	foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($argc > 1 ? $argv[1] : '.')) as $path) {
		/* @var $path SplFileInfo */
		if ($path->isDir()) {
			continue;
		}
		if ($path->getExtension() === 'php' && strpos($path->getBasename('php'), 'Test')) {
			$b = get_declared_classes();
			require_once $path;
			$tests = array_merge($tests, array_diff(get_declared_classes(), $b));
		}
	}
}

(new \PhpTruth\TestRunner($tests))->run();
