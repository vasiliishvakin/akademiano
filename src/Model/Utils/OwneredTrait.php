<?php

namespace Akademiano\UserEO\Model\Utils;

use Akademiano\Delegating\OperatorInterface;
use Akademiano\Entity\UserInterface;
use Akademiano\EntityOperator\EntityOperator;
use Akademiano\UserEO\Model\User;

trait OwneredTrait
{
    /** @var UserInterface */
    protected $owner;

    abstract public function getOperator():?OperatorInterface;

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
            /** @var EntityOperator $operator */
            $operator = $this->getOperator();
            $this->owner = $operator->get($this->getOwnerClass(), $this->owner);
        }
        return $this->owner;
    }
}
