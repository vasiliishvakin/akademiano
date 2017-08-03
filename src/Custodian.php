<?php


namespace Akademiano\UserEO;


use Akademiano\Entity\UserInterface;
use Akademiano\Entity\UuidInterface;
use Akademiano\EntityOperator\EntityOperator;
use Akademiano\Operator\DelegatingInterface;
use Akademiano\Operator\DelegatingTrait;
use Akademiano\User\GuestUser;
use Akademiano\User\SimpleCustodian;
use Akademiano\UserEO\Model\Request\HttpSessionDataTool;
use Akademiano\UserEO\Model\Request\RequestDataToolInterface;
use Akademiano\UserEO\Model\User;

class Custodian extends SimpleCustodian implements DelegatingInterface
{
    use DelegatingTrait;

    /** @var  RequestDataToolInterface*/
    protected $rdt;

    /**
     * @return RequestDataToolInterface
     */
    public function getRdt()
    {
        return $this->rdt;
    }

    /**
     * @param RequestDataToolInterface $rdt
     */
    public function setRdt(RequestDataToolInterface $rdt)
    {
        $this->rdt = $rdt;
    }

    public function isAuthenticate(UuidInterface $user = null)
    {
        return $this->getRdt()->isAuthenticate();
    }

    public function getCurrentUser()
    {
        if (!$this->currentUser) {
            $uid = $this->getRdt()->getCurrentUserId();
            if ($uid) {
                /** @var EntityOperator $operator */
                $operator = $this->getOperator();
                $user = $operator->get(User::class, $uid);
                $this->currentUser = $user instanceof UserInterface ? $user : new GuestUser();
            } else {
                $this->currentUser = new GuestUser();
            }
        }
        return $this->currentUser;
    }

    public function authenticate($identifier, $password)
    {
        /** @var EntityOperator $operator */
        $operator = $this->getOperator();
        /** @var User $user */
        $user = $operator->find(User::class, ["email" => $identifier], 1)->firstOrFail();
        return $user->verifyPassword($password);
    }

    public function sessionClose()
    {
        $rdt = $this->getRdt();
        if ($rdt instanceof HttpSessionDataTool) {
            $rdt->deleteCurrentUserId();
        }
    }

    public function sessionStart(UserInterface $user)
    {
        $rdt = $this->getRdt();
        if ($rdt instanceof HttpSessionDataTool) {
            $rdt->setCurrentUserId($user->getId()->getInt());
        }
    }
}
