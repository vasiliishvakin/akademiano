<?php

namespace Akademiano\Acl\Model;

use Akademiano\Acl\Model\Adapter\AdapterInterface;
use Akademiano\Acl\Model\Adapter\DenyAdapter;
use Akademiano\User\AuthInterface;
use Akademiano\Entity\UserInterface;
use Akademiano\Entity\GroupInterface;


class AclManager implements AccessCheckInterface
{
    /** @var  AuthInterface */
    protected $custodian;

    /** @var  AdapterInterface */
    protected $aclAdapter;

    /**
     * @param AuthInterface $custodian
     */
    public function setCustodian(AuthInterface $custodian)
    {
        $this->custodian = $custodian;
    }

    /**
     * @return AuthInterface
     */
    public function getCustodian()
    {
        return $this->custodian;
    }

    /**
     * @return AdapterInterface
     */
    public function getAclAdapter()
    {
        if (null === $this->aclAdapter) {
            $this->aclAdapter = new DenyAdapter();
        }
        return $this->aclAdapter;
    }

    /**
     * @param AdapterInterface $aclAdapter
     */
    public function setAclAdapter($aclAdapter)
    {
        $this->aclAdapter = $aclAdapter;
    }

    public function accessCheck($resource, GroupInterface $group, UserInterface $user = null, UserInterface $owner = null)
    {
        $resource = (string)$resource;
        if (is_null($user)) {
            $user = $this->getCustodian()->getCurrentUser();
        }
        $group = $user->getGroup();
        return $this->getAclAdapter()->accessCheck($resource, $group, $user, $owner);
    }
}
