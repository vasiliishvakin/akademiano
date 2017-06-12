<?php


namespace Acl\Model\Parts;


use Acl\Model\AclManager;
use HttpWarp\Request;
use User\Model\UserManager;
use User\Model\User;

trait AccessCheckTrait
{
    /** @var  AclManager */
    protected $aclManager;

    /** @var  UserManager */
    protected $userManager;

    /** @var  Request */
    protected $request;

    /**
     * @return AclManager
     */
    public function getAclManager()
    {
        return $this->aclManager;
    }

    /**
     * @param AclManager $aclManager
     */
    public function setAclManager($aclManager)
    {
        $this->aclManager = $aclManager;
    }

    /**
     * @return UserManager
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * @param UserManager $userManager
     */
    public function setUserManager($userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function getResource()
    {
        $resource = (string)$this->getRequest()->getUrl()->getPath();
        return $resource;
    }

    /**
     * @return \User\Model\User
     */
    public function getCurrentUser()
    {
        return $this->getUserManager()->getCurrentUser();
    }

    public function checkAccess($owner = null, $resource = null, User $user = null)
    {
        if (null === $resource) {
            $resource = $this->getResource();
        }
        if (null === $user) {
            $user = $this->getCurrentUser();
        }
        return $this->getAclManager()->isAllow($resource, $user, $owner);
    }
}
