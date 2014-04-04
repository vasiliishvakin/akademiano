<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Acl\Model;


class AclManager 
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @param \Model\UserManager $userManager
     */
    public function setUserManager($userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return \Model\UserManager
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    public function isAllow($resource, User $user = null)
    {
        if (is_null($user)) {
            $user = $this->getUserManager()->getCurrentUser();
            if (!$user) {
                return false;
            }
        }
        return true;
    }

} 