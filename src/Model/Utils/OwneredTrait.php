<?php

namespace Akademiano\UserEO\Model\Utils;

use Akademiano\Entity\UserInterface;
use Akademiano\EntityOperator\EntityOperator;
use Akademiano\UserEO\Model\User;

trait OwneredTrait
{
    /** @var UserInterface */
    protected $owner;

    abstract public function getOperator();

    protected function getOwnerClass()
    {
        return User::class;
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    public function getOwner()
    {
        if (null !== $this->owner && !$this->owner instanceof UserInterface) {
            /** @var EntityOperator $operator */
            $operator = $this->getOperator();
            $this->owner = $operator->get($this->getOwnerClass(), $this->owner);
        }
        return $this->owner;
    }
}
