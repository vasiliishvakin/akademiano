<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */
return [
    'aclManager' => function ($c) {
        $aclManager = new \Acl\Model\AclManager();
        $aclManager->setUserManager($c['userManager']);
        $adapterName = $c->getConfig(["Acl", "adapter"], "\\Acl\\Model\\Adapter\\AllowAdapter");
        $adapter = new $adapterName;
        if ($adapter instanceof \DeltaCore\ConfigurableInterface) {
            $adapter->setConfig($c->getConfig());
        }
        $aclManager->setAclAdapter($adapter);
        return $aclManager;
    },
];