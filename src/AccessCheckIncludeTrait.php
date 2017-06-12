<?php

namespace Akademiano\Acl;

use Akademiano\Entity\GroupInterface;
use Akademiano\Entity\UserInterface;

trait AccessCheckIncludeTrait
{
    /** @var  AccessCheckInterface */
    protected $aclManager;

    /**
     * @return AccessCheckInterface
     */
    public function getAclManager()
    {
        return $this->aclManager;
    }

    /**
     * @param AccessCheckInterface $aclManager
     */
    public function setAclManager(AccessCheckInterface $aclManager)
    {
        $this->aclManager = $aclManager;
    }

    public function accessCheck($resource = null, GroupInterface $group = null, UserInterface $user = null, UserInterface $owner = null)
    {
        return $this->getAclManager()->accessCheck($resource, $group, $user, $owner);
    }
}
