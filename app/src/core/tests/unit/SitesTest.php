<?php


class SitesTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var  \Akademiano\Core\Application */
    protected $application;

    /** @var  \Mockery\MockInterface|\Akademiano\HttpWarp\Environment */
    protected $environment;

    protected function _before()
    {
        $this->application = new \Akademiano\Core\Application();
        $loader = include ROOT_DIR . "/vendor/autoload.php";
        $this->application->setLoader($loader);

        $this->environment = \Mockery::mock(\Akademiano\HttpWarp\Environment::class);
        $this->application->addToDiContainer("environment", function () {
            return $this->environment;
        });
    }

    protected function _after()
    {
        \Mockery::close();
        if (isset($this->application)) {
            unset($this->application);
        }

        if (isset($this->environment)) {
            unset($this->environment);
        }
    }

    // tests
    public function testSite()
    {
        $this->environment->shouldReceive("getServerName")
            ->andReturn("_testsite_test");


        $site = $this->application->getCurrentSite();

        $this->tester->assertInstanceOf(\Akademiano\Sites\Site::class, $site);
    }
}