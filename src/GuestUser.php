<?php

namespace Akademiano\User;


use Akademiano\Entity\NamedEntity;

class GuestUser extends NamedEntity implements GuestUserInterface
{
    protected $id = 0;
    protected $group = null;
    protected $title = "guest";

    public function getGroup()
    {
        if (null === $this->group) {
            $this->group = new GuestGroup();
        }
        return $this->group;
    }
}
