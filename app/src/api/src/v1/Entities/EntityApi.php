<?php


namespace Akademiano\Api\v1\Entities;


use Akademiano\Api\v1\AbstractApi;
use Akademiano\Delegating\DelegatingInterface;
use Akademiano\Delegating\DelegatingTrait;
use Akademiano\Delegating\OperatorInterface;
use Akademiano\DI\Container;
use Akademiano\Entity\BaseEntity;
use Akademiano\Entity\Entity;
use Akademiano\Entity\EntityInterface;
use Akademiano\Entity\Uuid;
use Akademiano\EntityOperator\Command\CountCommand;
use Akademiano\EntityOperator\Command\CreateCommand;
use Akademiano\EntityOperator\Command\DeleteCommand;
use Akademiano\EntityOperator\Command\FindCommand;
use Akademiano\EntityOperator\Command\GetCommand;
use Akademiano\EntityOperator\Command\KeeperWorkerInfoCommand;
use Akademiano\EntityOperator\Command\LoadCommand;
use Akademiano\EntityOperator\Command\SaveCommand;
use Akademiano\EntityOperator\Worker\EntitiesWorker;
use Akademiano\EntityOperator\Worker\KeeperInterface;
use Akademiano\Operator\Command\GetWorkerCommand;
use Akademiano\Operator\Command\WorkerInfoCommand;
use Akademiano\Operator\Operator;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\User\CustodianIncludeInterface;
use Akademiano\User\CustodianIncludeTrait;
use Akademiano\Utils\Parts\ResourceBuilderTrait;
use Akademiano\Utils\ResourceBuilderInterface;
use Akademiano\UUID\Command\UuidCreateCommand;
use Akademiano\UUID\UuidComplexShortTables;
use PhpOption\Some;
use PhpOption\None;
use Akademiano\Utils\Paging\PagingMetadata;
use Akademiano\Api\v1\Items\ItemsPage;
use Akademiano\Core\Exception\AccessDeniedException;
use Akademiano\HttpWarp\Exception\NotFoundException;

class EntityApi extends AbstractApi implements EntityApiInterface, CustodianIncludeInterface, DelegatingInterface, ResourceBuilderInterface
{
    const ENTITY_CLASS = Entity::class;
    const API_ID = 'entityApi';

    use DelegatingTrait;
    use CustodianIncludeTrait;
    use ResourceBuilderTrait;

    final public function __construct(OperatorInterface $operator)
    {
        $this->setOperator($operator);
    }

    public function intToUuidCST($value): UuidComplexShortTables
    {
        return $this->delegate(
            (new UuidCreateCommand(UuidComplexShortTables::class))
                ->setValue($value)
        );
    }


    public function count($criteria = null)
    {
        return $this->delegate(
            (new CountCommand(static::ENTITY_CLASS))
                ->setCriteria($criteria ?? [])
        );
    }

    public function getDefaultOrder()
    {
        return static::DEFAULT_ORDER;
    }

    //TODO use Identity Map
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
        $items = $this->delegate(
            (new FindCommand(static::ENTITY_CLASS))
                ->setCriteria($criteria)
                ->setLimit($itemsPerPage)
                ->setOffset($pagingMetadata->getItemsOffset())
                ->setOrderBy($orderBy)
        );
        return new ItemsPage($items, $pagingMetadata);
    }

    public function findOne($criteria = null)
    {
        if (null === $criteria) {
            $criteria = ["owner" => $this->getCurrentUser()];
        }

        /** @var EntityInterface $item */
        $item = $this->delegate(
            (new FindCommand(static::ENTITY_CLASS))
                ->setCriteria($criteria)
                ->setLimit(1)
        )->firstOrFalse();

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

        $items = $item = $this->delegate(
            (new FindCommand(static::ENTITY_CLASS))
                ->setCriteria($criteria)
                ->setOrderBy($orderBy)
        );

        return $items;
    }

    public function getRaw($id)
    {
        $item = $this->delegate(
            (new GetCommand(static::ENTITY_CLASS))
                ->setId($id)
        );

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
            $item = $this->delegate(new CreateCommand(static::ENTITY_CLASS));
        }

        $this->delegate((new LoadCommand($item))->setData($data));
        return $this->saveEntity($item);
    }

    public function saveEntity(EntityInterface $entity)
    {
        $owner = null;
        if ($entity->isExistingEntity()) {
            $resource = sprintf('entityapi:save:%s:%s', static::ENTITY_CLASS, $entity->getId());
            $owner = $entity->getOwner();
        } else {
            $resource = sprintf('entityapi:add:%s', static::ENTITY_CLASS);
        }
        if (!$this->accessCheck($resource, $owner)) {
            throw new AccessDeniedException(sprintf('Access Denied to "%s"', $resource), null, null, $resource);
        }

        /** @var  $entity EntityInterface */
        $entity->setChanged(new \DateTime());

        if (!$entity->isExistingEntity()) {
            $entity->setOwner($this->getCustodian()->getCurrentUser());
        }

        $this->delegate(new SaveCommand($entity));

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

        return $this->delegate(new DeleteCommand($entity));

    }

    protected function getWorker(?string $entityClass = null): WorkerInterface
    {
        if (null === $entityClass) {
            $entityClass = static::ENTITY_CLASS;
        }
        return $this->delegate(new GetWorkerCommand(new FindCommand($entityClass)));
    }

    public function getWorkerId(?string $entityClass = null): string
    {
        $workerClass = get_class($this->getWorker($entityClass));
        $constantName = $workerClass . "::WORKER_ID";
        if (!defined($constantName)) {
            throw new \Exception(sprintf('Constant %s not defined %s', "WORKER_ID", $workerClass));
        }
        return constant($constantName);
    }

    public function getFields(?string $entityClass = null): array
    {
        $workerId = $this->getWorkerId($entityClass);
        $command = new KeeperWorkerInfoCommand($workerId, KeeperWorkerInfoCommand::ATTRIBUTE_FIELDS);
        return $this->delegate($command);
    }

    public function getUntouchablesFields(): array
    {
        return $this->getFields(Entity::class);
    }

    public function getFormFields(): array
    {
        $fields = $this->getFields();
        $untouchablesFields = $this->getUntouchablesFields();
        $fields = array_diff_key($fields, $untouchablesFields);
        return $fields;
    }

    public static function build(\Pimple\Container $container): object
    {
        return new static($container[Operator::RESOURCE_ID]);
    }

//    /**
//     * @return \Closure
//     * return function for instance in DI container
//     */
//    public static function getBuilder(): \Closure
//    {
//        return function (\Pimple\Container $c) {
//            return new static($c[Operator::RESOURCE_ID]);
//        };
//    }
}
