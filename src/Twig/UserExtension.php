<?php

namespace Akademiano\User\Twig;

use Akademiano\User\AuthInterface;

class UserExtension extends \Twig_Extension
{
    /** @var  AuthInterface|\Akademiano\UserEO\Custodian */
    protected $custodian;

    public function getName()
    {
        return 'akademiano_user';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'current_user',
                [$this, 'currentUser']
            ),
            new \Twig_SimpleFunction(
                'is_user_authenticate',
                [$this, 'isAuthenticate']
            ),
        ];
    }

    /**
     * @return AuthInterface
     */
    public function getCustodian()
    {
        return $this->custodian;
    }

    /**
     * @param AuthInterface $custodian
     */
    public function setCustodian(AuthInterface $custodian)
    {
        $this->custodian = $custodian;
    }

    public function currentUser()
    {
        return $this->getCustodian()->getCurrentUser();
    }

    public function isAuthenticate()
    {
        return $this->getCustodian()->isAuthenticate();
    }
}
