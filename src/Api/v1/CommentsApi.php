<?php


namespace Akademiano\Content\Comments\Api\v1;


use Akademiano\Acl\AccessCheckIncludeInterface;
use Akademiano\Acl\AccessCheckIncludeTrait;
use Akademiano\Api\v1\Entities\AbstractEntityApi;
use Akademiano\Api\v1\Items\ItemsPage;
use Akademiano\Content\Comments\Model\Comment;
use Akademiano\Utils\Paging\PagingMetadata;
use PhpOption\None;
use PhpOption\Some;
use Akademiano\HttpWarp\Exception\NotFoundException;

class CommentsApi extends AbstractEntityApi implements AccessCheckIncludeInterface
{
    use AccessCheckIncludeTrait;

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

    public function get($id)
    {
        $item = $this->getOperator()->get(Comment::class, $id);

        if (!$item) {
            $hexId = dechex($id);
            throw new NotFoundException("Comment with id {$hexId} not found");
        }
        return $item;
    }

    public function getMaybe($id)
    {
        $item = $this->get($id);
        if ($item) {
            return new Some($item);
        } else {
            return None::create();
        }
    }

    public function save(array $data)
    {
        if (isset($data["id"])) {
            $id = hexdec($data["id"]);
            unset($data["id"]);
        }

        if (isset($id)) {
            $item = $this->getMaybe($id)->getOrThrow(
                new NotFoundException("Exist task with is {$id} not found")
            );
        } else {
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
        $item = $this->get($id);
        if (!$item) {
            throw new NotFoundException(sprintf('Task with id "%s" not found', $id));
        }

        $this->getOperator()->delete($item);
    }
}
