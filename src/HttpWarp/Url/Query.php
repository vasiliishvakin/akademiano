<?php

namespace HttpWarp\Url;

use DeltaUtils\Object\ArrayObject;
use DeltaUtils\Object\Prototype\ArrayableInterface;
use DeltaUtils\Object\Prototype\StringableInterface;

class Query extends ArrayObject implements ArrayableInterface, StringableInterface
{
    protected $rawStr;
    protected $normalStr;
    protected $composedString;

    public function __construct($data = null)
    {
        if (!is_null($data)) {
            if (is_array($data)) {
                $this->setItems($data);
            } elseif (is_string($data)) {
                $this->setRawStr($data);
            }
        }
    }

    /**
     * @return string
     */
    public function getRawStr()
    {
        return $this->rawStr;
    }

    /**
     * @param string $rawStr
     */
    protected function setRawStr($rawStr)
    {
        $this->rawStr = $rawStr;
    }

    public function normalize($string)
    {
        if (($pos = strpos($string, '?')) !== false) {
            $string = substr($string, $pos);
        }
        $string = urldecode($string);

        return $string;
    }

    /**
     * @return null|string
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
     * @param mixed $normalStr
     */
    protected function setNormalStr($normalStr)
    {
        $this->normalStr = $normalStr;
    }

    /**
     * @return mixed
     */
    public function getComposedString()
    {
        return $this->composedString;
    }

    /**
     * @param mixed $composedString
     */
    public function setComposedString($composedString)
    {
        $this->composedString = $composedString;
    }

    protected function &getItems()
    {
        if (empty($this->items)) {
            if (empty($this->getNormalStr())) {
                $this->items = [];
            } else {
                $string = $this->getNormalStr();
                $parts = explode("&", $string);
                $newParts = [];
                foreach ($parts as $part) {
                    $part = explode("=", $part);
                    $newParts[$part[0]] = $part[1];
                }
                $this->setItems($newParts);
            }
        }
        return $this->items;
    }

    protected function clearItemsMeta()
    {
        parent::clearItemsMeta();
        $this->composedString = null;
    }

    function __toString()
    {
        if (is_null($this->composedString)) {
            $this->ksort();
            $str = [];
            foreach ($this as $name => $part) {
                $str[] = urlencode($name) . "=" . urlencode($part);
            }
            $str = implode("&", $str);
            $this->composedString = $str;
        }

        return $this->composedString;
    }

}
