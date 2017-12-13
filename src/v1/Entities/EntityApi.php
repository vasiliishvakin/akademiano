<?php


namespace Akademiano\Api\v1\Entities;


use Akademiano\Acl\AccessCheckInterface;
use Akademiano\Api\v1\AbstractApi;
use Akademiano\Entity\Entity;
use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\EntityOperator;
use Akademiano\EntityOperator\Worker\KeeperInterface;
use Akademiano\HeraldMessages\Model\Message;
use Akademiano\Operator\Command\WorkerInfoCommand;
use Akademiano\User\CustodianIncludeInterface;
use Akademiano\User\CustodianIncludeTrait;
use Akademiano\UUID\UuidComplexShortTables;
use PhpOption\Some;
use PhpOption\None;
use Akademiano\Utils\Paging\PagingMetadata;
use Akademiano\Api\v1\Items\ItemsPage;
use Akademiano\Core\Exception\AccessDeniedException;
use Akademiano\HttpWarp\Exception\NotFoundException;

class EntityApi extends AbstractApi implements EntityApiInterface, CustodianIncludeInterface
{
    const ENTITY_CLASS = Entity::class;
    const API_ID = 'entityApi';

    /** @var  EntityOperator */
    protected $operator;

    use CustodianIncludeTrait;

    /**
     * AbstractEntityApi constructor.
     * @param EntityOperator $operator
     */
    public function __construct(EntityOperator $operator = null)
    {
        if (null !== $operator) {
            $this->setOperator($operator);
        }
    }

    /**
     * @return EntityOperator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param EntityOperator $operator
     */
    public function setOperator(EntityOperator $operator)
    {
        $this->operator = $operator;
    }

    public function intToUuidCST($value)
    {
        return $this->getOperator()->create(UuidComplexShortTables::class, ["value" => $value]);
    }


    public function count($criteria)
    {
        return $this->getOperator()->count(static::ENTITY_CLASS, $criteria);
    }

    public function getDefaultOrder()
    {
        return static::DEFAULT_ORDER;
    }

    public function find($criteria = null, $page = 1, $orderBy = null, $itemsPerPage = null)
    {
        if (null === $criteria) {
            $criteria = ["owner" => $this->getCurrentUser()];
        }

        if (null === $orderBy) {
            $orderBy = $this->getDefaultOrder();
        }

        $resource = sprintf('entityapi:list:%s', static::ENTITY_CLASS);
        if (!$this->accessCheck($resource, isset($criteria["owner"]) ? $criteria["owner"] : null)) {
            throw new AccessDeniedException(sprintf('Access Denied to "%s"', $resource), null, null, $resource);
        }

        $count = $this->count($criteria);
        $pagingMetadata = new PagingMetadata($count, $page, $itemsPerPage);
        $items = $this->getOperator()->find(static::ENTITY_CLASS, $criteria, $itemsPerPage, $pagingMetadata->getItemsOffset(), $orderBy);

        return new ItemsPage($items, $pagingMetadata);
    }

    public function findOne($criteria = null)
    {
        if (null === $criteria) {
            $criteria = ["owner" => $this->getCurrentUser()];
        }

        /** @var EntityInterface $item */
        $item = $this->getOperator()->find(static::ENTITY_CLASS, $criteria, 1)->firstOrFalse();

        if (!$item) {
            return None::create();
        }

        $resource = sprintf('entityapi:view:%s:%s', static::ENTITY_CLASS, $item->getId());
        if (!$this->accessCheck($resource, isset($criteria["owner"]) ? $criteria["owner"] : null)) {
            throw new AccessDeniedException(sprintf('Access Denied to "%s"', $resource), null, null, $resource);
        }

        return new Some($item);
    }

    /**
     * @param null $criteria
     * @param null $orderBy
     * @return EntityInterface[]|\Akademiano\Utils\Object\Collection
     * @throws AccessDeniedException
     */
    public function findAll($criteria = null, $orderBy = null)
    {
        if (null === $criteria) {
            $criteria = ["owner" => $this->getCurrentUser()];
        }

        $resource = sprintf('entityapi:list:%s', static::ENTITY_CLASS);
        if (!$this->accessCheck($resource, isset($criteria["owner"]) ? $criteria["owner"] : null)) {
            throw new AccessDeniedException(sprintf('Access Denied to "%s"', $resource), null, null, $resource);
        }

        $items = $this->getOperator()->find(static::ENTITY_CLASS, $criteria, $orderBy);

        return $items;
    }

    public function getRaw($id)
    {
        $item = $this->getOperator()->get(static::ENTITY_CLASS, $id);

        if (!$item) {
            return null;
        }
        $resource = sprintf('entityapi:view:%s:%s', static::ENTITY_CLASS, $item->getId());
        if (!$this->accessCheck($resource, $item->getOwner())) {
            throw new AccessDeniedException(sprintf('Access Denied to "%s"', $resource), null, null, $resource);
        }
        return $item;
    }

    /**
     * @param $id
     * @return \PhpOption\Option
     */
    public function get($id)
    {
        $item = $this->getRaw($id);
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
            /** @var EntityInterface $item */
            $item = $this->get($id)->getOrThrow(
                new NotFoundException("Exist entity with is {$id} not found")
            );
        } else {
            $item = $this->getOperator()->create(static::ENTITY_CLASS);
        }

        $this->getOperator()->load($item, $data);
        return $this->saveEntity($item);
    }

    public function saveEntity(EntityInterface $entity)
    {
        if ($entity->isExistingEntity()) {
            $resource = sprintf('entityapi:save:%s:%s', static::ENTITY_CLASS, $entity->getId());
        } else {
            $resource = sprintf('entityapi:add:%s', static::ENTITY_CLASS);
        }
        if (!$this->accessCheck($resource)) {
            throw new AccessDeniedException(sprintf('Access Denied to "%s"', $resource), null, null, $resource);
        }

        /** @var  $entity EntityInterface */
        $entity->setChanged(new \DateTime());

        if (!$entity->isExistingEntity()) {
            $entity->setOwner($this->getCustodian()->getCurrentUser());
        }

        $this->getOperator()->save($entity);

        return $entity;
    }

    public function delete($id)
    {
        /** @var EntityInterface $item */
        $item = $this->get($id)->getOrThrow(
            new NotFoundException(sprintf('Entity with id %s not found', dechex($id)))
        );
        return $this->deleteEntity($item);
    }

    public function deleteEntity(EntityInterface $entity)
    {
        $resource = sprintf('entityapi:delete:%s:%s', static::ENTITY_CLASS, $entity->getId());
        if (!$this->accessCheck($resource, $entity->getOwner())) {
            throw new AccessDeniedException(sprintf('Access Denied to "%s"', $resource), null, null, $resource);
        }

        return $this->getOperator()->delete($entity);

    }

    public function getFields()
    {
        $command = new WorkerInfoCommand(KeeperInterface::WORKER_INFO_FIELDS, Message::class);
        $fields = $this->getOperator()->execute($command);
        return $fields;
    }

}
