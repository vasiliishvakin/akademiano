<?php

namespace Akademiano\Acl\Model;

use Akademiano\Acl\Model\Adapter\AdapterInterface;
use Akademiano\User\AuthInterface;
use Akademiano\Entity\UserInterface;


class AclManager
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
        return $this->aclAdapter;
    }

    /**
     * @param AdapterInterface $aclAdapter
     */
    public function setAclAdapter($aclAdapter)
    {
        $this->aclAdapter = $aclAdapter;
    }

    public function isAllow($resource, UserInterface $user = null, $owner = null)
    {
        $resource = (string) $resource;
        if (is_null($user)) {
            $user = $this->getCustodian()->getCurrentUser();
        }
        $group = $user->getGroup();
        return $this->getAclAdapter()->isAllow($group ? $group->getTitle() : 'user', $resource, $user->getId(), $owner);
    }
}
