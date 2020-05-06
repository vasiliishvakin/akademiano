<?php


namespace Akademiano\LazyProperty;


use Ds\Map;

class LazyPropertyContainer
{
    protected Map $lazyStore;

    /**
     * LazyPropertyContainer constructor.
     */
    public function __construct()
    {
        $this->lazyStore = new Map();
    }

    protected function getLazyStore(): Map
    {
        return $this->lazyStore;
    }

    public function setProperty(string $propertyId, \Closure $closure): void
    {
        $this->getLazyStore()->put($propertyId, $closure);
    }

    public function getProperty(string $propertyId, $default = null)
    {
        $closure = $this->getLazyStore()->remove($propertyId, $default);
        return is_callable($closure) ? call_user_func($closure) : $default;
    }

    public function hasProperty(string $propertyId): bool
    {
        return $this->getLazyStore()->hasKey($propertyId);
    }


    public
    function hasByMethod(string $methodName): bool
    {
        return $this->hasProperty(Helper::propertyId($methodName));
    }

    public
    function setByMethod(string $methodName, \Closure $closure): void
    {
        $this->setProperty(Helper::propertyId($methodName), $closure);
    }

    public
    function getByMethod(string $methodName, $default = null)
    {
        return $this->getProperty(Helper::propertyId($methodName), $default);
    }
}
