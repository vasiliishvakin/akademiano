<?php

use Akademiano\Acl\Adapter\FileBasedAdapterInterface;

return [
    'aclManager' => function (\Pimple\Container $c) {
        $aclManager = new \Akademiano\Acl\AclManager();
        $aclManager->setCustodian($c['custodian']);
        $aclManager->setRequest($c["request"]);
        /** @var \Akademiano\Config\Config $config */
        $config = $c["config"];
        $adapterName = $config->get(["acl", "adapter"], "Akademiano\\Acl\\Adapter\\AllowAdapter");
        /** @var \Akademiano\Acl\Adapter\AdapterInterface $adapter */
        $adapter = new $adapterName;

        if ($adapter instanceof \Akademiano\Acl\Adapter\FileBasedAdapterInterface) {
            /** @var \Akademiano\Config\ConfigLoader $configLoader */
            $configLoader = $c["configLoader"];
            $files = $configLoader->getFiles(FileBasedAdapterInterface::CONFIG_NAME, FileBasedAdapterInterface::FILE_EXT);
            $adapter->setFiles($files);
        }

        $aclManager->setAclAdapter($adapter);
        return $aclManager;
    },
];
