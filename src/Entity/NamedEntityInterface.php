<?php


namespace DeltaPhp\Operator\Entity;


interface NamedEntityInterface extends EntityInterface
{
    public function getTitle();
    public function getDescription();
}
