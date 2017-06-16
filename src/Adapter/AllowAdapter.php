<?php

namespace Akademiano\Acl\Adapter;


use Akademiano\Entity\GroupInterface;
use Akademiano\Entity\UserInterface;

class AllowAdapter implements AdapterInterface
{
    public function accessCheck($resource, UserInterface $owner = null, GroupInterface $group, UserInterface $user = null)
    {
        return true;
    }

}
