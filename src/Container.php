<?php

namespace Akademiano\DI;


use Pimple\Exception\UnknownIdentifierException;
use Akademiano\Config\ConfigurableInterface;

class Container extends \Pimple\Container
{
    protected $values = [];

    public function lazyGet($id)
    {
        return function() use ($id) {
            return $this[$id];
        };
    }

    public function getConfig()
    {
        return $this["config"];
    }

    protected function prepare($value)
    {
        if ($value instanceof ConfigurableInterface) {
            $value->setConfig($this->getConfig());
        }
        return $value;
    }

    public function offsetGet($id)
    {
        if (!$this->offsetExists($id)) {
            throw new UnknownIdentifierException($id);
        }
        if (!isset($this->values[$id])) {
            $this->values[$id] = $this->prepare(parent::offsetGet($id));
        }
        return $this->values[$id];
    }

    public function offsetUnset($id)
    {
        unset($this->values[$id]);
        parent::offsetUnset($id);;
    }
}
