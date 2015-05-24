<?php
/**
 * User: Evgeniy
 */

namespace User\Model;

use DeltaDb\EntityInterface;
use DeltaDb\Repository;

/**
 * Class UserPlacesManager
 * @package User\Model
 * @method setUserManager(Callable $userManager)
 * @method UserManager getUserManager()
 */
class UserPlacesManager extends Repository
{
    protected $metaInfo = [
        'users_places' => [
            'class'  => '\\User\\Model\\UserPlace',
            'id'     => 'id',
            'fields' => [
                'id',
                'user',
                "created",
                "changed",
                "data",
            ]
        ]
    ];

    /**
     * @param array $data
     * @param null $entityClass
     * @return UserPlace
     */
    public function create(array $data = null, $entityClass = null)
    {
        $entity = parent::create($data, $entityClass);
        if ($entity instanceof UserPlace) {
            $entity->setUserManager($this->getUserManager());
        }
        return $entity;
    }

    public function save(EntityInterface $entity)
    {
        /** @var UserPlace $entity */
        $created = $entity->getCreated();
        if (is_null($created)) {
            $entity->setCreated(new \DateTime());
        }
        $entity->setChanged(new \DateTime());
        return parent::save($entity);
    }

}