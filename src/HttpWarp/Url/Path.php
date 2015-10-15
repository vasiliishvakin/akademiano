<?php

namespace HttpWarp\Url;

use DeltaUtils\Object\ArrayObject;
use DeltaUtils\Object\Prototype\ArrayableInterface;
use DeltaUtils\Object\Prototype\StringableIterface;

class Path extends ArrayObject implements ArrayableInterface, StringableIterface
{
    protected $rawStr;
    protected $normalStr;
    protected $composedString;


    public function __construct($string = null)
    {
        if (!is_null($string)) {
            $this->setRawStr($string);
        }
    }

    public function normalize($string)
    {
        if (($pos = strpos($string, '?')) !== false) {
            $string = substr($string, 0, $pos);
        }
        $string = preg_replace('~(\/){2,}~', '/', $string);
        if ($string !== '/') {
            $string = rtrim($string, '/');
        }
        $string = rawurldecode($string);
        return $string;
    }

    /**
     * @return string
     */
    public function getRawStr()
    {
        return $this->rawStr;
    }

    /**
     * @param null $rawStr
     */
    protected function setRawStr($rawStr)
    {
        $this->rawStr = $rawStr;
    }

    /**
     * @return mixed|string
     */
    public function getNormalStr()
    {
        if (is_null($this->normalStr)) {
            if (empty($this->getRawStr())) {
                return null;
            }
            $this->setNormalStr($this->normalize($this->getRawStr()));
        }
        return $this->normalStr;
    }

    /**
     * @param mixed|string $normalStr
     */
    protected function setNormalStr($normalStr)
    {
        $this->normalStr = $normalStr;
    }

    protected function &getItems()
    {
        if (empty($this->items)) {
            if (empty($this->getNormalStr())) {
                $this->items = [];
            } else {
                $string = $this->getNormalStr();
                $string = trim($string, "/");
                $this->setItems(explode("/", $string));
            }
        }
        return $this->items;
    }

    protected function clearItemsMeta()
    {
        $this->composedString = null;
        parent::clearItemsMeta();
    }


    function __toString()
    {
        if (is_null($this->composedString)) {
            $parts = array_map(function ($val) {
                return rawurlencode($val);
            }, $this->toArray());

            $this->composedString = "/" . implode("/", $parts);
        }
        return $this->composedString;
    }
}
