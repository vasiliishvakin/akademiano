<?php


class SitesTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var  \Akademiano\Core\Application */
    protected $application;

    protected function _before()
    {
        $this->application = new \Akademiano\Core\Application();
        $loader = include ROOT_DIR . "/vendor/autoload.php";
        $this->application->setLoader($loader);
    }

    protected function _after()
    {
        \Mockery::close();
        if (isset($this->application)) {
            unset($this->application);
        }
    }

    // tests
    public function testCurrentSite()
    {

    }
}