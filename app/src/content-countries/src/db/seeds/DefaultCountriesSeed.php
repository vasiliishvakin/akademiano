<?php

use Phinx\Seed\AbstractSeed;

class DefaultCountriesSeed extends AbstractSeed
{
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
        $api = $app->getDiContainer()[\Akademiano\Content\Countries\Api\v1\CountriesApi::API_ID];
        $api->save(["title" => "Вьетнам"]);
        $api->save(["title" => "Таиланд"]);
        $api->save(["title" => "Малазия"]);
    }
}
