<?php

namespace Akademiano\UserEO\Api\v1;

use Akademiano\Api\v1\Entities\EntityApi;
use Akademiano\EntityOperator\Command\CreateCommand;
use Akademiano\EntityOperator\Command\LoadCommand;
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
            $resource = sprintf('%s/save/%s', static::ENTITY_CLASS, $item->getId());
            if (!$this->accessCheck($resource)) {
                throw new AccessDeniedException("AccessDenied to save user", 0, null, $resource);
            }
        } else {
            /** @var User $item */
            $item = $this->delegate(new CreateCommand(static::ENTITY_CLASS));
        }

        if (isset($data["password"])) {
            unset($data["password"]);
        }

        $this->delegate((new LoadCommand($item))->setData($data));

        if (isset($data["newPassword"])) {
            $newPassword = trim($data["newPassword"]);
            $item->setNewPassword($newPassword);
            if ($newPassword !== "") {
                $item->updatePassword($newPassword);
            }
        }

        $this->saveEntity($item);

        return $item;
    }
}
