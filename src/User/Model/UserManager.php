<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace User\Model;

use DeltaDb\Repository;

class UserManager extends Repository
{
    const SESSION_CURRENT_USER = 'uid';
    protected $metaInfo = [
        'users' => [
            'class'  => '\\User\\Model\\User',
            'id'     => 'id',
            'fields' => [
                'id',
                'email',
                'password',
            ]
        ]
    ];

    /**
     * @var \HttpWarp\Session
     */
    protected $session;

    /**
     * @var User|null
     */
    protected $currentUser;

    /**
     * @param \HttpWarp\Session $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @return \HttpWarp\Session
     */
    public function getSession()
    {
        return $this->session;
    }

    public function authenticate($email, $password)
    {
        $adapter = $this->getAdapter();
        $table = $this->getTableName("\\User\\Model\\User");
        $sql = "select id, password from {$table} where email=$1";
        $data = $adapter->selectRow($sql, $email);
        return (empty($data)) ? false :
            !User::verifyPassword($password,$data['password']) ? false : $this->findById($data['id']);
    }

    public function findByEmail($email)
    {

        return $this->findOne(['email'=>$email]);
    }

    public function setCurrentUser(User $user)
    {
        $session = $this->getSession();
        $session->set(self::SESSION_CURRENT_USER, $user->getId());
    }

    public function getCurrentUser()
    {
        if (is_null($this->currentUser)) {
            $session = $this->getSession();
            if (null === ($uid = $session->get(self::SESSION_CURRENT_USER))) {
                return null;
            }
            $this->currentUser = $this->findById($uid);
        }
        return $this->currentUser;
    }

    public function logout()
    {
        $session = $this->getSession();
        $session->rm(self::SESSION_CURRENT_USER);
    }

    public function addUser($email, $password)
    {
        $someUser = $this->findByEmail($email);
        if ($someUser) {
            return false;
        }
        /** @var User $user */
        $user = $this->create();
        $user->setEmail($email);
        $user->setNewPassword($password);
        $this->save($user);
        return $user;
    }

} 