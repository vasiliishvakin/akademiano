<?php


namespace Akademiano\Entity;


use Akademiano\Delegating\DelegatingInterface;

interface EntityInterface extends BaseEntityInterface, DelegatingInterface
{
    public function setCreated($date);

    /**
     * @return \DateTime
     */
    public function getCreated():\DateTime;

    /**
     * @param \DateTime|string $date
     */
    public function setChanged($date);

    /**
     * @return \DateTime
     */
    public function getChanged():\DateTime;

    public function isExistingEntity():bool ;

    /**
     * @param boolean $existing
     */
    public function setExistingEntity($existing = true);

    /**
     * @return bool
     */
    public function isActive():bool;

    public function setActive($active);

    public function getOwner():?UserInterface;

    public function setOwner($owner);
}
