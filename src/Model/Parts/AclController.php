<?php

namespace Acl\Model\Parts;


use Akademiano\Entity\UserInterface;
use Pimple\Container;

trait AclController
{
    /**
     * @return Container
     */
    abstract public function getDIContainer();

    /**
     * @return \Akademiano\HttpWarp\Request
     */
    abstract public function getRequest();

    public function isAllow($resource = null, UserInterface $user = null)
    {
        $diCont = $this->getDIContainer();
        /** @var \Akademiano\Acl\Model\AclManager $aclManager */
        $aclManager = $diCont['aclManager'];
        if (!$resource) {
            /** @var \Akademiano\HttpWarp\Request $request */

            $resource = (string)$this->getRequest()->getUrl()->getPath();
        }
        if (!$user) {
            $user = $this->getCurrentUser();
        }
        return $aclManager->isAllow($resource, $user);
    }

    /**
     * @return UserInterface|null
     */
    public function getCurrentUser()
    {
        $diCont = $this->getDIContainer();
        /** @var \Akademiano\User\AuthInterface $custodian */
        $custodian = $diCont['custodian'];
        return $custodian->getCurrentUser();
    }
}
