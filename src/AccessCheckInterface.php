<?php

namespace Akademiano\Acl;


use Akademiano\Entity\GroupInterface;
use Akademiano\Entity\UserInterface;

interface AccessCheckInterface
{
    public function accessCheck($resource, GroupInterface $group, UserInterface $user = null, UserInterface $owner = null);
}
