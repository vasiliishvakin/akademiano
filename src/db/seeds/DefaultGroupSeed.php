<?php

use Phinx\Seed\AbstractSeed;

class DefaultGroupSeed extends AbstractSeed
{
    const DEFAULT_USER_GROUP_TITLE = "users";
    const DEFAULT_ADMIN_GROUP_TITLE = "admins";
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        /** @var \Akademiano\Core\Application $app */
        $app = include __DIR__ . "/../../vendor/akademiano/core/src/bootstrap.php";
        /** @var \Akademiano\Config\ConfigLoader $configLoader */
        $configLoader = $app->getDiContainer()["baseConfigLoader"];
        $configLoader->addConfigDir(__DIR__ . '/../../src/config', PHP_INT_MAX);
        $app->init();
        /** @var \Akademiano\UserEO\Api\v1\GroupsApi $groupsApi */
        $groupsApi = $app->getDiContainer()["groupsApi"];
        $groupsApi->save(["title" => self::DEFAULT_ADMIN_GROUP_TITLE]);
        $groupsApi->save(["title" => self::DEFAULT_USER_GROUP_TITLE]);
    }
}
