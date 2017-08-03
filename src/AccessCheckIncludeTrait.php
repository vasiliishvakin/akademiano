<?php

namespace Akademiano\Acl;

use Akademiano\Entity\GroupInterface;
use Akademiano\Entity\UserInterface;

trait AccessCheckIncludeTrait
{
    /** @var  AccessCheckInterface */
    protected $aclManager;

    /** @var bool  */
    protected $disabledAccessCheck = false;

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

    /**
     * @return bool
     */
    public function isDisabledAccessCheck()
    {
        return $this->disabledAccessCheck;
    }

    /**
     * @param bool $disableAccessCheck
     */
    public function setDisabledAccessCheck($disableAccessCheck)
    {
        $this->disabledAccessCheck = $disableAccessCheck;
    }

    public function disabledAccessCheck()
    {
        $this->setDisabledAccessCheck(true);
    }

    public function enableAcessCheck()
    {
        $this->setDisabledAccessCheck(false);
    }

    public function accessCheck($resource = null, UserInterface $owner = null, GroupInterface $group = null, UserInterface $user = null)
    {
        if ($this->isDisabledAccessCheck()) {
            return true;
        }
        return $this->getAclManager()->accessCheck($resource, $owner, $group, $user);
    }
}
