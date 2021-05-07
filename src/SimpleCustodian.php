<?php


namespace Akademiano\User;


use Akademiano\Entity\UuidInterface;
use Akademiano\Entity\UserInterface;

class SimpleCustodian implements AuthInterface
{
    public const RESOURCE_ID = "custodian";

    protected $currentUser;


    public function authenticate($identifier, $password): ?UserInterface
    {
        return null;
    }

    /**
     * @return UserInterface
     */
    public function getCurrentUser()
    {
        if (null === $this->currentUser) {
            $this->currentUser = new GuestUser();
        }
        return $this->currentUser;
    }

    public function isAuthenticate(UuidInterface $user = null): bool
    {
        return false;
    }

    public function sessionClose()
    {
        return true;
    }

    public function sessionStart(UserInterface $user)
    {
        return true;
    }
}
