<?php


namespace Akademiano\UserEO\Api\v1;


use Akademiano\Api\v1\Entities\AbstractEntityApi;
use Akademiano\Api\v1\Items\ItemsPage;
use Akademiano\Core\Exception\AccessDeniedException;
use Akademiano\HttpWarp\Exception\NotFoundException;
use Akademiano\User\CustodianIncludeInterface;
use Akademiano\User\CustodianIncludeTrait;
use Akademiano\UserEO\Model\Group;
use Akademiano\UserEO\Model\User;
use Akademiano\Utils\Paging\PagingMetadata;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

class GroupsApi extends AbstractEntityApi implements CustodianIncludeInterface
{
    use CustodianIncludeTrait;

    public function count($criteria)
    {
        return $this->getOperator()->count(Group::class, $criteria);
    }

    /**
     * @param null $criteria
     * @param string $orderBy
     * @return Option
     * @throws AccessDeniedException
     */
    public function findOne($criteria = null, $orderBy = "id")
    {
        if (null === $criteria) {
            $criteria = ["owner" => $this->getCurrentUser()];
        }

        if (!$this->accessCheck("groups", isset($criteria["owner"]) ? $criteria["owner"] : null)) {
            throw new AccessDeniedException();
        }
        $group = $this->getOperator()->find(Group::class, $criteria, 1, null, $orderBy)->firstOrFalse();
        return !$group ? None::create() : new Some($group);
    }

    public function find($criteria = null, $page = 1, $orderBy = "id", $itemsPerPage = 10)
    {
        if (null === $criteria) {
            $criteria = ["owner" => $this->getCurrentUser()];
        }

        if (!$this->accessCheck("groups", isset($criteria["owner"]) ? $criteria["owner"] : null)) {
            throw new AccessDeniedException();
        }


        $count = $this->count($criteria);
        $pagingMetadata = new PagingMetadata($count, $page, $itemsPerPage);
        $items = $this->getOperator()->find(Group::class, $criteria, $itemsPerPage, $pagingMetadata->getItemsOffset(), $orderBy);

        return new ItemsPage($items, $pagingMetadata);
    }

    public function getRaw($id)
    {
        $item = $this->getOperator()->get(Group::class, $id);

        if (!$item) {
            return null;
        }
        if (!$this->accessCheck("groups/view/{$item->getId()}", $item->getOwner())) {
            throw new AccessDeniedException();
        }
        return $item;
    }

    public function save(array $data)
    {

        if (isset($data["id"])) {
            $id = hexdec($data["id"]);
            unset($data["id"]);
        }

        if (isset($id)) {
            /** @var Group $item */
            $item = $this->get($id)->getOrThrow(
                new NotFoundException(sprintf('Exist group with id "%s" not found', dechex($id)))
            );
            if (!$this->accessCheck("groups/save/{$item->getId()}", $item->getOwner())) {
                throw new AccessDeniedException();
            }
        } else {
            $item = $this->getOperator()->create(Group::class);
        }

        $this->getOperator()->load($item, $data);

        /** @var  $item User */
        $item->setChanged(new \DateTime());

        $this->getOperator()->save($item);

        return $item;
    }

    public function delete($id)
    {
        $item = $this->get($id)->getOrThrow(
            new NotFoundException(sprintf('Group with id %s not found', dechex($id)))
        );

        if (!$this->accessCheck("groups/delete/{$item->getId()}", $item->getOwner())) {
            throw new AccessDeniedException();
        }

        $this->getOperator()->delete($item);
    }
}
