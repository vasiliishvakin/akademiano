<?php

namespace Akademiano\Acl\Adapter;


use Akademiano\Entity\UserInterface;
use Akademiano\Entity\GroupInterface;

interface AdapterInterface
{
    public function accessCheck($resource, UserInterface $owner = null, GroupInterface $group , UserInterface $user = null);
}
