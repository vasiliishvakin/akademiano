<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */
return [
    'userManager' => function ($c) {
        $userManager = new \User\Model\UserManager();
        $userManager->setSession($c['sessions']);
        if (isset($c["fileManager"])) {
            $userManager->setFileManager($c["fileManager"]);
        }
        $gm = $c["groupManager"];
        $userManager->setGroupManager($gm);

        return $userManager;
    },
    "groupManager" => function ($c) {
        /** @var \DictDir\Model\DirectoryFactory $dm */
        $dm = $c["directoryFactory"];
        $dm->addTable("groups");
        $gm = $dm->getManager("groups");
        return $gm;
    },
    "userProvidersManager" => function ($c) {
        /** @var \DeltaCore\Application $c */
        $manager = new \User\Model\UserProvidersManager();
        $manager->setUserManager($c->lazyGet("userManager"));

        return $manager;
    }
];