<?php


namespace Akademiano\Api\v1\Items;


use Akademiano\Utils\Exception\ReadOnlyAttemptChange;
use Akademiano\Utils\Object\Collection;
use Akademiano\Utils\Object\Prototype\ArrayableInterface;
use Akademiano\Utils\Paging\PagingMetadata;

class ItemsPage implements \ArrayAccess, \Iterator, \Countable, \JsonSerializable, ArrayableInterface
{
    /** @var  Collection */
    protected $items;

    /** @var  PagingMetadata */
    protected $pageMetadata;

    protected $array;

    /**
     * ItemsPage constructor.
     * @param Collection $items
     * @param PagingMetadata $pageMetadata
     */
    public function __construct(Collection $items, PagingMetadata $pageMetadata)
    {
        $this->items = $items;
        $this->pageMetadata = $pageMetadata;
    }

    /**
     * @return Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return PagingMetadata
     */
    public function getPageMetadata()
    {
        return $this->pageMetadata;
    }

    public function toArray()
    {
        if (null === $this->array) {
            $this->array = [
                "items" => $this->getItems()->toArray(),
                "pageMetadata" => $this->getPageMetadata()->toArray(),
            ];
        }
        return $this->array;
    }

    public function current()
    {
        return $this->getItems()->current();
    }

    public function next()
    {
        $this->getItems()->next();
    }

    public function key()
    {
        return $this->getItems()->key();
    }

    public function valid()
    {
        return $this->getItems()->valid();
    }

    public function rewind()
    {
        $this->getItems()->rewind();
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->toArray());
    }

    public function offsetGet($offset)
    {
        return $this->toArray()[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new ReadOnlyAttemptChange("Could not set value for $offset in " . __CLASS__);
    }

    public function offsetUnset($offset)
    {
        throw new ReadOnlyAttemptChange("Could not unset value for $offset in " . __CLASS__);
    }

    public function count()
    {
        return $this->getItems()->count();
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
