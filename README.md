

TIP: Truth In PHP
=====

or: TruthPHP, TruthPhp<br />
or: PHPTruth, PhpTruth

A skinny unit testing framework for PHP based off [Google Truth](http://google.github.io/truth/).

The goal of Truth is to use expressive statements that are easily readable and can be traced back to their origin.

In Truth assertions are context-sensitive to the subject being passed. Instead of `$this->assertArrayHasKey($key, $array)` or `$this->assertObjectHasAttribute($key, $object)`, in Truth both can be done with `assertThat($subject)->containsKey($key)`.

Assertions are chainable: `assertThat($subject)->isLessThan(3)->isNotEqualTo(0)`

Exceptions can be tested for using closures:

```php
assertThat(function() {
	throw new \InvalidArgumentException();
})->throwsException(\InvalidArgumentException::class);
```

Tests can also be skipped or noted as incomplete:

```php
test()->isIncomplete();
test()->isSkipped();
```

