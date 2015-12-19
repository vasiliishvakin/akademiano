<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */
return [
    "menuManager" => function ($c) {
        $manager = new \SiteMenu\Model\MenuManager();
        $manager->setModuleManager($c["moduleManager"]);
        $manager->setConfigLoader($c->getConfigLoader());
        $manager->setRouter($c["router"]);
        $manager->setAclManager($c["aclManager"]);
        $manager->setRouter($c["router"]);

        return $manager;
    },
];
