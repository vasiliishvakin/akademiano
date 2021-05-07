<?php


namespace Akademiano\UserEO;


use Akademiano\Delegating\DelegatingInterface;
use Akademiano\Delegating\DelegatingTrait;
use Akademiano\Entity\UserInterface;
use Akademiano\Entity\UuidInterface;
use Akademiano\EntityOperator\Command\FindCommand;
use Akademiano\EntityOperator\Command\GetCommand;
use Akademiano\HttpWarp\EnvironmentIncludeInterface;
use Akademiano\HttpWarp\Parts\EnvironmentIncludeTrait;
use Akademiano\User\GuestUser;
use Akademiano\User\SimpleCustodian;
use Akademiano\UserEO\Exception\NotFoundUserException;
use Akademiano\UserEO\Model\Request\HttpSessionDataTool;
use Akademiano\UserEO\Model\Request\RequestDataToolInterface;
use Akademiano\UserEO\Model\User;

class Custodian extends SimpleCustodian implements DelegatingInterface, EnvironmentIncludeInterface
{
    use DelegatingTrait;
    use EnvironmentIncludeTrait;

    /** @var  RequestDataToolInterface */
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

    public function isAuthenticate(UuidInterface $user = null): bool
    {
        return ($this->getRdt()->isAuthenticate() && !$this->getCurrentUser() instanceof GuestUser);
    }

    public function setCurrentUser($user)
    {
        if (!$user instanceof UserInterface) {
            $user = $this->delegate((new GetCommand(User::class))->setId($user));
            if (!$user instanceof UserInterface) {
                throw new \InvalidArgumentException(sprintf('User with id %s not found', $user));
            }
        }
        $this->sessionStart($user);
        $this->currentUser = $user;
    }

    public function getCurrentUser()
    {
        if (!$this->currentUser) {
            $rdt = $this->getRdt();
            if (!$this->getEnvironment()->isCli() || !$rdt instanceof HttpSessionDataTool) {
                $uid = $rdt->getCurrentUserId();
            }
            if (isset($uid) && $uid) {
                $user = $this->delegate((new GetCommand(User::class))->setId($uid));
                $this->currentUser = $user instanceof UserInterface ? $user : new GuestUser();
            } else {
                $this->currentUser = new GuestUser();
            }
        }
        return $this->currentUser;
    }

    /**
     * @param $identifier
     * @param $password
     * @return UserInterface|null
     * @throws \Exception
     */
    public function authenticate($identifier, $password): ?UserInterface
    {
        /** @var User $user */
        $user = $this->delegate((new FindCommand(User::class))
            ->setCriteria([
                "email" => $identifier,
                "active" => true,
            ])
            ->setLimit(1)
        )->firstOrFail(new NotFoundUserException(sprintf('User with identifier %s not found', $identifier)));
        if (!$user->verifyPassword($password)) {
            return null;
        }
        return $user;
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
