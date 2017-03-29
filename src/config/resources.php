<?php

return [
    "menuManager" => function (\Pimple\Container $c) {
        /** @var \Akademiano\Router\Router $router */
        $router = $c["router"];

        /** @var \Akademiano\Config\ConfigLoader $configLoader */
        $configLoader = $c["configLoader"];
        $menuConfig = $configLoader->getConfig(\Akademiano\Menu\Model\MenuManager::NAME_CONFIG);

        $manager = new \Akademiano\Menu\Model\MenuManager($menuConfig, $router);

        return $manager;
    },
];
