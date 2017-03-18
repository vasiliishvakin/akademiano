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

    /** UserInterface */
    public function getCurrentUser();

    /**
     * @param UuidInterface|null $user
     * @return bool
     */
    public function isAuth(UuidInterface $user = null);

    /**
     * @param UserInterface|null $user
     * @return bool
     */
    public function sessionClose(UserInterface $user = null);

}
