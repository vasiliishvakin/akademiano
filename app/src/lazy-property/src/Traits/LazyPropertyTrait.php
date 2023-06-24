<?php


namespace Akademiano\LazyProperty\Traits;

use Akademiano\Entity\BaseEntityInterface;
use Akademiano\LazyProperty\Helper;
use Akademiano\LazyProperty\LazyPropertyContainer;
use Akademiano\LazyProperty\LazyPropertyContainerFactory;
use Closure;
use Ds\Hashable;


trait LazyPropertyTrait
{
    protected LazyPropertyContainer $lpContainer;


    protected function getLpContainer(): LazyPropertyContainer
    {
        if (!isset($this->lpContainer)) {
            $this->lpContainer = ($this instanceof BaseEntityInterface && $this->hasId()) ? LazyPropertyContainerFactory::factory() : LazyPropertyContainerFactory::build();
        }
        return $this->lpContainer;
    }

    protected function setByMethodLazy(string $methodName, Closure $calculatedClosure, ?Closure $conditionalClosure = null, $readyValue = null): void
    {
        if (is_callable($conditionalClosure)) {
            $switch = call_user_func($conditionalClosure);
            if ($switch && null !== $readyValue) {
                $property = Helper::method2Property($methodName);
                $this->{$property} = $readyValue;
                return;
            }
        }
        $this->getLpContainer()->setByMethod(Helper::getObjectHash($this), $methodName, $calculatedClosure);
    }

    protected function getByMethodLazy(string $methodName)
    {
        $property = Helper::method2Property($methodName);
        if (!isset($this->{$property}) || null == $this->{$property}) {
            $this->{$property} = $this->getLpContainer()->getByMethod(Helper::getObjectHash($this), $methodName);
        }
        return $this->{$property};
    }
}
