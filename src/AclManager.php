<?php

namespace Akademiano\Acl;

use Akademiano\Acl\Adapter\AdapterInterface;
use Akademiano\Acl\Adapter\DenyAdapter;
use Akademiano\HttpWarp\Request;
use Akademiano\User\AuthInterface;
use Akademiano\Entity\UserInterface;
use Akademiano\Entity\GroupInterface;


class AclManager implements AccessCheckInterface
{
    /** @var  AuthInterface */
    protected $custodian;

    /** @var  AdapterInterface */
    protected $aclAdapter;

    /** @var  Request */
    protected $request;

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

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function getResource()
    {
        $resource = (string)$this->getRequest()->getUrl()->getPath();
        return $resource;
    }

    public function accessCheck($resource = null, UserInterface $owner = null, GroupInterface $group, UserInterface $user = null)
    {
        if (null === $resource) {
            $resource = $this->getResource();
        }
        if (is_null($user)) {
            $user = $this->getCustodian()->getCurrentUser();
        }
        $group = $user->getGroup();
        return $this->getAclAdapter()->accessCheck($resource, $owner, $group, $user);
    }
}
