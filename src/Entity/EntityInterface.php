<?php


namespace DeltaPhp\Operator\Entity;


interface EntityInterface extends ExistingEntityInterface
{
    public function getId();

    public function getCreated();

    public function getChanged();

    public function isActive();
}
