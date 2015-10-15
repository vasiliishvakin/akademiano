<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaUtils\Object;


class ArrayObject implements \ArrayAccess, \Iterator, \Countable
{
    protected $items = [];
    protected $itemsValues;
    protected $itemsKeys;
    protected $count;

    protected $position = 0;

    protected function clearItemsMeta()
    {
        $this->itemsKeys = null;
        $this->itemsValues = null;
        $this->count = null;
    }

    public function getKeys()
    {
        if (null === $this->itemsKeys) {
            $this->itemsKeys = array_keys($this->getItems());
        }
        return $this->itemsKeys;
    }

    public function getValues()
    {
        if (null === $this->itemsValues) {
            $this->itemsValues = array_values($this->getItems());
        }
        return $this->itemsValues;
    }

    public function setItems(array $items)
    {
        $this->items = $items;
        $this->clearItemsMeta();
    }

    protected function &getItems()
    {
        return $this->items;
    }


    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->getItems());
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->getItems()[$offset];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $items = &$this->getItems();
        if (null === $offset) {
            $items[] = $value;
        } else {
            $items[$offset] = $value;
        }
        $this->clearItemsMeta();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        $items = &$this->getItems();
        unset($items[$offset]);
        $this->clearItemsMeta();
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        if (null === $this->count) {
            $this->count = count($this->getItems());
        }
        return $this->count;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->getValues()[$this->position];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->getKeys()[$this->position];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->position >= 0 && $this->position < $this->count();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->position = 0;
    }

    public function toArray()
    {
        return $this->getItems();
    }

    public function isEmpty()
    {
        return (bool)$this->count() <= 0;
    }

    public function usort(Callable $function)
    {
        return usort($this->getItems(), $function);
    }

    public function uksort(Callable $function)
    {
        return uksort($this->getItems(), $function);
    }

    public function ksort()
    {
        return ksort($this->getItems());
    }

}