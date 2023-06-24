<?php


namespace Akademiano\LazyProperty\Traits;

use Closure;

trait LazyPropertyAbstractTrait
{
    abstract protected function setByMethodLazy(string $methodName, Closure $calculatedClosure, ?Closure $conditionalClosure = null, $readyValue = null): void;
    abstract protected function getByMethodLazy(string $methodName);
}
