<?php

namespace Akademiano\DI;


use Akademiano\Utils\Object\Prototype\ArrayableInterface;
use Pimple\Exception\UnknownIdentifierException;
use Akademiano\Config\ConfigurableInterface;

class Container extends \Pimple\Container implements ArrayableInterface
{
    protected $values = [];

    private \Closure $factoriesGetter;
    private \Closure $rawsGetter;
    private \Closure $valuesGetter;

    /**
     * Instantiates the container.
     *
     * Objects and parameters can be passed as argument to the constructor.
     *
     * @param array $values The parameters or objects
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);


        $getterFactoriesClosure = \Closure::bind(function (\Pimple\Container $container) {
            return $container->factories;
        }, null, \Pimple\Container::class);

        $this->factoriesGetter = function () use ($getterFactoriesClosure) {
            return $getterFactoriesClosure($this);
        };

        $getterRawClosure = \Closure::bind(function (\Pimple\Container $container) {
            return $container->raw;
        }, null, \Pimple\Container::class);

        $this->rawsGetter = function () use ($getterRawClosure) {
            return $getterRawClosure($this);
        };

        $getterValuesClosure = \Closure::bind(function (\Pimple\Container $container) {
            return $container->values;
        }, null, \Pimple\Container::class);

        $this->valuesGetter = function () use ($getterValuesClosure) {
            return $getterValuesClosure($this);
        };
    }

    protected function getFactories()
    {
        return ($this->factoriesGetter)();
    }

    protected function getRaws()
    {
        return ($this->rawsGetter)();
    }


    protected function getValues()
    {
        return ($this->valuesGetter)();
    }

    protected function getRaw($id)
    {
        $raws = $this->getRaws();
        if (isset($raws[$id])) {
            return $raws[$id];
        }
        $values = $this->getValues();
        return $values[$id];
    }

    protected function isFactory($id): bool
    {
        $raw = $this->getRaw($id);
        if (!$raw instanceof \Closure) {
            return false;
        }
        $factories = $this->getFactories();
        return isset($factories[$raw]);
    }

    public function lazyGet($id)
    {
        return function () use ($id) {
            return $this[$id];
        };
    }

    //rewrite in child
    protected function prepare($value)
    {
        return $value;
    }

    public function offsetGet($id)
    {
        if (!$this->offsetExists($id)) {
            throw new UnknownIdentifierException($id);
        }
        if (!isset($this->values[$id])) {
            if ($this->isFactory($id)) {
                return $this->prepare(parent::offsetGet($id));
            }
            $this->values[$id] = $this->prepare(parent::offsetGet($id));
        }
        return $this->values[$id];
    }

    public function offsetUnset($id)
    {
        unset($this->values[$id]);
        parent::offsetUnset($id);;
    }

    public function toArray()
    {
        $keys = $this->keys();
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this[$key];
        }
        return $values;
    }


}
