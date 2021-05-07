<?php

namespace Akademiano\UserEO\Model;


use Akademiano\Delegating\DelegatingTrait;
use Akademiano\Entity\GroupInterface;
use Akademiano\Entity\NamedEntity;
use Akademiano\Entity\UserInterface;
use Akademiano\Utils\StringUtils;
use Akademiano\EntityOperator\Command\GetCommand;

class User extends NamedEntity implements UserInterface
{
    use DelegatingTrait;

    protected $email;

    protected $phone;

    protected $group;

    protected $password;

    protected ?string $newPassword = null;

    public function getTitle()
    {
        if (empty($this->title)) {
            $this->title = $this->getEmail() ? StringUtils::obfuscateEmailV1($this->getEmail()) : null;
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
            $command = (new GetCommand(Group::class))->setId($this->group);
            $this->group = $this->delegate($command, true);
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
        if (empty($this->getPassword())) {
            return false;
        }
        return password_verify($password, $this->getPassword());
    }

    /**
     * @return mixed
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    /**
     * @param mixed $newPassword
     */
    public function setNewPassword($newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    public function getOwner(): ?UserInterface
    {
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    public function toArray(): array
    {
        $data = parent::toArray();
        $data['email'] = $this->getEmail();
        $data['phone'] = $this->getPhone();
        $data['group'] = $this->getGroup()->toArray();
        return $data;
    }
}
