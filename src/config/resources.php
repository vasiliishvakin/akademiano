<?php

use Akademiano\Menu\Model\MenuManager;

return [
    MenuManager::RESOURCE_ID => function (\Pimple\Container $c) {
        /** @var \Akademiano\Router\Router $router */
        $router = $c["router"];

        /** @var \Akademiano\Config\ConfigLoader $configLoader */
        $configLoader = $c["configLoader"];
        $menuConfig = $configLoader->getConfig(\Akademiano\Menu\Model\MenuManager::NAME_CONFIG);

        $manager = new MenuManager($menuConfig, $router);

        return $manager;
    },
];
