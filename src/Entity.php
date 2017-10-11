<?php


namespace Akademiano\Entity;


use Carbon\Carbon;

class Entity extends BaseEntity implements EntityInterface
{
    /** @var  \DateTime */
    protected $created;
    /** @var  \DateTime */
    protected $changed;
    /** @var  boolean */
    protected $active = true;

    protected $owner;

    /** @var  boolean */
    protected $existingEntity = false;

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        if (null !== $this->created && !$this->created instanceof Carbon) {
            if ($this->created instanceof \DateTime) {
                $this->created = Carbon::instance($this->created);
            } else {
                $this->created = new Carbon($this->created);
            }
        }
        return $this->created;
    }

    /**
     * @param \DateTime|string $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getChanged()
    {
        if (null !== $this->changed && !$this->changed instanceof Carbon) {
            if ($this->changed instanceof \DateTime) {
                $this->changed = Carbon::instance($this->changed);
            } else {
                $this->changed = new Carbon($this->changed);
            }
        }
        return $this->changed;
    }

    /**
     * @param \DateTime|string $changed
     */
    public function setChanged($changed)
    {
        $this->changed = $changed;
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
        if ($active === "t" || $active === "true") {
            $active = true;
        } elseif ($active === "f" || $active === "false") {
            $active = false;
        }
        $this->active = (boolean)$active;
    }

    public function isExistingEntity()
    {
        return $this->existingEntity;
    }

    public function setExistingEntity($existing = true)
    {
        $this->existingEntity = $existing;
    }

    /**
     * @return UserInterface
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    public function toArray()
    {
        $data = parent::toArray();
        $data['created'] = $this->getCreated();
        $data['changed'] = $this->getChanged();
        $data['active'] = $this->isActive();
        $data['owner'] = $this->getOwner();
        return $data;
    }


}
