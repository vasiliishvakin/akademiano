<?php


namespace Akademiano\EntityOperator\Entity;


interface ExistingEntityInterface
{
    public function isExistingEntity();

    /**
     * @param boolean $existing
     */
    public function setExistingEntity($existing = true);
}
