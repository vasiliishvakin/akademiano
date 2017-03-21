<?php


namespace Akademiano\Entity;


interface EntityInterface extends BaseEntityInterface
{
    public function setCreated($date);

    /**
     * @return \DateTime
     */
    public function getCreated();

    /**
     * @param \DateTime|string $date
     */
    public function setChanged($date);

    /**
     * @return \DateTime
     */
    public function getChanged();

    public function isExistingEntity();

    /**
     * @param boolean $existing
     */
    public function setExistingEntity($existing = true);

    /**
     * @return bool
     */
    public function isActive();

    public function getOwner();

    public function setOwner($owner);
}
