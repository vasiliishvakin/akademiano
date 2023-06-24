<?php


class SimpleCustodianTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var  \Akademiano\User\SimpleCustodian */
    protected $custodian;

    protected function _before()
    {
        $this->custodian = new \Akademiano\User\SimpleCustodian();
    }

    protected function _after()
    {
        if (isset($this->custodian)) {
            unset($this->custodian);
        }
    }

    public function testCustodian()
    {
        $this->tester->assertNull($this->custodian->authenticate("user", "password"));
        $this->assertInstanceOf(\Akademiano\User\GuestUserInterface::class, $this->custodian->getCurrentUser());
        $this->assertFalse($this->custodian->isAuthenticate());
        $this->assertTrue($this->custodian->sessionClose());
    }
}