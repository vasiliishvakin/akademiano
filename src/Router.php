<?php

namespace Akademiano\Router;

use Akademiano\HttpWarp\Environment;
use Akademiano\HttpWarp\EnvironmentIncludeInterface;
use Akademiano\HttpWarp\Exception\NotFoundException;
use Akademiano\HttpWarp\Parts\EnvironmentIncludeTrait;
use Akademiano\Utils\Object\Collection;
use Akademiano\Utils\RegexpUtils;
use Akademiano\HttpWarp\Request;
use Akademiano\HttpWarp\Url;


class Router implements EnvironmentIncludeInterface
{
    use EnvironmentIncludeTrait;

    const RUN_NEXT = "____run_next";
    const CONFIG_NAME = "routes";

    /** @var array Collection|Route[] */
    protected $routes;
    protected $isRun = false;
    /** @var  Route */
    protected $currentRoute;

    /** @var array Collection|Route[] */
    protected $afterRoutes;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request = null, Environment $environment = null)
    {
        if (null !== $request) {
            $this->setRequest($request);
        }
        if (null !== $environment) {
            $this->setEnvironment($environment);
        }
        $this->routes = new Collection();
        $this->afterRoutes = new Collection();
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

    public function getEnvironment()
    {
        if (null === $this->environment) {
            if ($this->getRequest() instanceof EnvironmentIncludeInterface) {
                $environment = $this->getRequest()->getEnvironment();
                if ($environment instanceof Environment) {
                    $this->setEnvironment($environment);
                }
            }
            if (null === $this->environment) {
                $this->environment = new Environment();
            }
        }
        return $this->environment;
    }

    /**
     * @return Collection|Route[]
     */
    public function getRoutes()
    {
        return $this->routes;
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
            $route->setEnvironment($this->getEnvironment());
        }
        $this->routes[$route->getId()] = $route;
    }

    /**
     * @return Collection|Route[]
     */
    public function getAfterRoutes()
    {
        return $this->afterRoutes;
    }

    public function getAfterRoute($name)
    {
        $routes = $this->getAfterRoutes();
        return isset($routes[$name]) ? $routes[$name] : null;
    }

    /**
     * @param array $routes
     */
    public function setAfterRoutes(array $routes)
    {
        foreach ($routes as $name => $route) {
            $this->setAfterRoute($route, $name);
        }
    }

    public function setAfterRoute($route, $name = null)
    {
        if (!$route instanceof Route) {
            if (Route::isShort($route)) {
                $route = Route::shortNormalize($route);
            }
            if (!is_null($name) && !is_numeric($name) && !isset($route["id"])) {
                $route["id"] = $name;
            }
            $route = new Route($route);
            $route->setEnvironment($this->getEnvironment());
        }
        $this->afterRoutes[$route->getId()] = $route;
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
                    case RoutePattern::TYPE_PREFIX:
                    {
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

        foreach ($this->getAfterRoutes() as $route) {
            $methods = $route->getMethods();
            foreach ($methods as $method) {
                $routesTree[$method][][] = $route;
            }
        }
        return $routesTree;
    }

    public function isMatchByType($value, $pattern, $type = RoutePattern::TYPE_FULL, array &$matches = [])
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
            case RoutePattern::TYPE_PARAMS:
            {
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
        array_unshift($args, $route);
        return call_user_func_array($route->getAction(), $args);
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
        if ($this->isRun) {
            return null;
        } //fix double run
        $this->isRun = true;

        $currentMethod = $this->getRequest()->getMethod();
        $currentUrl = $this->getCurrentUrl();
        $routes = $this->getPatternsTree();

        if (empty($routes)) {
            throw new \RuntimeException("In this router urls is not defined");
        }

        $workedMethods = [];
        if (isset($routes[$currentMethod]) && count($routes[$currentMethod]) > 0) {
            $workedMethods[$currentMethod] = $routes[$currentMethod];
        }
        if (isset($routes[Route::METHOD_ALL]) && count($routes[Route::METHOD_ALL]) > 0) {
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
        return null;
    }

    public function __invoke()
    {
        return $this->run();
    }

    public function getUrl($id, array $params = [])
    {
        $routes = $this->getRoutes();
        if (!isset($routes[$id])) {
            throw new \InvalidArgumentException(sprintf('Not found route with id "%s"', $id));
        }
        $route = $routes[$id];

        return $route->getUrl($params);
    }
}
