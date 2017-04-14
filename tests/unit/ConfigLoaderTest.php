<?php


class ConfigLoaderTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var  \Akademiano\Config\ConfigLoader */
    protected $configLoader;

    /** @var  \Akademiano\Config\Config */
    protected $configDefault;

    protected function _before()
    {
        $this->configLoader = new \Akademiano\Config\ConfigLoader();
        $rootDir = realpath(__DIR__ . "/../../");
        $this->configLoader->setRootDir($rootDir);

        $configDir = $rootDir . DIRECTORY_SEPARATOR . "tests" .
            DIRECTORY_SEPARATOR . "_data" . DIRECTORY_SEPARATOR . "config";

        $this->configLoader->addConfigDir($configDir);

        $configDefaultGlobal = include $configDir . DIRECTORY_SEPARATOR . "config.php";
        $configDefaultLocal = include $configDir . DIRECTORY_SEPARATOR . "local.config.php";

        $this->configDefault = new Akademiano\Config\Config(
            \Akademiano\Utils\ArrayTools::mergeRecursiveDisabled(
                $configDefaultGlobal,
                $configDefaultLocal
            )
        );
    }

    protected function _after()
    {
        unset($this->configLoader);
        unset($this->configDefault);
    }

    public function testAll()
    {
        $config = $this->configLoader->getConfig();

        var_dump($config->get(["testArray", "filter-list"])->toArray());
//        die();

        $this->tester->assertInstanceOf(\Akademiano\Config\Config::class, $config);
        $this->tester->assertEquals($this->configDefault->toArray(), $config->toArray());
        $this->tester->assertFalse(array_search("unsettted", $config->get(["testArray", "filter-list"])->toArray()));
        $this->tester->assertTrue(false !== array_search("setted", $config->get(["testArray", "filter-list"])->toArray()), "check setted");
        $this->tester->assertNull($config->get(["testArray", "nulled"], "true"), "Nulled Value check");
    }
}
