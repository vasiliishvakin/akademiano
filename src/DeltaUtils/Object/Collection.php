<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 19.06.2015
 * Time: 18:56
 */

namespace DeltaUtils\Object;


use DeltaCore\Prototype\ArrayableInterface;
use DeltaUtils\Exception\EmptyException;

class Collection extends ArrayObject implements ArrayableInterface
{
    function __construct(array $items = null)
    {
        if (null !== $items) {
            $this->setItems($items);
        }
    }

    public function toArray()
    {
        $array = [];
        foreach($this as $key=>$value) {
            if ($value instanceof ArrayableInterface) {
                $value = $value->toArray();
            }
            $array[$key] = $value;
        }
        return $array;
    }

    public function first()
    {
        $this->rewind();
        if (!$this->valid()) {
            return null;
        }
        return $this->current();
    }

    public function firstOrFail()
    {
        if ($this->count() <=0) {
            throw new EmptyException();
        }
        return $this->first();
    }

    public function firstOrFalse()
    {
        if ($this->count() <=0) {
            return false;
        }
        return $this->first();
    }

    public function all()
    {
        return $this->items;
    }

}
