<?php
return [
    'aclManager' => function (\Pimple\Container $c) {
        $aclManager = new \Akademiano\Acl\Model\AclManager();
        $aclManager->setCustodian($c['custodian']);
        /** @var \Akademiano\Config\Config $config */
        $config = $c["config"];
        $adapterName = $config->get(["Acl", "adapter"], "Akademiano\\Acl\\Model\\Adapter\\AllowAdapter");
        $adapter = new $adapterName;
        if ($adapter instanceof \Akademiano\Config\ConfigurableInterface) {
            $adapter->setConfig($config);
        }
        $aclManager->setAclAdapter($adapter);
        return $aclManager;
    },
];
