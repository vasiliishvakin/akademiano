<?php

namespace Akademiano\Menu\Model;

use Akademiano\Config\Config;
use Akademiano\HttpWarp\Environment;
use Akademiano\HttpWarp\EnvironmentIncludeInterface;
use Akademiano\HttpWarp\Parts\EnvironmentIncludeTrait;
use Akademiano\Router\Router;

class MenuManager implements \ArrayAccess, EnvironmentIncludeInterface
{
    use EnvironmentIncludeTrait;

    const NAME_CONFIG = "menu";

    protected $rawMenu;

    /** @var array Menu[] */
    protected $menuStore = [];

    /** @var  Router */
    protected $router;

    public function __construct(Config $menuData = null, Router $router = null, Environment $environment = null)
    {
        if (null !== $menuData) {
            $this->loadMenu($menuData);
        }
        if (null !== $router) {
            $this->setRouter($router);
        }
        if (null !== $environment) {
            $this->setEnvironment($environment);
        }
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
     * @param string $menuName
     * @return Menu
     */
    public function getMenu($menuName)
    {
        if (!key_exists($menuName, $this->menuStore)) {
            $this->menuStore[$menuName] = $this->processMenu($menuName);
        }
        return $this->menuStore[$menuName];
    }

    protected function prepareMenu($menuConfig)
    {
        $menuData = [];
        foreach ($menuConfig as $item) {
            if (isset($item["route"]) || isset($item["link"])) {
                $itemId = isset($item["route"]) ? "__r__" . $item["route"] : "__l__" . $item["link"];
                $menuData[$itemId] = $item;
            }
        }
        return $menuData;
    }

    protected function processMenu($name)
    {
        if (!isset($this->rawMenu[$name]) || empty($this->rawMenu[$name])) {
            return new Menu();
        }
        $menuConfig = $this->prepareMenu($this->rawMenu[$name]);
        $menu = new Menu($name, $this->getRouter(), $this->getEnvironment());

        $i = 0;
        foreach ($menuConfig as $key => $itemData) {
            $i++;
            if (!isset($itemData["order"])) {
                $itemData["order"] = $i;
            }
            $item = $menu->createItem($itemData);
            //check access here

            $menu->addItem($item);
        }
        return $menu;
    }

    public function loadMenu(Config $menuConfig)
    {
        $this->rawMenu = $menuConfig->toArray();
        $this->menuStore = [];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return !is_null($this->getMenu($offset));
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->getMenu($offset);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        return;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        return;
    }
}
