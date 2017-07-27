<?php


namespace Akademiano\User;


use Akademiano\Entity\UuidInterface;
use Akademiano\Entity\UserInterface;

interface AuthInterface
{
    /**
     * @param $identifier
     * @param $password
     * @return UserInterface|null
     */
    public function authenticate($identifier, $password);

    /**
     * @return UserInterface
     */
    public function getCurrentUser();

    /**
     * @param UuidInterface|null $user
     * @return bool
     */
    public function isAuthenticate(UuidInterface $user = null);

    /**
     * @param UserInterface|null $user
     * @return bool
     */
    public function sessionClose();

    public function sessionStart(UserInterface $user);

}
