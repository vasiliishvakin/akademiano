<?php


namespace Akademiano\UserEO\Api\v1;


use Akademiano\Api\v1\Entities\AbstractEntityApi;
use Akademiano\Api\v1\Items\ItemsPage;
use Akademiano\Core\Exception\AccessDeniedException;
use Akademiano\HttpWarp\Exception\NotFoundException;
use Akademiano\User\CustodianIncludeInterface;
use Akademiano\User\CustodianIncludeTrait;
use Akademiano\UserEO\Model\User;
use Akademiano\Utils\Paging\PagingMetadata;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

class UsersApi extends AbstractEntityApi implements CustodianIncludeInterface
{
    use CustodianIncludeTrait;

    public function count($criteria)
    {
        return $this->getOperator()->count(User::class, $criteria);
    }

    /**
     * @param null $criteria
     * @param string $orderBy
     * @return Option
     * @throws AccessDeniedException
     */
    public function findOne($criteria = null, $orderBy = "id")
    {
        if (empty($criteria)) {
            $criteria = ["owner" => $this->getCurrentUser()->getId()];
        }

        if (!$this->accessCheck("users", isset($criteria["owner"]) ? $criteria["owner"] : null)) {
            throw new AccessDeniedException();
        }
        $user = $this->getOperator()->find(User::class, $criteria, 1, null, $orderBy)->firstOrFalse();
        return !$user ? None::create() : new Some($user);
    }

    public function find($criteria = null, $page = 1, $orderBy = "id", $itemsPerPage = 10)
    {
        if (empty($criteria)) {
            $criteria = ["owner" => $this->getCurrentUser()->getId()];
        }

        if (!$this->accessCheck("users", isset($criteria["owner"]) ? $criteria["owner"] : null)) {
            throw new AccessDeniedException();
        }


        $count = $this->count($criteria);
        $pagingMetadata = new PagingMetadata($count, $page, $itemsPerPage);
        $items = $this->getOperator()->find(User::class, $criteria, $itemsPerPage, $pagingMetadata->getItemsOffset(), $orderBy);

        return new ItemsPage($items, $pagingMetadata);
    }

    public function getRaw($id)
    {
        $item = $this->getOperator()->get(User::class, $id);

        if (!$item) {
            return null;
        }
        if (!$this->accessCheck("users/view/{$item->getId()}", $item->getOwner())) {
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
            /** @var User $item */
            $item = $this->get($id)->getOrThrow(
                new NotFoundException(sprintf('Exist user with is "%s" not found', dechex($id)))
            );
            if (!$this->accessCheck("users/save/{$item->getId()}", $item->getOwner())) {
                throw new AccessDeniedException();
            }
        } else {
            $item = $this->getOperator()->create(User::class);
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
            new NotFoundException(sprintf('User with id %s not found', dechex($id)))
        );

        if (!$this->accessCheck("users/rm/{$item->getId()}", $item->getOwner())) {
            throw new AccessDeniedException();
        }

        $this->getOperator()->delete($item);
    }
}
