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

    protected $id;
    /** @var array */
    protected $methods = [self::METHOD_ALL];
    /** @var  Collection|RoutePattern[] */
    protected $patterns;
    /** @var  Callable */
    protected $action;

    protected $args=[];

    function __construct($params = null)
    {
        $this->patterns = new Collection();
        if (!is_null($params)) {
            $this->setParams($params);
        }
    }

    public function getId()
    {
        if (empty($this->id)) {
            $patterns = $this->getPatterns();
            $ids = [];
            foreach ($patterns as $pattern) {
                $ids[] = $pattern->getId();
            }

            return implode("#", $this->getMethods()) . "#" . implode("$", $ids);
        }

        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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

    /**
     * @return mixed
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param mixed $args
     */
    public function setArgs($args)
    {
        $this->args = (array)$args;
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

    public function fillPart()
    {

    }

    public function getUrl(array $params = [])
    {
        $url = new Url();
        foreach ($this->getPatterns() as $pattern) {
            switch ($pattern->getPart()) {
                case RoutePattern::PART_DOMAIN:
                    $paramName = RoutePattern::PART_DOMAIN_NAME;
                    $method = "setDomain";
                    break;
                case RoutePattern::PART_PATH:
                    $paramName = RoutePattern::PART_PATH_NAME;
                    $method = "setPath";
                    break;
                case RoutePattern::PART_QUERY:
                    $paramName = RoutePattern::PART_QUERY_NAME;
                    $method = "setQuery";
                    break;
                default:
                    throw new \InvalidArgumentException("Not implemented for part" . $pattern->getPart());
            }
            $param = $this->getPatterns()->count() > 1 ? (isset($params[$paramName]) ? $params[$paramName] : "null") : $params;
            if (empty($param)) {
                $param = null;
            }
            $value = $pattern->calcValue($param);
            $url->{$method}($value);
        }

        return $url;
    }
}
