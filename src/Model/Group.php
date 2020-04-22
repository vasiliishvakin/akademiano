<?php

namespace Akademiano\UserEO\Model;


use Akademiano\Entity\GroupInterface;
use Akademiano\Entity\NamedEntity;
use Akademiano\Entity\UserInterface;

class Group extends NamedEntity implements GroupInterface
{
    public function getOwner(): ?UserInterface
    {
        return null;
    }
}
