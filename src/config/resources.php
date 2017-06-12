<?php

use Akademiano\Acl\Model\Adapter\FileBasedAdapterInterface;

return [
    'aclManager' => function (\Pimple\Container $c) {
        $aclManager = new \Akademiano\Acl\Model\AclManager();
        $aclManager->setCustodian($c['custodian']);
        /** @var \Akademiano\Config\Config $config */
        $config = $c["config"];
        $adapterName = $config->get(["Acl", "adapter"], "Akademiano\\Acl\\Model\\Adapter\\AllowAdapter");
        /** @var \Akademiano\Acl\Model\Adapter\AdapterInterface $adapter */
        $adapter = new $adapterName;
        if ($adapter instanceof \Akademiano\Config\ConfigurableInterface) {
            $adapter->setConfig($config);
        }

        if ($adapter instanceof \Akademiano\Acl\Model\Adapter\FileBasedAdapterInterface) {
            /** @var \Akademiano\Config\ConfigLoader $configLoader */
            $configLoader = $c["configLoader"];
            $files = $configLoader->getFiles(FileBasedAdapterInterface::CONFIG_NAME, FileBasedAdapterInterface::FILE_EXT);
            $adapter->setFiles($files);
        }

        $aclManager->setAclAdapter($adapter);
        return $aclManager;
    },
];
