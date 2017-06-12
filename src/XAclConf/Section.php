<?php


namespace Akademiano\Acl\XAclConf;


use Akademiano\Utils\Object\Prototype\ArrayableInterface;

class Section implements ArrayableInterface
{
    protected $name;

    /** @var Item[] */
    protected $items = [];

    /**
     * Section constructor.
     * @param $name
     */
    public function __construct($name = null)
    {
        if ($name) {
            $this->setName($name);
        }
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
        return $this->items;
    }

    /**
     * @param array $items
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }

    public function addItem(Item $item)
    {
        $this->items[$item->getPath()] = $item;
    }

    public function toArray()
    {
        $data = [];
        foreach ($this->getItems() as $item) {
            $data[$item->getPath()] = $item->toArray();
        }
        return $data;
    }

    public function __toString()
    {
        $strItems = [];
        foreach ($this->getItems() as $item) {
            $strItems[] = (string)$item;
        }
        $str = implode(PHP_EOL, $strItems);
        $sectionName = !empty($this->getName()) ? "[" . $this->getName() . "]" . PHP_EOL : "";
        return $sectionName . $str;
    }
}
