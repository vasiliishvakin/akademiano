<?php

namespace Akademiano\User;


use Akademiano\Entity\NamedEntity;

class GuestGroup extends NamedEntity implements GuestGroupInterface
{
    protected $id = null;
    protected $name = "guest";
}
