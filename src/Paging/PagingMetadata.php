<?php


namespace Akademiano\Utils\Paging;


use Akademiano\Utils\Object\Collection;
use Akademiano\Utils\Object\Prototype\ArrayableInterface;
use Akademiano\Utils\Exception\ReadOnlyAttemptChange;


class PagingMetadata implements \ArrayAccess, \Iterator, \Countable, \JsonSerializable, ArrayableInterface
{
    protected $currentPage;
    protected $itemsCount;
    protected $itemsOffset;
    protected $sliceSize;
    protected $pagesCount;
    protected $isLastPage;
    /** @var  Collection */
    protected $pages;
    protected $array;


    public function __construct($itemsCount, $currentPage = 1, $sliceSize = 10)
    {
        $this->setItemsCount($itemsCount);
        $this->setCurrentPage($currentPage);
        $this->setSliceSize($sliceSize);
    }


    /**
     * @return mixed
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param mixed $currentPage
     */
    protected function setCurrentPage($currentPage)
    {
        $this->currentPage = (int)$currentPage;
    }

    /**
     * @return mixed
     */
    public function getItemsCount()
    {
        return $this->itemsCount;
    }

    /**
     * @param mixed $itemsCount
     */
    protected function setItemsCount($itemsCount)
    {
        $this->itemsCount = (int)$itemsCount;
    }

    /**
     * @return mixed
     */
    public function getItemsOffset()
    {
        if (empty($this->itemsOffset)) {
            if (null === $this->getSliceSize() ) {
                return null;
            }
            $this->itemsOffset = ($this->getCurrentPage() - 1) * $this->getSliceSize();
        }
        return $this->itemsOffset;
    }


    /**
     * @return mixed
     */
    public function getSliceSize()
    {
        return $this->sliceSize;
    }

    /**
     * @param mixed $sliceSize
     */
    protected function setSliceSize($sliceSize)
    {
        if (null !== $sliceSize) {
            $this->sliceSize = (int)$sliceSize;
        }
    }

    /**
     * @return mixed
     */
    public function isCurrentPageFirst()
    {
        return $this->getCurrentPage() === 1;
    }

    /**
     * @return mixed
     */
    public function isCurrentPageLast()
    {
        return $this->getCurrentPage() != $this->getPages()->count();
    }

    public function getPages()
    {
        if (null === $this->pages) {
            $count = ceil($this->getItemsCount() / $this->getSliceSize());
            $pages = [];
            for ($i = 1; $i <= $count; $i++) {
                $pages[$i] = new Page($i, $this->getCurrentPage() === $i);
            }
            $this->pages = new Collection($pages);
        }
        return $this->pages;
    }

    public function toArray()
    {
        if (null === $this->array) {
            $this->array = [
                "currentPage" => $this->getCurrentPage(),
                "itemsOffset" => $this->getItemsOffset(),
                "sliceSize" => $this->getSliceSize(),
                "pagesCount" => $this->count(),
                "isCurrentPageLast" => $this->isCurrentPageLast(),
                "isCurrentPageFirst" => $this->isCurrentPageFirst(),
                "pages" => $this->getPages()->toArray(),
            ];
        }
        return $this->array;
    }

    public function current()
    {
        return $this->getPages()->current();
    }

    public function next()
    {
        $this->getPages()->next();
    }

    public function key()
    {
        return $this->getPages()->key();
    }

    public function valid()
    {
        return $this->getPages()->valid();
    }

    public function rewind()
    {
        $this->getPages()->rewind();
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
        return $this->getPages()->count();
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

}
