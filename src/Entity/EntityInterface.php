<?php


namespace EntityOperator\Entity;


interface EntityInterface
{
    public function getId();

    public function getCreated();

    public function getChanged();

    public function isActive();
}
