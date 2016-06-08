<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 19.06.2015
 * Time: 18:56
 */

namespace DeltaUtils\Object;


use DeltaUtils\Object\Prototype\ArrayableInterface;
use DeltaUtils\Exception\EmptyException;

class Collection extends ArrayObject implements ArrayableInterface
{
    function __construct($data = null)
    {
        if (null !== $data) {
            $this->setItems((array)$data);
        }
    }

    public function toArray()
    {
        $array = [];
        foreach ($this as $key => $value) {
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
        if ($this->count() <= 0) {
            throw new EmptyException();
        }
        return $this->first();
    }

    public function firstOrFalse()
    {
        if ($this->count() <= 0) {
            return false;
        }
        return $this->first();
    }

    public function last()
    {
        return end($this->items);
    }

    public function lists($field, $keyField = null)
    {
        $data = [];
        $method = 'get' . ucfirst($field);
        $keyMethod = !is_null($keyField) ? 'get' . ucfirst($keyField) : null;
        foreach ($this as $item) {
            if (is_callable([$item, $method])) {
                $value = $item->{$method}();
            }
            if (!empty($keyMethod) && is_callable([$item, $keyMethod])) {
                $key = $item->{$keyMethod}();
            }
            if ($value) {
                if ($keyField) {
                    $data[$key] = $value;
                } else {
                    $data[] = $value;
                }
            }
        }
        return $data;
    }

    public function filter($field, $needValue, $operator = "===")
    {
        $data = [];
        if (is_string($field)) {
            $method = function ($item) use ($field) {
                if (is_object($item)) {
                    $method = 'get' . ucfirst($field);
                    if (is_callable([$item, $method])) {
                        return $item->{$method}();
                    }
                } elseif (is_array($item)) {
                    if (isset($item[$field])) {
                        return $item[$field];
                    }
                }
            };
        } elseif (is_integer($field)) {
            $method = function ($item) use ($field) {
                if (is_array($item) && isset($item[$field])) {
                    return $item[$field];
                }
            };
        } elseif (is_callable($field)) {
            $method = $field;
        }
        foreach ($this as $item) {
            $value = call_user_func($method, $item);
            switch ($operator) {
                case "===" :
                    if ($value === $needValue) {
                        $data[] = $item;
                    }
                    break;
                case "!==" :
                case "<>" :
                    if ($value !== $needValue) {
                        $data[] = $item;
                    }
                    break;
                case "==" :
                    if ($value === $needValue) {
                        $data[] = $item;
                    }
                    break;
                case "<":
                    if ($value < $needValue) {
                        $data[] = $item;
                    }
                    break;
                case ">":
                    if ($value > $needValue) {
                        $data[] = $item;
                    }
                    break;
                case "<=":
                    if ($value <= $needValue) {
                        $data[] = $item;
                    }
                    break;
                case ">=":
                    if ($value >= $needValue) {
                        $data[] = $item;
                    }
                    break;
            }
        }
        return new Collection($data);
    }

    public function isEmpty()
    {
        return (bool)$this->count() <= 0;
    }

    public function merge($data)
    {
        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $this[$key] = $value;
            } else {
                $this[] = $value;
            }
        }
    }

    public function usort(Callable $function)
    {
        return usort($this->items, $function);
    }

    protected function minMax($direction, $field, Callable $function = null)
    {
        if ($this->isEmpty()) {
            throw new \LengthException("Empty collection");
        }
        $method = "get" . ucfirst($field);
        $calcItem = null;
        foreach ($this as $item) {
            if (is_callable([$item, $method])) {
                $value = $item->{$method}();
                if (is_callable($function)) {
                    $value = call_user_func($function, $value);
                }
                if (!isset($calcVal)) {
                    $calcVal = $value;
                    $calcItem = $item;
                }
                if ($direction === "min") {
                    if ($value < $calcVal) {
                        $calcItem = $item;
                    }
                } elseif ($direction === "max") {
                    if ($value > $calcVal) {
                        $calcItem = $item;
                    }
                }
            }
        }
        return $calcItem;
    }

    public function min($field, Callable $valueCalcFunction = null)
    {
        return $this->minMax("min", $field, $valueCalcFunction);
    }

    public function max($field, Callable $valueCalcFunction = null)
    {
        return $this->minMax("max", $field, $valueCalcFunction);
    }

    public function slice($offset = 1, $length = null)
    {
        $slice = array_slice($this->getItems(), $offset, $length, true);
        return new Collection($slice);
    }

    public function map(Callable $function)
    {
        foreach ($this as $key => $item) {
            $this[$key] = call_user_func($function, $item, $key);
        }
        return $this;
    }
}
