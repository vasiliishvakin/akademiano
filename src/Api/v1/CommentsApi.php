<?php


namespace Akademiano\Content\Comments\Api\v1;

use Akademiano\Api\v1\Entities\EntityApi;
use Akademiano\Api\v1\Items\ItemsPage;
use Akademiano\Content\Comments\Model\Comment;
use Akademiano\Core\Exception\AccessDeniedException;
use Akademiano\Entity\EntityInterface;
use Akademiano\Utils\Paging\PagingMetadata;
use Akademiano\HttpWarp\Exception\NotFoundException;

class CommentsApi extends EntityApi
{

    public function saveBound(EntityInterface $entity, array $data)
    {
        $resource = sprintf('entityapi:save:bound:%s:%s', get_class($entity), $entity->getId());
        if (!$this->accessCheck($resource, $entity->getOwner())) {
            throw new AccessDeniedException(sprintf('Access Denied to "%s"', $resource), null, null, $resource);
        }

        if (isset($data["id"])) {
            $id = hexdec($data["id"]);
            unset($data["id"]);
        }

        if (isset($id)) {
            /** @var EntityInterface $item */
            $item = $this->get($id)->getOrThrow(
                new NotFoundException(sprintf('Exist comment with is %s not found', dechex($id)))
            );
            $resource = sprintf('entityapi:save:%s:%s', static::ENTITY_CLASS, $item->getId());
            if (!$this->accessCheck($resource, $item->getOwner())) {
                throw new AccessDeniedException(sprintf('Access Denied to "%s"', $resource), null, null, $resource);
            }
        } else {
            $resource = sprintf('entityapi:add:%s', static::ENTITY_CLASS);
            if (!$this->accessCheck($resource)) {
                throw new AccessDeniedException(sprintf('Access Denied to "%s"', $resource), null, null, $resource);
            }
            $item = $this->getOperator()->create(static::ENTITY_CLASS);

            $data["entity"] = $entity->getId()->getInt();
        }

        $this->getOperator()->load($item, $data);

        /** @var  $item Comment */
        $item->setChanged(new \DateTime());

        if (!$item->isExistingEntity()) {
            $item->setOwner($this->getCustodian()->getCurrentUser());
        }

        $this->getOperator()->save($item);

        return $item;
    }

    public function saveNotEmpty(EntityInterface $entity, array $data)
    {
        if (!isset($data["content"]) && empty($data["content"])) {
            return false;
        }
        $data["content"] = trim($data["content"]);
        if (empty($data["content"])) {
            return false;
        }

        return $this->saveBound($entity, $data);
    }
}
