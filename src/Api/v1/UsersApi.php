<?php

namespace Akademiano\UserEO\Api\v1;

use Akademiano\Api\v1\Entities\EntityApi;
use Akademiano\UserEO\Model\User;
use Akademiano\HttpWarp\Exception\NotFoundException;
use Akademiano\Entity\EntityInterface;
use Akademiano\Core\Exception\AccessDeniedException;

class UsersApi extends EntityApi
{
    const API_ID = "usersApi";
    const ENTITY_CLASS = User::class;

    public function save(array $data)
    {
        if (isset($data["id"])) {
            $id = hexdec($data["id"]);
            unset($data["id"]);
        }

        if (isset($id)) {
            /** @var User $item */
            $item = $this->get($id)->getOrThrow(
                new NotFoundException("Exist entity with is {$id} not found")
            );
            if (!$this->accessCheck(sprintf('%s/save/%s', static::ENTITY_CLASS, $item->getId()), $item->getOwner())) {
                throw new AccessDeniedException();
            }
        } else {
            /** @var User $item */
            $item = $this->getOperator()->create(static::ENTITY_CLASS);
        }

        if (isset($data["password"])) {
            unset($data["password"]);
        }

        $this->getOperator()->load($item, $data);

        if (isset($data["newPassword"])) {
            $newPassword = trim($data["newPassword"]);
            if ($newPassword !== "") {
                $item->updatePassword($newPassword);
            }
        }

        /** @var  $item EntityInterface */
        $item->setChanged(new \DateTime());

        if (!$item->isExistingEntity()) {
            $item->setOwner($this->getCustodian()->getCurrentUser());
        }

        $this->getOperator()->save($item);

        return $item;
    }
}
