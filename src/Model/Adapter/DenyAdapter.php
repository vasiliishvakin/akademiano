<?php

namespace Akademiano\Acl\Model\Adapter;


use Akademiano\Entity\GroupInterface;
use Akademiano\Entity\UserInterface;

class DenyAdapter implements AdapterInterface
{
    public function accessCheck($resource, GroupInterface $group, UserInterface $user = null, UserInterface $owner = null)
    {
        return false;
    }

}
