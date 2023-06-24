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
        $lazyStore = $this->getLazyStore();
        if (!$lazyStore->hasKey($propertyId)) {
            trigger_error(sprintf('PropertyId %s not found in current LazyStore (keys: "%s")', $propertyId, $lazyStore->keys()->join(' | ')), E_USER_NOTICE);
        }
        $closure = $this->getLazyStore()->remove($propertyId, $default);
        return is_callable($closure) ? call_user_func($closure) : $default;
    }

    public function hasProperty(string $propertyId): bool
    {
        return $this->getLazyStore()->hasKey($propertyId);
    }


    public function hasByMethod(string $objectHash, string $methodName): bool
    {
        return $this->hasProperty(Helper::propertyId($objectHash, $methodName));
    }

    public function setByMethod(string $objectHash, string $methodName, \Closure $closure): void
    {
        $this->setProperty(Helper::propertyId($objectHash, $methodName), $closure);
    }

    public function getByMethod(string $objectHash, string $methodName, $default = null)
    {
        return $this->getProperty(Helper::propertyId($objectHash, $methodName), $default);
    }
}
