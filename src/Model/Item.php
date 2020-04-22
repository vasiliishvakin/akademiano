<?php

namespace Akademiano\Menu\Model;


use Akademiano\HttpWarp\Environment;
use Akademiano\HttpWarp\EnvironmentIncludeInterface;
use Akademiano\HttpWarp\Parts\EnvironmentIncludeTrait;
use Akademiano\Router\Router;
use Akademiano\Router\Route;
use Akademiano\HttpWarp\Url;


class Item implements EnvironmentIncludeInterface
{
    use EnvironmentIncludeTrait;

    protected $id;
    protected $text;
    protected $title;
    /** @var  Router */
    protected $router;
    /** @var  Route */
    protected $route;
    /** @var Route[]|[] */
    protected $subRoutes = [];
    protected $routeParams = [];
    /** @var  Url */
    protected $url;
    protected $order = 0;
    protected $active = false;

    public function __construct($data = null, Router $router = null, Environment $environment = null)
    {
        if (null !== $router) {
            $this->setRouter($router);
        }
        if (null !== $environment) {
            $this->setEnvironment($environment);
        }
        if ($data) {
            foreach ($data as $name => $value) {
                $method = "set" . ucfirst($name);
                if (method_exists($this, $method)) {
                    $this->{$method}($value);
                }
            }
        }
    }

    public function getId()
    {
        if (is_null($this->id)) {
            $this->id = ($this->getRoute() instanceof Route) ? $this->getRoute()->getId() : $this->getUrl()->getId();
        }

        return $this->id;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param Router $router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        if (!$this->route instanceof Route && !is_null($this->route)) {
            $this->route = $this->getRouter()->getRoute($this->route);
        }

        return $this->route;
    }

    /**
     * @param Route|string $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return mixed
     */
    public function getSubRoutes()
    {
        return $this->subRoutes;
    }

    public function addSubRoute($route)
    {
        if (!$route instanceof Route) {
            $route = $this->getRouter()->getRoute($route);
        }
        $this->subRoutes[$route->getId()] = $route;
    }

    /**
     * @param mixed $subRoutes
     */
    public function setSubRoutes(array $subRoutes)
    {
        $this->subRoutes = [];
        foreach ($subRoutes as $route) {
            $this->addSubRoute($route);
        }
    }

    /**
     * @return mixed
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * @param mixed $routeParams
     */
    public function setRouteParams(array $routeParams)
    {
        $this->routeParams = $routeParams;
    }

    /**
     * @return Url
     */
    public function getUrl()
    {
        if (is_null($this->url)) {
            if (is_null($this->getRoute())) {
                throw new \LogicException("Not set url or route for menu item");
            }
            $this->url = $this->getRoute()->getUrl($this->getRouteParams());
        } elseif (is_string($this->url)) {
            $this->url = new Url($this->url, $this->getEnvironment());
        }

        return $this->url;
    }

    /**
     * @param Url|string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @param $link
     * @deprecated
     */
    public function setLink($link)
    {
        $this->setUrl($link);
    }

    /**
     * @return string
     * @deprecated
     */
    public function getLink()
    {
        return (string)$this->getUrl();
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->id = null;
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        if (empty($this->title)) {
            return $this->getText();
        }
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder($order)
    {
        $this->order = (integer)$order;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active = true)
    {
        $this->active = $active;
    }

    public function isEquivRoute(Route $route)
    {
        if (null !== $this->getRoute() && $this->getRoute()->getId() === $route->getId()) {
            return true;
        } else {
            foreach ($this->getSubRoutes() as $subRoute) {
                if ($route->getId() === $subRoute->getId()) {
                    return true;
                }
            }
        }
    }

    public function isEquivUrl(Url $url)
    {
        return $url->getId() === $this->getUrl()->getId();
    }
}
