<?php


namespace Akademiano\Entity;


interface EntityNamedInterface extends EntityInterface
{
    public function getTitle();
    public function getDescription();
}
