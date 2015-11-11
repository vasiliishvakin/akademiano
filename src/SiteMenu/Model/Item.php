<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace SiteMenu\Model;
use DeltaCore\Parts\MagicSetGetManagers;
use DeltaCore\Prototype\MagicMethodInterface;
use DeltaRouter\Route;
use DeltaRouter\Router;
use HttpWarp\Url;

/**
 * Class Item
 * @package SiteMenu\Model
 * @method setAclManager(\Acl\Model\AclManager $manager)
 * @method \Acl\Model\AclManager getAclManager()
 */
class Item implements MagicMethodInterface
{
    use MagicSetGetManagers;

    protected $id;
    protected $text;
    protected $title;
    /** @var  Router */
    protected $router;
    /** @var  Route */
    protected $route;
    protected $routeParams = [];
    /** @var  Url */
    protected $url;
    protected $order = 0;
    protected $active = false;

    function __construct($data = null, Router $router = null)
    {
        if ($router) {
            $this->setRouter($router);
        }
        if ($data) {
            foreach($data as $name=>$value) {
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
    public function setRouter($router)
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
        if (is_null($this->url)){
            if (is_null($this->getRoute())) {
                throw new \LogicException("Not set url or route for menu item");
            }
            $this->url = $this->getRoute()->getUrl($this->getRouteParams());
        } elseif (is_string($this->url)){
            $this->url = new Url($this->url);
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
        return (string) $this->getUrl();
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
        $this->order = (integer) $order;
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
    public function setActive($active)
    {
        $this->active = $active;
    }

    public function isAllow($user = null)
    {
        if ($aclManager = $this->getAclManager()) {
            return $aclManager->isAllow((string) $this->getUrl()->getPath(), $user);
        }
        return true;
    }
}
