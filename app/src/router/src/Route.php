<?php

namespace Akademiano\Router;


use Akademiano\HttpWarp\EnvironmentIncludeInterface;
use Akademiano\HttpWarp\Parts\EnvironmentIncludeTrait;
use Akademiano\Utils\ArrayTools;
use Akademiano\Utils\Debug;
use Akademiano\Utils\Object\Collection;
use Akademiano\Utils\Parts\SetParams;
use Akademiano\HttpWarp\Url;

class Route implements EnvironmentIncludeInterface
{
    use EnvironmentIncludeTrait;

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

    protected $args = [];

    public function __construct($params = null)
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
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param array $methods
     */
    public function setMethods($methods)
    {
        $this->methods = (array)$methods;
    }


    /**
     * @return RoutePattern[]|Collection
     */
    public function getPatterns(): Collection
    {
        return $this->patterns;
    }

    /**
     * @param RoutePattern[] $patterns
     */
    public function setPatterns($patterns)
    {
        $patterns = (array)$patterns;
        if (ArrayTools::getArrayType($patterns) === ArrayTools::ARRAY_TYPE_ASSOC) {
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
        $patternsIds = $this->getPatterns()->lists("type")->toArray();

        return min($patternsIds);
    }

    public function getMaxLength()
    {
        $type = $this->getType();
        switch ($type) {
            case RoutePattern::TYPE_FULL:
            case RoutePattern::TYPE_FIRST_PREFIX:
            case RoutePattern::TYPE_PREFIX:
            {
                $patterns = $this->getPatterns()->filter("type", $type);
                $item = $patterns->max("value", "strlen");
                if (!$item instanceof RoutePattern) {
                    throw new \RuntimeException("Error in get route pattern max length");
                }

                return strlen($item->getValue());
            }
            default:
            {
                throw new \OverflowException("Route with base type {$type} not support length param");
            }
        }
    }

    public function getUrl(array $params = [])
    {
        $url = new Url();
        $url->setEnvironment($this->getEnvironment());
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

    public static function isShort($routeData)
    {
        if (is_array($routeData) && (!isset($routeData["patterns"]) || !isset($routeData["action"]))) {
            return (count($routeData) === 2 && ArrayTools::getArrayType($routeData) === -1 && (mb_strpos($routeData[0], "/") === 0 || mb_strpos($routeData[0], "/") === 2));
        }

        return false;
    }

    public static function shortNormalize(array $routeData)
    {
        if (count($routeData) < 2) {
            throw new \RuntimeException("Route mast have at least two parameters, first for pattern and second for action: " . Debug::var2str($routeData));
        }
        if (isset($routeData["patterns"]) && isset($routeData["action"])) {
            return $routeData;
        }

        $value = $routeData[0];
        $type = RoutePattern::TYPE_DEFAULT;
        if (is_string($value)) {
            $secondChars = substr($value, 1, 1);
            if ($secondChars === ":") {
                $firstChars = substr($value, 0, 2);
                switch ($firstChars) {
                    case RoutePattern::KEY_TYPE_FULL:
                        $type = RoutePattern::TYPE_FULL;
                        break;
                    case RoutePattern::KEY_TYPE_FIRST_PREFIX:
                        $type = RoutePattern::TYPE_FIRST_PREFIX;
                        break;
                    case RoutePattern::KEY_TYPE_PREFIX:
                        $type = RoutePattern::KEY_TYPE_PREFIX;
                        break;
                    case RoutePattern::KEY_TYPE_REGEXP:
                        $type = RoutePattern::TYPE_REGEXP;
                        break;
                    default:
                        throw new \Exception();
                }
                $value = substr($value, 2);
            }
        }

        $routeDataNew = [
            "patterns" => [
                [
                    "type" => $type,
                    "value" => $value,
                ],
            ],
            "action" => $routeData[1],
        ];

        return $routeDataNew;
    }

    public static function isOld($routeData)
    {
        if (is_array($routeData) && (!isset($routeData["patterns"]) || !isset($routeData["action"]))) {
            if (count($routeData) === 2 && ArrayTools::getArrayType($routeData) === -1) {
                $flag = explode("::", $routeData[0]);
                if (count($flag) < 2) {
                    return false;
                }
                $flag = $flag[0];
                return $flag === "full" || $flag === "reg" || $flag === "str";
            }
        }

        return false;
    }

    public static function oldNormalize(array $routeData)
    {
        if (count($routeData) < 2) {
            throw new \RuntimeException("Route mast have at least two parameters, first for pattern and second for action: " . Debug::var2str($routeData));
        }
        $flag = explode("::", $routeData[0]);
        if (count($flag) < 2) {
            return false;
        }
        $value = $flag[1];
        $flag = $flag[0];

        $pattern = ["value" => $value];

        switch ($flag) {
            case "str" :
                break;
            case "full" :
                $pattern["type"] = RoutePattern::TYPE_FULL;
                break;
            case "reg" :
                $pattern["type"] = RoutePattern::TYPE_REGEXP;
                break;
            default:
                throw  new \RuntimeException("Route old flag {$flag} is unknown");
        }
        $routeDataNew = [
            "patterns" => [
                $pattern,
            ],
            "action" => $routeData[1],
        ];
        return $routeDataNew;
    }

    public static function normalize($routeData)
    {
        if (self::isShort($routeData)) {
            $routeData = self::shortNormalize($routeData);
        } elseif (self::isOld($routeData)) {
            $routeData = self::oldNormalize($routeData);
        }
        return $routeData;
    }
}
