<?php

namespace Akademiano\Menu\Model;

use Akademiano\HttpWarp\Environment;
use Akademiano\HttpWarp\EnvironmentIncludeInterface;
use Akademiano\HttpWarp\Parts\EnvironmentIncludeTrait;
use Akademiano\Router\Router;
use Akademiano\Utils\Object\Collection;


class Menu implements \Countable, EnvironmentIncludeInterface
{
    use EnvironmentIncludeTrait;

    protected $name;
    /** @var Item[]|Collection */
    protected $items;

    /** @var  Router */
    protected $router;

    /** @var  Item */
    protected $activeItem;

    protected $isSortedItems = false;

    public function __construct($name = null, $router = null, Environment $environment = null)
    {
        $this->items = new Collection();
        if (null !== $name) {
            $this->setName($name);
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
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Item[]
     */
    public function getItems()
    {
        if (!$this->activeItem) {
            $this->getActiveItem();
        }
        $this->sort();
        return $this->items;
    }

    /**
     * @param Item[] $items
     */
    public function setItems(array $items)
    {
        $this->items = new Collection();
        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    public function addItem(Item $item)
    {
        $this->items[$item->getId()] = $item;
        $this->isSortedItems = false;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return count($this->items);
    }

    public function getActiveItem()
    {
        if (is_null($this->activeItem)) {
            $this->sort();
            foreach ($this->items as $item) {
                if ($item->isEquivRoute($this->getRouter()->getCurrentRoute()) || $item->isEquivUrl($this->getRouter()->getCurrentUrl())) {
                    $item->setActive();
                    $this->activeItem = $item;
                    break;
                }
            }
        }
        return $this->activeItem;
    }

    public function resetActiveItem()
    {
        if ($this->activeItem) {
            $this->activeItem->setActive(false);
            unset($this->activeItem);
        }
    }

    public function sort()
    {
        if (!$this->isSortedItems) {
            $this->items->usort(
                function (Item $a, Item $b) {
                    $lA = $a->getOrder();
                    $lB = $b->getOrder();
                    if ($lA == $lB) {
                        return 0;
                    }
                    return ($lA < $lB) ? -1 : 1;
                });
            $this->isSortedItems = true;
        }
    }

    public function createItem(array $data, Router $router = null, Environment $environment = null)
    {
        if (null === $router) {
            $router = $this->getRouter();
        }
        if (null === $environment) {
            $environment = $this->getEnvironment();
        }
        $item = new Item($data, $router, $environment);
        return $item;
    }
}
