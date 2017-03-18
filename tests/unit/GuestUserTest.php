<?php


class GuestUserTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var  \Akademiano\User\GuestUserInterface */
    protected $user;

    protected function _before()
    {
        $this->user = new \Akademiano\User\GuestUser();
    }

    protected function _after()
    {
        if (isset($this->user)) {
            unset($this->user);
        }
    }

    // tests
    public function testGuestUser()
    {
        $this->assertInstanceOf(\Akademiano\User\GuestGroupInterface::class, $this->user->getGroup());
    }
}