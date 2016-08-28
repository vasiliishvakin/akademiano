<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace DeltaDb\Model\Relations;


use DeltaCore\Prototype\AbstractEntity;
use DeltaDb\EntityInterface;
use DeltaDb\Repository;

class mnRelation extends AbstractEntity implements EntityInterface
{
    protected $firstItem;
    protected $secondItem;

    /** @var  Repository */
    protected $firstManager;
    /** @var  Repository */
    protected $secondManager;

    /**
     * @return mixed
     */
    public function getFirstItem()
    {
        if (!empty($this->firstItem) && !is_object($this->firstItem)) {
            $this->firstItem = $this->getFirstManager()->findById($this->firstItem);
        }
        return $this->firstItem;
    }

    /**
     * @param mixed $firstItem
     */
    public function setFirstItem($firstItem)
    {
        $this->firstItem = $firstItem;
    }

    /**
     * @return Repository
     */
    public function getFirstManager()
    {
        return $this->firstManager;
    }

    /**
     * @param Repository $firstManager
     */
    public function setFirstManager($firstManager)
    {
        $this->firstManager = $firstManager;
    }

    /**
     * @return mixed
     */
    public function getSecondItem()
    {
        if (!empty($this->secondItem) && !is_object($this->secondItem)) {
            $this->secondItem = $this->getSecondManager()->findById($this->secondItem);
        }
        return $this->secondItem;
    }

    /**
     * @param mixed $secondItem
     */
    public function setSecondItem($secondItem)
    {
        $this->secondItem = $secondItem;
    }

    /**
     * @return Repository
     */
    public function getSecondManager()
    {
        return $this->secondManager;
    }

    /**
     * @param Repository $secondManager
     */
    public function setSecondManager($secondManager)
    {
        $this->secondManager = $secondManager;
    }
} 
