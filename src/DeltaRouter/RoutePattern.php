<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 13.10.2015
 * Time: 13:10
 */

namespace DeltaRouter;


use DeltaUtils\Object\Prototype\ArrayableInterface;
use DeltaUtils\Parts\SetParams;
use DeltaUtils\RegexpUtils;
use HttpWarp\Url;

class RoutePattern implements ArrayableInterface
{
    use SetParams;
    const PART_DOMAIN = 1;
    const PART_PATH = 2;
    const PART_QUERY = 3;
    const PART_DOMAIN_NAME = "domain";
    const PART_PATH_NAME = "path";
    const PART_QUERY_NAME = "query";

    const TYPE_FULL = 1;
    const TYPE_FIRST_PREFIX = 2;
    const TYPE_REGEXP = 3;
    const TYPE_PREFIX = 4;
    const TYPE_PARAMS = 5;

    protected $part = self::PART_PATH;
    protected $type = self::TYPE_PREFIX;
    protected $value;

    function __construct(array $params = null)
    {
        if (!is_null($params)) {
            $this->setParams($params);
        }
    }

    public function getId()
    {
        return $this->getPart() . "|" . $this->getType() . "|" . $this->getValue();
    }

    /**
     * @return string
     */
    public function getPart()
    {
        return $this->part;
    }

    /**
     * @param string $part
     */
    public function setPart($part)
    {
        $this->part = $part;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        if ($this->getPart() === self::PART_QUERY) {
            if (is_null($this->type) || $this->type === self::TYPE_PREFIX) {
                $this->type = self::TYPE_PARAMS;
            }
            if ($this->type !== self::TYPE_PARAMS) {
                throw new \RuntimeException("Bad configuration for router pattern: pattern for url params may be only type " . self::TYPE_PARAMS);
            }
        }

        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string|Url\Path|Url\Query
     */
    public function getValue()
    {
        if ($this->getType() === self::TYPE_REGEXP) {
            //convert simple url to url
            if (strpos($this->value, "{:") !== false) {
                $this->value = RegexpUtils::simpleToNormal($this->value);
            }
        } elseif (is_array($this->value)) {
            switch ($this->getPart()) {
                case self::PART_PATH :
                    $value = new Url\Path();
                    break;
                case self::PART_QUERY:
                    $value = new Url\Query();
                    break;
                default:
                    throw new \InvalidArgumentException("Array value not allowed in this type " . $this->getType());
            }
            $value->setItems($this->value);
            $this->value = $value;
        } elseif ($this->getPart() === self::PART_QUERY && is_string($this->value) && $this->getType() === self::TYPE_PARAMS) {
            $this->value = new Url\Query($this->value);
        }

        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    public function toArray()
    {
        $value = $this->getValue() instanceof ArrayableInterface ? $this->getValue()->toArray() : $this->getValue();

        return [
            "part" => $this->getPart(),
            "type" => $this->getType(),
            "value" => $value,
        ];
    }

    public function getDelimiter()
    {
        switch ($this->getPart()) {
            case self::PART_DOMAIN :
                $delimiter = ".";
                break;
            case self::PART_PATH :
                $delimiter = "/";
                break;
            case self::PART_QUERY:
                $delimiter = "&";
                break;
            default :
                throw new \LogicException("Could not get delimiter for part" . $this->getPart());
        }

        return $delimiter;
    }

    public function calcValue($params = null)
    {
        $value = $this->getValue();
        if (is_null($params)) {
            if ($this->getType() === self::TYPE_REGEXP) {
                throw new \InvalidArgumentException("Regexp type cold not be calculated without params");
            }

            return $value;
        }
        switch ($this->getType()) {
            case self::TYPE_FULL:
                throw new \InvalidArgumentException("Full type RoutePattern not use params (" . implode("|",
                        (array)$params) . ") for calc value");
            case self::TYPE_PREFIX:
            case self::TYPE_FIRST_PREFIX:
                if (is_array($params)) {
                    $params = implode($this->getDelimiter(), $params);
                }

                return $value . $this->getDelimiter() . $params;
            case self::TYPE_REGEXP:
                $params = (array)$params;

                return RegexpUtils::replaceNamedParams($value, $params);
            case self::TYPE_PARAMS:
                $params = (array)$params;
                /** Query $value */
                $value = $value->toArray();
                $value = array_merge($value, $params);

                return $value;
            default:
                throw new \InvalidArgumentException("Type " . $this->getType() . " not supported");
        }
    }
}
