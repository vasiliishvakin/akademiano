<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace User\Model;

use Attach\Model\FileManager;
use DeltaDb\EntityInterface;
use DeltaDb\Repository;
use PermAuth\Model\Authenticator;
use User\Exception\UserAlreadyExists;
use User\Exception\UserNotFound;
use User\Exception\WrongUserCredential;

/**
 * Class UserProvidersManager
 * @package User\Model
 * @method setUserManager(Callable $userManager)
 * @method UserManager getUserManager()
 */
class UserProvidersManager extends Repository
{
    protected $metaInfo = [
        "table" => 'users_providers',
        'class' => '\\User\\Model\\UserProvider',
        'id' => 'id',
        'fields' => [
            'id',
            'provider',
            'identifier',
            'user',
            "created",
            "changed",
            "data",
        ]
    ];

    /**
     * @param array $data
     * @param null $entityClass
     * @return UserProvider
     */
    public function create(array $data = null, $entityClass = null)
    {
        $item = parent::create($data, $entityClass);
        $item->setUserManager($this->getUserManager());
        return $item;
    }

    public function save(EntityInterface $entity)
    {
        /** @var UserProvider $entity */
        $created = $entity->getCreated();
        if (is_null($created)) {
            $entity->setCreated(new \DateTime());
        }
        $entity->setChanged(new \DateTime());
        return parent::save($entity);
    }

}