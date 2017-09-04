<?php

namespace Akademiano\Acl;

use Akademiano\Acl\Adapter\AdapterInterface;
use Akademiano\Acl\Adapter\DenyAdapter;
use Akademiano\Acl\Adapter\XAclAdapter;
use Akademiano\HttpWarp\EnvironmentIncludeInterface;
use Akademiano\HttpWarp\Parts\EnvironmentIncludeTrait;
use Akademiano\HttpWarp\Request;
use Akademiano\User\AuthInterface;
use Akademiano\Entity\UserInterface;
use Akademiano\Entity\GroupInterface;


class AclManager implements AccessCheckInterface, EnvironmentIncludeInterface
{
    const ENV_VAR_DISABLE_CHECK_NAME = "AKADEMIANO_NO_ACL_CHECK";

    use EnvironmentIncludeTrait;

    /** @var  AuthInterface */
    protected $custodian;

    /** @var  AdapterInterface */
    protected $aclAdapter;

    /** @var  Request */
    protected $request;

    /** @var bool */
    protected $disabledAccessCheck = false;

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
        $resource = XAclAdapter::prepareResource((string)$this->getRequest()->getUrl()->getPath());
        return $resource;
    }

    /**
     * @return bool
     */
    public function isDisabledAccessCheck()
    {
        if ($this->getEnvironment()->isCli() && $this->getEnvironment()->getVar(self::ENV_VAR_DISABLE_CHECK_NAME, false) == 1) {
            return true;
        }
        return $this->disabledAccessCheck;
    }

    /**
     * @param bool $disableAccessCheck
     */
    public function setDisableAccessCheck($disableAccessCheck)
    {
        $this->disabledAccessCheck = $disableAccessCheck;
    }

    public function disableAccessCheck()
    {
        $this->setDisableAccessCheck(true);
    }

    public function enableAccessCheck()
    {
        $this->setDisableAccessCheck(false);
    }

    public function accessCheck($resource = null, UserInterface $owner = null, GroupInterface $group = null, UserInterface $user = null)
    {
        if ($this->isDisabledAccessCheck()) {
            return true;
        }

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
