<?php
namespace DeltaRouter;

use DeltaRouter\Exception\NotFoundException;
use DeltaUtils\Object\Collection;
use DeltaUtils\RegexpUtils;
use HttpWarp\Request;
use HttpWarp\Url;

/**
 * класс осуществляет роутинг и вызывает нужные обработчики
 */
class Router
{
    const RUN_NEXT = "____run_next";

    /** @var array Collection|Router[] */
    protected $routes;
    protected $isRun = false;
    /** @var  Route */
    protected $currentRoute;

    /**
     * @var Request
     */
    protected $request;

    function __construct(Request $request = null)
    {
        if (!is_null($request)) {
            $this->setRequest($request);
        }
        $this->routes = new Collection();
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        if (is_null($this->request)) {
            $this->request = new Request();
        }

        return $this->request;
    }

    /**
     * @return Collection|Route[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    public function setRoute($route, $name = null)
    {
        if (!$route instanceof Route) {
            if (Route::isShort($route)) {
                $route = Route::shortNormalize($route);
            }
            if (!is_null($name) && !is_numeric($name) && !isset($route["id"])) {
                $route["id"] = $name;
            }
            $route = new Route($route);
        }
        $this->routes[$route->getId()] = $route;
    }

    public function getRoute($name)
    {
        return $this->getRoutes()[$name];
    }

    /**
     * @param array $routes
     */
    public function setRoutes(array $routes)
    {
        foreach ($routes as $name => $route) {
            $this->setRoute($route, $name);
        }
    }

    public function getPatternsTree()
    {
        $routesTree = [];
        foreach ($this->getRoutes() as $route) {
            $methods = $route->getMethods();
            foreach ($methods as $method) {
                $routesTree[$method][$route->getType()][] = $route;
            }
        }
        //sort by types
        foreach ($routesTree as $method => &$types) {
            ksort($types);
            foreach ($types as $type => &$routes) {
                switch ($type) {
                    case RoutePattern::TYPE_FULL:
                    case RoutePattern::TYPE_FIRST_PREFIX :
                    case RoutePattern::TYPE_PREFIX: {
                        usort($routes, function (Route $a, Route $b) {
                            /** @var Route $a */
                            /** @var Route $b */
                            $la = $a->getMaxLength();
                            $lb = $b->getMaxLength();
                            if ($la === $lb) {
                                return 0;
                            }

                            return ($la > $lb) ? -1 : 1;
                        });
                        break;
                    }
                }
            }
        }

        return $routesTree;
    }

    public function isMatchByType($value, $pattern, $type = self::TYPE_FULL, array &$matches = [])
    {
        switch ($type) {
            case RoutePattern::TYPE_FULL :
                return (string)$value === (string)$pattern;
                break;
            case RoutePattern::TYPE_PREFIX:
            case RoutePattern::TYPE_FIRST_PREFIX:
                return strpos($value, $pattern) === 0;
                break;
            case RoutePattern::TYPE_REGEXP:
                //prepare pattern
                $pattern = RegexpUtils::addDelimiters($pattern);
                $compare = preg_match($pattern, $value, $matches);
                if ($compare === false) {
                    throw new \InvalidArgumentException("Bad regexp");
                }

                return $compare;
                break;
            case RoutePattern::TYPE_PARAMS: {
                if (!$value instanceof Url\Query && !$pattern instanceof Url\Query) {
                    throw new \InvalidArgumentException("Value and pattern mast be type Query for pattern type " . RoutePattern::TYPE_PARAMS);
                }
                if ($pattern->count() > $value->count()) {
                    return false;
                }
                $compare = false;
                foreach ($pattern as $name => $valueParam) {
                    if (!isset($value[$name]) || (string)$value[$name] !== (string)$valueParam) {
                        return false;
                    }
                    $compare = true;
                }

                return $compare;
            }
            default:
                throw new \InvalidArgumentException("This type compare not realised");
        }
    }

    public function isMatch(Route $route, Url $url, array &$matches = [])
    {
        $match = false;
        foreach ($route->getPatterns() as $pattern) {
            switch ($pattern->getPart()) {
                case RoutePattern::PART_DOMAIN:
                    $match = $this->isMatchByType($url->getDomain(), $pattern->getValue(), $pattern->getType(),
                        $matches);
                    break;
                case RoutePattern::PART_PATH:
                    $match = $this->isMatchByType($url->getPath(), $pattern->getValue(), $pattern->getType(), $matches);
                    break;
                case RoutePattern::PART_QUERY:
                    $urlValue = $url->getQuery();
                    $match = $this->isMatchByType($urlValue, $pattern->getValue(), $pattern->getType(), $matches);
                    break;
                default:
                    throw new \InvalidArgumentException("This type compare by part not realised");
            }
            if ($match === false) {
                return false;
            }
        }

        return $match;
    }

    public function exec(Route $route, $params = [])
    {
        $args = (array)$route->getArgs();
        if (!empty($params)) {
            $args[] = $params;
        }
        if (!empty($args)) {
            return call_user_func_array($route->getAction(), $args);
        } else {
            return call_user_func($route->getAction());
        }

    }

    public function getCurrentUrl()
    {
        return $this->getRequest()->getUrl();
    }

    /**
     * @return Route
     */
    public function getCurrentRoute()
    {
        return $this->currentRoute;
    }

    /**
     * @param Route $currentRoute
     */
    public function setCurrentRoute($currentRoute)
    {
        $this->currentRoute = $currentRoute;
    }

    public function run()
    {

        if ($this->getRoutes()->isEmpty()) {
            throw new \RuntimeException("In this router urls is not defined");
        }

        if ($this->isRun) {
            return;
        } //fix double run
        $this->isRun = true;

        $currentMethod = $this->getRequest()->getMethod();
        $currentUrl = $this->getCurrentUrl();
        $routes = $this->getPatternsTree();

        $workedMethods = [];
        if (isset($routes[$currentMethod]) && count($routes[$currentMethod]) > 0) {
            $workedMethods[$currentMethod] = $routes[$currentMethod];
        }
        if (isset($routes[Route::METHOD_ALL]) && count(isset($routes[Route::METHOD_ALL])) > 0) {
            $workedMethods[Route::METHOD_ALL] = $routes[Route::METHOD_ALL];
        }

        $processed = false;
        foreach ($workedMethods as $method => $types) {
            foreach ($types as $type => $routes) {
                /** @var Route[] $routes */
                foreach ($routes as $route) {
                    $matches = [];
                    if ($this->isMatch($route, $currentUrl, $matches)) {
                        $this->currentRoute = $route;
                        $matches = array_filter($matches, function ($key) {
                            return !is_integer($key);
                        }, ARRAY_FILTER_USE_KEY);
                        $runResult = $this->exec($route, $matches);
                        $processed = true;
                        if ($runResult !== self::RUN_NEXT) {
                            $this->isRun = false;

                            return $runResult;
                            break;
                        }
                    }
                }
            }
        }
        $this->isRun = false;

        if (!$processed) {
            throw new NotFoundException();
        }
    }

    function __invoke()
    {
        return $this->run();
    }

    public function getUrl($id, array $params = [])
    {
        $routes = $this->getRoutes();
        if (!isset($routes[$id])) {
            throw new \InvalidArgumentException("Not found router with id $id");
        }
        $route = $routes[$id];

        return $route->getUrl($params);
    }
}
