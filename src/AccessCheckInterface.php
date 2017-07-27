<?php

namespace Akademiano\Acl;


use Akademiano\Entity\GroupInterface;
use Akademiano\Entity\UserInterface;

interface AccessCheckInterface
{
    public function accessCheck($resource, UserInterface $owner = null, GroupInterface $group = null, UserInterface $user = null);
}
