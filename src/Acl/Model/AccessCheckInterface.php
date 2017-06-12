<?php


namespace Acl\Model;

use User\Model\User;

interface AccessCheckInterface
{
    /**
     * @return User
     */
    public function getCurrentUser();

    /**
     * @param integer $owner
     * @param string $resource
     * @param User|null $user
     * @return bool
     */
    public function checkAccess($owner = null, $resource = null, User $user = null);
}
