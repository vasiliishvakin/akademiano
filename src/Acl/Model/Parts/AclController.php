<?php

namespace Acl\Model\Parts;


trait AclController
{
    use AccessCheckTrait;

    /**
     * @return \DeltaCore\Application
     */
    abstract public function getApplication();

    /**
     * @return \Acl\Model\AclManager
     */
    public function getAclManager()
    {
        if (null === $this->aclManager) {
            $this->aclManager = $this->getApplication()["aclManager"];
        }
        return $this->aclManager;
    }

    public function getRequest()
    {
        if (null === $this->request) {
            $this->request = $this->getApplication()["request"];
        }
        return $this->request;
    }

    public function getUserManager()
    {
        if (null === $this->userManager) {
            $this->userManager = $this->getApplication()["userManager"];
        }
        return $this->userManager;
    }
}
