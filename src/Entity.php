<?php


namespace Akademiano\Entity;


use Akademiano\Delegating\DelegatingTrait;
use Carbon\Carbon;

class Entity extends BaseEntity implements EntityInterface
{
    use DelegatingTrait;

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
    public function getCreated(): \DateTime
    {
        if (null !== $this->created && !$this->created instanceof Carbon) {
            $this->created = Carbon::make($this->created);
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
    public function getChanged(): \DateTime
    {
        if (null !== $this->changed && !$this->changed instanceof Carbon) {
            $this->changed = Carbon::make($this->changed);
        } else {
            $this->changed = $this->getCreated();
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
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        if (is_string($active)) {
            if ($active === "t" || $active === "true") {
                $active = true;
            } elseif ($active === "f" || $active === "false") {
                $active = false;
            }
        }
        $this->active = (boolean)$active;
    }

    public function isExistingEntity(): bool
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
    public function getOwner(): ?UserInterface
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

    protected function toValuesArray(): array
    {
        $data = parent::toValuesArray();
        $data['created'] = $this->getCreated();
        $data['changed'] = $this->getChanged();
        $data['active'] = $this->isActive();
        $data['owner'] = $this->getOwner()->getId()->getInt();
        return $data;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['created'] = $this->getCreated();
        $data['changed'] = $this->getChanged();
        $data['active'] = $this->isActive();
        $data['owner'] = $this->getOwner();
        return $data;
    }
}
