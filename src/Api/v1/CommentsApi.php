<?php


namespace Akademiano\Content\Comments\Api\v1;

use Akademiano\Api\v1\Entities\AbstractEntityApi;
use Akademiano\Api\v1\Items\ItemsPage;
use Akademiano\Content\Comments\Model\Comment;
use Akademiano\Core\Exception\AccessDeniedException;
use Akademiano\Entity\EntityInterface;
use Akademiano\Utils\Paging\PagingMetadata;
use Akademiano\HttpWarp\Exception\NotFoundException;

class CommentsApi extends AbstractEntityApi
{
    public function count($criteria)
    {
        return $this->getOperator()->count(Comment::class, $criteria);
    }

    public function find($criteria = null, $page = 1, $orderBy = "id", $itemsPerPage = null)
    {
        $count = $this->count($criteria);
        $pagingMetadata = new PagingMetadata($count, $page, $itemsPerPage);
        $items = $this->getOperator()->find(Comment::class, $criteria, $itemsPerPage, $pagingMetadata->getItemsOffset(), $orderBy);

        return new ItemsPage($items, $pagingMetadata);
    }

    protected function getRaw($id)
    {
        $item = $this->getOperator()->get(Comment::class, $id);

        if (!$item) {
            return null;
        }
        if (!$this->accessCheck("comments/view/{$item->getId()}", $item->getOwner())) {
            throw new AccessDeniedException();
        }
        return $item;
    }

    public function save(EntityInterface $entity, array $data)
    {
        if (isset($data["id"])) {
            $id = hexdec($data["id"]);
            unset($data["id"]);
        }

        if (isset($id)) {
            $item = $this->get($id)->getOrThrow(
                new NotFoundException(sprintf('Exist comment with is %s not found', dechex($id)))
            );
            if (!$this->accessCheck("comments/save/{$item->getId()}", $item->getOwner())) {
                throw new AccessDeniedException();
            }
        } else {
            if (!$this->accessCheck("comments/create")) {
                throw new AccessDeniedException();
            }
            $item = $this->getOperator()->create(Comment::class);
        }

        $this->getOperator()->load($item, $data);

        /** @var  $item Comment */
        $item->setChanged(new \DateTime());

        $this->getOperator()->save($item);

        return $item;
    }

    public function delete($id)
    {
        $item = $this->get($id)->getOrThrow(
            new NotFoundException(sprintf('Comment with id "%s" not found', dechex($id)))
        );

        if (!$this->accessCheck("comments/delete/{$item->getId()}", $item->getOwner())) {
            throw new AccessDeniedException();
        }

        $this->getOperator()->delete($item);
    }
}
