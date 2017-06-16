<?php

namespace Akademiano\User;


use Akademiano\Entity\UserInterface;

trait CustodianIncludeTrait
{
    /** @var  AuthInterface */
    protected $custodian;

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
    public function setCustodian($custodian)
    {
        $this->custodian = $custodian;
    }

    /**
     * @return UserInterface
     */
    public function getCurrentUser()
    {
        return $this->getCustodian()->getCurrentUser();
    }

    public function getCurrentGroup()
    {
        return $this->getCurrentUser()->getGroup();
    }
}
