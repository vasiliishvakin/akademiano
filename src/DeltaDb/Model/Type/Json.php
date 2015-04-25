<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb\Model\Type;


class Json implements \JsonSerializable, \ArrayAccess
{
    private $rawData;

    protected $data;

    function __construct($data = null)
    {
        if (!is_null($data)) {
            $this->setData($data);
        }
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        if (is_scalar($data)) {
            $this->rawData = $data;
            $this->data = null;
        } elseif (is_array($data)){
            $this->data = $data;
        } elseif (is_object($data)) {
            $this->data = (array) $data;
        } else {
            throw new \InvalidArgumentException("data type not supported");
        }
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        if (is_null($this->data)) {
            $this->data = json_decode($this->rawData, true);
        }
        return $this->data;
    }

    function __toString()
    {
        return json_encode($this->getData(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return $this->getData();
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
        return (array_key_exists($offset, $this->getData()));
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
        return $this->getData()[$offset];
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
        $data = $this->getData();
        $data[$offset] = $value;
        $this->setData($data);
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
        $data = $this->getData();
        unset($data[$offset]);
        $this->setData($data);
    }


}