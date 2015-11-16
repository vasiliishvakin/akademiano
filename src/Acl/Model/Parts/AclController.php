<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Acl\Model\Parts;


trait AclController
{
    public function isAllow($resource = null, User $user= null)
    {
        $app = $this->getApplication();
        /** @var AclManager $aclManager */
        $aclManager = $app['aclManager'];
        if (!$resource) {
            /** @var \HttpWarp\Request $request */
            $request = $app['request'];
            $resource = (string) $request->getUrl()->getPath();
        }
        if (!$user) {
            $user = $this->getCurrentUser();
        }
        return $aclManager->isAllow($resource, $user);
    }

    /**
     * @return User|null
     */
    public function getCurrentUser()
    {
        $app = $this->getApplication();
        /** @var UserManager $userManager */
        $userManager = $app['userManager'];
        return $userManager->getCurrentUser();
    }
} 