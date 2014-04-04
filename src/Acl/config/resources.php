<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */
return [
    'aclManager'       => function ($c) {
            $aclManager = new \Acl\Model\AclManager();
            $aclManager->setUserManager($c['userManager']);
            return $aclManager;
        },
];