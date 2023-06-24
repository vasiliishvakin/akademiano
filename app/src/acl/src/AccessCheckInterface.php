<?php

namespace Akademiano\Acl;


use Akademiano\Entity\GroupInterface;
use Akademiano\Entity\UserInterface;

interface AccessCheckInterface
{
    /**
     * @return bool
     */
    public function isDisabledAccessCheck();

    /**
     * @param bool $disableAccessCheck
     */
    public function setDisableAccessCheck($disableAccessCheck);

    public function disableAccessCheck();

    public function enableAccessCheck();

    public function accessCheck($resource, UserInterface $owner = null, GroupInterface $group = null, UserInterface $user = null);
}
