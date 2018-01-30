<?php

namespace Akademiano\DI;


use Akademiano\Utils\Object\Prototype\ArrayableInterface;
use Pimple\Exception\UnknownIdentifierException;
use Akademiano\Config\ConfigurableInterface;

class Container extends \Pimple\Container implements ArrayableInterface
{
    protected $values = [];

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
