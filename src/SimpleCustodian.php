<?php


namespace Akademiano\User;


use Akademiano\Entity\UuidInterface;
use Akademiano\Entity\UserInterface;

class SimpleCustodian implements AuthInterface
{
    protected $currentUser;


    public function authenticate($identifier, $password)
    {
        return null;
    }

    public function getCurrentUser()
    {
        if (null === $this->currentUser) {
            $this->currentUser = new GuestUser();
        }
        return $this->currentUser;
    }

    public function isAuth(UuidInterface $user = null)
    {
        return false;
    }

    public function sessionClose(UserInterface $user = null)
    {
        return true;
    }
}
