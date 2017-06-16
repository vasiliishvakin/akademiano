<?php

namespace Akademiano\Acl;


use Akademiano\Entity\GroupInterface;
use Akademiano\Entity\UserInterface;

interface AccessCheckIncludeInterface
{
    public function setAclManager(AccessCheckInterface $aclManager);

    public function accessCheck($resource = null, UserInterface $owner = null, GroupInterface $group = null, UserInterface $user = null);
}
