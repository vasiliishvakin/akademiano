<?php

namespace Akademiano\UserEO\Model;


use Akademiano\Entity\GroupInterface;
use Akademiano\Entity\NamedEntity;
use Akademiano\Entity\UserInterface;
use Akademiano\EntityOperator\EntityOperator;
use Akademiano\Operator\DelegatingInterface;
use Akademiano\Operator\DelegatingTrait;
use Akademiano\Utils\Object\Prototype\IntegerableInterface;
use Akademiano\Utils\Object\Prototype\StringableInterface;
use Akademiano\Utils\StringUtils;
use Akademiano\UUID\UuidableInterface;

class User extends NamedEntity implements UserInterface, DelegatingInterface
{
    use DelegatingTrait;

    protected $email;

    protected $group;

    protected $password;

    public function getTitle()
    {
        if (empty($this->title)) {
            $this->title = StringUtils::obfuscateEmailV1($this->getEmail());
        }
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    public function getGroup()
    {
        if (!$this->group instanceof GroupInterface) {
            /** @var EntityOperator $operator */
            $operator = $this->getOperator();
            $this->group = $operator->get(Group::class, $this->group);
        }
        return $this->group;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function updatePassword($password)
    {
        $this->setPassword(password_hash($password, PASSWORD_DEFAULT));
    }

    public function verifyPassword($password)
    {
        return password_verify($password, $this->getPassword());
    }

    public function getOwner()
    {
        return $this;
    }
}
