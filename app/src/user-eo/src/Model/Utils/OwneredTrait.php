<?php

namespace Akademiano\UserEO\Model\Utils;

use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Entity\UserInterface;
use Akademiano\EntityOperator\Command\GetCommand;
use Akademiano\UserEO\Model\User;

trait OwneredTrait
{
    /** @var UserInterface */
    protected $owner;

    abstract public function delegate(CommandInterface $command,  bool $throwOnEmptyOperator = false);

    protected function getOwnerClass()
    {
        return User::class;
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    public function getOwner():?UserInterface
    {
        if (null !== $this->owner && !$this->owner instanceof UserInterface) {
            $command = (new GetCommand($this->getOwnerClass()))->setId($this->owner);
            $this->owner = $this->delegate($command);
        }
        return $this->owner;
    }
}
