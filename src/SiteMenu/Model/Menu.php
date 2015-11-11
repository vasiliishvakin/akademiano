<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace SiteMenu\Model;


use DeltaCore\Parts\MagicSetGetManagers;
use DeltaCore\Prototype\MagicMethodInterface;
use DeltaRouter\Router;

/**
 * Class Menu
 * @package SiteMenu\Model
 * @method setAclManager(\Acl\Model\AclManager $manager)
 * @method \Acl\Model\AclManager getAclManager()
 */
class Menu implements \Countable, MagicMethodInterface
{
    use MagicSetGetManagers;

    protected $name;
    /** @var Item[] */
    protected $items = [];

    /** @var  Router */
    protected $router;

    /** @var  Item */
    protected $activeItem;

    protected $isSortedItems = false;

    function __construct($name = null, $router = null)
    {
        if (!is_null($name)) {
            $this->setName($name);
        }
        if (!is_null($router)) {
            $this->setRouter($router);
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
    public function setRouter($router)
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
        if (!$this->isSortedItems) {
            $this->sortItems();
        }
        return $this->items;
    }

    /**
     * @param Item[] $items
     */
    public function setItems(array $items)
    {
        $this->items = [];
        foreach($items as $item) {
            $this->addItem($item);
        }
    }

    public function addItem(Item $item)
    {
        $item->setAclManager($this->getAclManager());
        $this->items[$item->getId()] = $item;
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
            $items = $this->items;
            usort($items, function ($a, $b) {
                $lA = strlen((string)$a->getUrl());
                $lB = strlen((string)$b->getUrl());
                if ($lA == $lB) {
                    return 0;
                }
                return ($lA > $lB) ? -1 : 1;
            });
            foreach ($items as $item) {
                if ((null !== $item->getRoute() && $this->getRouter()->getCurrentRoute()->getId() === $item->getId())
                || ($this->getRouter()->getCurrentUrl()->getId() === $item->getId())) {
                    $item->setActive(true);
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

    public function sortItems()
    {
        uasort($this->items, function($a, $b){
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
