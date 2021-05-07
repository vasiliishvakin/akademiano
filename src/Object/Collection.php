<?php

namespace Akademiano\Utils\Object;


use Akademiano\Utils\Exception;
use Akademiano\Utils\Object\Prototype\ArrayableInterface;
use Akademiano\Utils\Exception\EmptyException;
use Akademiano\Utils\Object\Prototype\IntegerableInterface;
use Akademiano\Utils\Object\Prototype\StringableInterface;

class Collection extends ArrayObject implements ArrayableInterface, \JsonSerializable
{
    public function __construct($data = null)
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

    public function firstOrFail(\Exception $exception = null)
    {
        if ($this->count() <= 0) {
            if (null !== $exception) {
                throw $exception;
            } else {
                throw new EmptyException();
            }
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

    protected function itemFieldNameToCallable($fieldName): callable
    {
        if (is_string($fieldName)) {
            $method = function ($item) use ($fieldName) {
                if (is_object($item)) {
                    $method = 'get' . ucfirst($fieldName);
                    if (is_callable([$item, $method])) {
                        return $item->{$method}();
                    } else {
                        $method = 'is' . ucfirst($fieldName);
                        if (is_callable([$item, $method])) {
                            return $item->{$method}();
                        }
                    }
                } elseif (is_array($item)) {
                    if (isset($item[$fieldName])) {
                        return $item[$fieldName];
                    }
                } else {
                    throw new \LogicException(sprintf('Item type must be a object or array, but it is "%s"', gettype($item)));
                }
            };
        } elseif (is_integer($fieldName)) {
            $method = function ($item) use ($fieldName) {
                if (is_array($item) && isset($item[$fieldName])) {
                    return $item[$fieldName];
                } else {
                    throw new \LogicException(sprintf('Item type must be array, but it is "%s"', gettype($item)));
                }
            };
        } elseif (is_callable($fieldName)) {
            $method = $fieldName;
        }
        return $method;
    }

    public function lists($field, $keyField = null)
    {
        $method = $this->itemFieldNameToCallable($field);

        $keyMethod = !is_null($keyField)
            ? function ($item) use ($keyField) {
                $method = $this->itemFieldNameToCallable($keyField);
                $key = call_user_func($method, $item);
                if (is_object($key)) {
                    if ($key instanceof IntegerableInterface) {
                        $key = $key->getInt();
                    } elseif ($key instanceof StringableInterface) {
                        $key = $key->__toString();
                    } else {
                        throw new \LogicException(sprintf('Could not use object for array key'));
                    }
                } elseif (!is_scalar($key)) {
                    throw new \LogicException(sprintf('Could not use not scalar value for array key'));
                }
                return $key;
            }
            : null;

        $data = [];
        foreach ($this as $item) {
            $value = null;
            $key = null;

            $value = call_user_func($method, $item);
            $key = ($keyMethod) ? call_user_func($keyMethod, $item) : null;

            if ($value) {
                if ($key) {
                    $data[$key] = $value;
                } else {
                    $data[] = $value;
                }
            }
        }
        return new Collection($data);
    }

    public function filter($field, $needValue, $operator = "===")
    {
        $method = $this->itemFieldNameToCallable($field);

        //prepare $needValue
        if ($operator === 'in') {
            $needValue = (array)$needValue;
        }

        $data = [];
        foreach ($this as $item) {
            $value = call_user_func($method, $item);
            switch ($operator) {
                case '===' :
                    if ($value === $needValue) {
                        $data[] = $item;
                    }
                    break;
                case '!==' :
                case '<>' :
                    if ($value !== $needValue) {
                        $data[] = $item;
                    }
                    break;
                case '==' :
                    if ($value === $needValue) {
                        $data[] = $item;
                    }
                    break;
                case '<':
                    if ($value < $needValue) {
                        $data[] = $item;
                    }
                    break;
                case '>':
                    if ($value > $needValue) {
                        $data[] = $item;
                    }
                    break;
                case '<=':
                    if ($value <= $needValue) {
                        $data[] = $item;
                    }
                    break;
                case '>=':
                    if ($value >= $needValue) {
                        $data[] = $item;
                    }
                    break;
                case 'in':
                    if (in_array($value, $needValue)) {
                        $data[] = $item;
                    }
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('%s method %s not support argument operator %s', __CLASS__, __METHOD__, $operator));
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
        return $this;
    }

    public function usort(callable $function)
    {
        usort($this->items, $function);
        return $this;
    }

    protected function minMax($direction, $field, callable $function = null)
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

    public function min($field, callable $valueCalcFunction = null)
    {
        return $this->minMax("min", $field, $valueCalcFunction);
    }

    public function max($field, callable $valueCalcFunction = null)
    {
        return $this->minMax("max", $field, $valueCalcFunction);
    }

    public function slice($offset = 1, $length = null)
    {
        $slice = array_slice($this->getItems(), $offset, $length, true);
        return new Collection($slice);
    }

    public function map(callable $function): Collection
    {
        $items = $this->getItems();
        $result = [];
        foreach ($items as $key => $item) {
            $result[$key] = call_user_func($function, $item, $key);
        }
        return new Collection($result);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param Collection|array $collection
     * @return Collection
     */
    public function intersect($collection): Collection
    {
        if ($this->count() === 0) {
            return new Collection([]);
        }
        if ($collection instanceof Collection) {
            $collection = $collection->toArray();
        }
        $items = $this->getItems();
        $intersect = array_intersect_key($items, $collection);
        return new Collection($intersect);
    }

    public function reduce(callable $function, $initial = null)
    {
        $items = $this->getItems();
        $result = $initial;
        foreach ($items as $item) {
            $result = call_user_func($function, $result, $item);
        }
        return $result;
    }
}
