<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace User\Model;

use Attach\Model\File;
use DeltaCore\Prototype\AbstractEntity;
use DeltaDb\EntityInterface;
use DeltaDb\Repository;
use User\Exception\WrongPassword;

/**
 * Class User
 * @package User\Model
 * @method UserManager getUserManager()
 */
class User extends AbstractEntity implements EntityInterface
{
    protected $id;
    protected $email;
    protected $password;
    protected $group;
    protected $avatar;
    protected $firstName;
    protected $lastName;
    protected $userName;
    protected $confirmed = false;
    protected $created;
    protected $changed;

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
        if (!is_string($password) || strlen($password) < 6) {
            throw new WrongPassword();
        }
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

    /**
     * @return File
     */
    public function getAvatar()
    {
        if (is_null($this->avatar)) {
            $fm = $this->getUserManager()->getFileManager();
            $images = $fm->getFilesForObject($this);
            if ($images->isEmpty()) {
                $this->avatar = false;
            } else {
                $this->avatar = $images->last();
            }
        }
        return $this->avatar;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
        return $this;
    }

    public function getConfirmed()
    {
        return $this->confirmed;
    }

    public function setConfirmed($confirmed)
    {
        if (is_string($confirmed)) {
            if (!!$confirmed && $confirmed !== 'f' && $confirmed !== 'false') {
                $this->confirmed = true;
            }
        } else {
            $this->confirmed = (bool)$confirmed;
        }
        return $this;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        if ($created && !$created instanceof \DateTime) {
            if (is_array($created)) {
                $created = (new \DateTime($created['date']))->setTimezone(new \DateTimeZone($created['timezone']));
            } else {
                $created = new \DateTime($created);
            }
        }
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return \DateTime|null
     */
    public function getChanged()
    {
        return $this->changed;
    }

    /**
     * @param mixed $changed
     */
    public function setChanged($changed)
    {
        if ($changed && !$changed instanceof \DateTime) {
            if (is_array($changed)) {
                $changed = (new \DateTime($changed['date']))->setTimezone(new \DateTimeZone($changed['timezone']));
            } else {
                $changed = new \DateTime($changed);
            }
        }
        $this->changed = $changed;
    }

    public function getUrl()
    {
        return '/user/' . ($this->getUserName() ? $this->getUserName() : $this->getId());
    }
}
