<?php


namespace EntityOperator\Entity;


interface NamedEntityInterface extends EntityInterface
{
    public function getTitle();
    public function getDescription();
}
