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
use HttpWarp\Url;

class RoutePattern implements ArrayableInterface
{
    use SetParams;

    const PART_DOMAIN = 1;
    const PART_PATH = 2;
    const PART_PARAM = 3;

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
        if ($this->getPart() === self::PART_PARAM && $this->type !== self::TYPE_PARAMS) {
            throw new \RuntimeException("Bad configuration for router pattern: pattern for url params may be only type " . self::TYPE_PARAMS);
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
        if (is_array($this->value)){
            switch($this->getPart()) {
                case self::PART_PATH :
                    $value = new Url\Path();
                    break;
                case self::PART_PARAM:
                    $value = new Url\Query();
                    break;
                default:
                    throw new \InvalidArgumentException("Array value not allowed in this type " . $this->getType());
            }
            $value->setItems($this->value);
            $this->value = $value;
        } elseif ($this->getPart() === self::PART_PARAM && is_string($this->value) && $this->getType() === self::TYPE_PARAMS) {
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

}
