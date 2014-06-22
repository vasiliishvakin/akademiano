<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace User\Model;

use DeltaDb\AbstractEntity;
use DeltaDb\EntityInterface;
use DeltaDb\Repository;

class User extends AbstractEntity implements EntityInterface
{
    protected $id;
    protected $email;
    protected $password;
    protected $group;

    /** @var  Repository */
    protected $groupManager;

    /**
     * @return Repository
     */
    public function getGroupManager()
    {
        return $this->groupManager;
    }

    /**
     * @param Repository $groupManager
     */
    public function setGroupManager($groupManager)
    {
        $this->groupManager = $groupManager;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = (integer)$id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public static function checkHash($hash)
    {
        return password_needs_rehash($hash, PASSWORD_DEFAULT);
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setNewPassword($password)
    {
        $this->setPassword(self::hashPassword($password));
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return mixed
     */
    public function getGroup()
    {
        if (!is_null($this->group) && !is_object($this->group)) {
            $this->group = $this->getGroupManager()->findById($this->group);
        }
        return $this->group;
    }

}