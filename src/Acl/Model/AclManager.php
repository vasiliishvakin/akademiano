<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Acl\Model;

use Acl\Model\Adapter\AdapterInterface;
use User\Model\UserManager;
use User\Model\User;


class AclManager
{
    /** @var  UserManager */
    protected $userManager;

    /** @var  AdapterInterface */
    protected $aclAdapter;

    /**
     * @param UserManager $userManager
     */
    public function setUserManager($userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManager
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * @return AdapterInterface
     */
    public function getAclAdapter()
    {
        return $this->aclAdapter;
    }

    /**
     * @param AdapterInterface $aclAdapter
     */
    public function setAclAdapter($aclAdapter)
    {
        $this->aclAdapter = $aclAdapter;
    }

    public function isAllow($resource, User $user = null, $owner = null)
    {
        if (is_null($user)) {
            $user = $this->getUserManager()->getCurrentUser();
        }
        $group = $user->getGroup();
        return $this->getAclAdapter()->isAllow($group ? $group->getName() : 'user', $resource, $user->getId(), $owner);
    }

} 