<?php
/**
 * Created by PhpStorm.
 * User: orbisnull
 * Date: 13.10.2015
 * Time: 17:55
 */

namespace DeltaRouter;


use DeltaUtils\ArrayUtils;
use DeltaUtils\Object\Collection;
use DeltaUtils\Parts\SetParams;
use HttpWarp\Url;

class Route
{
    const METHOD_ALL = "ALL";
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const METHOD_PUT = "PUT";
    const METHOD_DELETE = "DELETE";
    const METHOD_OPTIONS = "OPTIONS";

    use SetParams;

    /** @var array */
    protected $methods = [self::METHOD_ALL];
    /** @var  Collection|RoutePattern[] */
    protected $patterns;
    protected $action;

    function __construct($params = null)
    {
        $this->patterns = new Collection();
        if (!is_null($params)) {
            $this->setParams($params);
        }
    }

    public function getId()
    {
        $patterns = $this->getPatterns();
        $ids = [];
        foreach ($patterns as $pattern) {
            $ids[] = $pattern->getId();
        }

        return implode("#", $this->getMethods()) . "#" . implode("$", $ids);
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param array $methods
     */
    public function setMethods($methods)
    {
        $this->methods = $methods;
    }


    /**
     * @return RoutePattern[]|Collection
     */
    public function getPatterns()
    {
        return $this->patterns;
    }

    /**
     * @param RoutePattern[] $patterns
     */
    public function setPatterns($patterns)
    {
        $patterns = (array)$patterns;
        if (ArrayUtils::getArrayType($patterns) === ArrayUtils::ARRAY_TYPE_ASSOC) {
            $patterns = [$patterns];
        }
        foreach ($patterns as $pattern) {
            if (!$pattern instanceof RoutePattern) {
                $pattern = new RoutePattern($pattern);
            }
            $this->patterns[$pattern->getId()] = $pattern;
        }
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    public function getType()
    {
        $patternsIds = $this->getPatterns()->lists("type");

        return min($patternsIds);
    }

    public function getMaxLength()
    {
        $type = $this->getType();
        switch ($type) {
            case RoutePattern::TYPE_FULL:
            case RoutePattern::TYPE_FIRST_PREFIX:
            case RoutePattern::TYPE_PREFIX: {
                $patterns = $this->getPatterns()->filter("type", $type);
                $item = $patterns->max("value", "strlen");
                if (!$item instanceof RoutePattern) {
                    throw new \RuntimeException("Error in get route pattern max length");
                }
                return strlen($item->getValue());
            }
            default: {
                throw new \OverflowException("Route with base type {$type} not support length param");
            }
        }
    }
}
