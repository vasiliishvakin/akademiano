<?php

namespace Akademiano\User;


use Akademiano\Entity\UserInterface;
use Akademiano\UserEO\Custodian;
use Akademiano\Utils\DIContainerIncludeInterface;

trait CustodianIncludeTrait
{
    /** @var  AuthInterface */
    protected AuthInterface $custodian;

    /**
     * @return AuthInterface
     */
    public function getCustodian()
    {
        if (empty($this->custodian)) {
            if ($this instanceof DIContainerIncludeInterface) {
                $this->custodian = $this->getDiContainer()[Custodian::RESOURCE_ID];
            }
        }
        return $this->custodian;
    }

    /**
     * @param AuthInterface $custodian
     */
    public function setCustodian(AuthInterface $custodian)
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

    public function isAuthenticate(): bool
    {
        return $this->getCustodian()->isAuthenticate();
    }
}
