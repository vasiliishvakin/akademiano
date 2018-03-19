<?php


namespace Akademiano\EntityOperator\Worker;


use Akademiano\Db\Adapter\AbstractAdapter;
use Akademiano\Db\Adapter\PgsqlAdapter;
use Akademiano\Db\Adapter\D2QL\Criteria;
use Akademiano\Db\Adapter\D2QL\Select;
use Akademiano\Delegating\DelegatingInterface;
use Akademiano\Delegating\DelegatingTrait;
use Akademiano\Entity\Entity;
use Akademiano\Entity\Uuid;
use Akademiano\EntityOperator\Command\EntityCommandInterface;
use Akademiano\EntityOperator\Command\KeeperWorkerInfoCommand;
use Akademiano\EntityOperator\Command\MergeCommand;
use Akademiano\EntityOperator\Command\CountCommand;
use Akademiano\EntityOperator\Command\DeleteCommand;
use Akademiano\EntityOperator\Command\FindCommand;
use Akademiano\EntityOperator\Command\GenerateIdCommand;
use Akademiano\EntityOperator\Command\GetCommand;
use Akademiano\EntityOperator\Command\LoadCommand;
use Akademiano\EntityOperator\Command\ReserveCommand;
use Akademiano\EntityOperator\Command\SaveCommand;
use Akademiano\EntityOperator\Command\CreateCriteriaCommand;
use Akademiano\EntityOperator\Command\CreateSelectCommand;
use Akademiano\EntityOperator\WorkersMap\Filter\RelationCommandEntityClassValueExtractor;
use Akademiano\EntityOperator\WorkersMap\Filter\ParentCommandEntityClassValueExtractor;
use Akademiano\Operator\Command\AfterCommand;
use Akademiano\Operator\Command\AfterCommandInterface;
use Akademiano\Operator\Command\PreCommand;
use Akademiano\Operator\Command\PreCommandInterface;
use Akademiano\EntityOperator\Command\SelectCommand;
use Akademiano\Operator\Command\SubCommandInterface;
use Akademiano\Operator\Worker\Exception\NotSupportedCommandException;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\Operator\Worker\WorkerSelfInstancedInterface;
use Akademiano\Operator\Worker\WorkerSelfMapCommandsInterface;
use Akademiano\Operator\Worker\WorkerMappingTrait;
use Akademiano\Operator\WorkersContainer;
use Akademiano\Operator\WorkersMap\Filter\FilterFieldInterface;
use Akademiano\Operator\WorkersMap\Filter\ValueClassExtractor;
use Akademiano\Utils\ClassTools;
use Akademiano\Utils\Object\Collection;
use Akademiano\Utils\Object\Prototype\IntegerableInterface;
use Akademiano\Utils\Object\Prototype\StringableInterface;
use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Entity\EntityInterface;
use Akademiano\Utils\StringUtils;
use Akademiano\Config\ConfigurableInterface;
use Akademiano\Config\ConfigurableTrait;


abstract class PostgresEntityWorker implements DatabaseEntityStorageInterface, ConfigurableInterface, DelegatingInterface, WorkerSelfMapCommandsInterface, WorkerSelfInstancedInterface
{
    use ConfigurableTrait;
    use DelegatingTrait;
    use WorkerMappingTrait;

    const WORKER_ID = 'postgresWorker';

    const TABLE_ID = null;
    const TABLE_NAME = null;
    const FIELDS = [];
    const UNMERGED_FIELDS = [];
    const EXT_ENTITY_FIELDS = [];
    const UNSAVED_FIELDS = [];


    protected $tableId;

    /** @var  PgsqlAdapter */
    private $adapter;

    /** @var array */
    private $fields;

    /** @var array */
    private $unmergedFields;

    /** @var array */
    private $extEntityFields;

    /** @var array */
    private $unsavedFields;

    private $workerId;

    public static function getSupportedCommands(): array
    {
        return [
            FindCommand::class,
            GetCommand::class,
            CountCommand::class,
            SaveCommand::class,
            DeleteCommand::class,
            LoadCommand::class,
            ReserveCommand::class,
            MergeCommand::class,
            GenerateIdCommand::class,
            KeeperWorkerInfoCommand::class,
            CreateCriteriaCommand::class,
            CreateSelectCommand::class,
            SelectCommand::class,
            PreCommand::class,
        ];
    }

    public static function getEntityClassForMapFilter()
    {
        return Entity::class;
    }

    public static function modifyMapFieldFilter(string $command, string $fieldName, array $filter): array
    {
        switch (true) {
            case $command === PreCommand::class:
                if (is_subclass_of($filter[FilterFieldInterface::PARAM_ASSERTION], EntityInterface::class)) {
                    $filter[FilterFieldInterface::PARAM_ASSERTION] = static::getEntityClassForMapFilter();
                }
                break;
            default:
                switch ($fieldName) {
                    case EntityCommandInterface::FILTER_FIELD_ENTITY_CLASS:
                        $filter[FilterFieldInterface::PARAM_ASSERTION] = static::getEntityClassForMapFilter();
                        break;
                }
                break;
        }
        return $filter;
    }

    public static function getMapFieldFilters(string $command): ?array
    {
        switch (true) {
            case is_subclass_of($command, PreCommandInterface::class):
                return [
                    SubCommandInterface::PARAM_PARENT_COMMAND => [
                        static::modifyMapFieldFilter($command, SubCommandInterface::PARAM_PARENT_COMMAND, [
                            FilterFieldInterface::PARAM_ASSERTION => [FindCommand::class, CountCommand::class, SaveCommand::class],
                            FilterFieldInterface::PARAM_EXTRACTOR => ValueClassExtractor::class,
                        ]),
                        static::modifyMapFieldFilter($command, SubCommandInterface::PARAM_PARENT_COMMAND, [
                            FilterFieldInterface::PARAM_ASSERTION => Entity::class,
                            FilterFieldInterface::PARAM_EXTRACTOR => ParentCommandEntityClassValueExtractor::class
                        ]),
                    ]
                ];
            case is_subclass_of($command, EntityCommandInterface::class):
                return [
                    EntityCommandInterface::FILTER_FIELD_ENTITY_CLASS =>
                        static::modifyMapFieldFilter($command, EntityCommandInterface::FILTER_FIELD_ENTITY_CLASS, [
                            FilterFieldInterface::PARAM_ASSERTION => Entity::class,
                            FilterFieldInterface::PARAM_EXTRACTOR => RelationCommandEntityClassValueExtractor::class,
                        ]),
                ];
            default:
                return null;
        }
    }


    public static function getSelfInstance(WorkersContainer $container): WorkerInterface
    {
        $worker = new static();
        $adapter = $container->getOperator()->getDependencies()[AbstractAdapter::RESOURCE_ID];
        $worker->setAdapter($adapter);
        return $worker;
    }

    /**
     * @return PgsqlAdapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param PgsqlAdapter $adapter
     */
    public function setAdapter(PgsqlAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return static::TABLE_NAME;
    }

    public function getWorkerId()
    {
        if (null === $this->workerId) {
            if ($parentClass = get_parent_class($this)) {
                $parentWID = constant($parentClass . '::' . 'WORKER_ID');
                if (null === $parentWID) {
                    throw new \LogicException(sprintf('Empty worker id in parent class "%s" on process worker id generation for class "%s"', $parentClass, get_class()));
                }
                if (static::WORKER_ID === $parentWID) {
                    throw new \LogicException(sprintf('Worker id in current class "%s" is same as in parent class "%s"', get_class(), $parentClass));
                }
            }
            $this->workerId = static::WORKER_ID;
        }
        return $this->workerId;
    }

    /**
     * @return int
     */
    public function getTableId()
    {
        if (null === $this->tableId) {
            $tableId = null;
            //try get from const
            if (!empty(static::TABLE_ID)) {
                $tableId = static::TABLE_ID;
            }

            if (!$tableId) {
                //try get from config
                $path = ['entityOperator', 'workers', $this->getWorkerId()];
                $tableId = $this->getConfig($path);
            }

            //then get from parent and increment
            if (!$tableId) {
                $parentClass = get_parent_class();
                $parentWorkerId = constant($parentClass . '::' . 'WORKER_ID');
                $tableId = $this->delegate(new KeeperWorkerInfoCommand($parentWorkerId, KeeperWorkerInfoCommand::ATTRIBUTE_TABLE_ID));
                if (empty($tableId)) {
                    throw new \RuntimeException(sprintf('Could not generate table id for worker', static::WORKER_ID));
                }
                $tableId = $tableId + 1;
            }
            $this->tableId = $tableId;
        }
        return $this->tableId;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        if (null === $this->fields) {
            $this->fields = ClassTools::getClassTreeArrayConstant(get_class($this), 'FIELDS');
        }
        return $this->fields;
    }

    /**
     * @return array
     */
    public function getUnmergedFields(): array
    {
        if (null === $this->unmergedFields) {
            $this->unmergedFields = ClassTools::getClassTreeArrayConstant(get_class($this), 'UNMERGED_FIELDS');
        }
        return $this->unmergedFields;
    }

    public function getExtEntityFields(): array
    {
        if (null === $this->extEntityFields) {
            $this->extEntityFields = ClassTools::getClassTreeArrayConstant(get_class($this), 'EXT_ENTITY_FIELDS');
        }
        return $this->extEntityFields;
    }

    /**
     * @return array
     */
    public function getUnsavedFields(): array
    {
        if (null === $this->unsavedFields) {
            $this->unsavedFields = ClassTools::getClassTreeArrayConstant(get_class($this), 'UNSAVED_FIELDS');
        }
        return $this->unsavedFields;
    }

    /**
     * @param CommandInterface $command
     * @return mixed
     * @throws NotSupportedCommandException
     */
    public function execute(CommandInterface $command)
    {
        switch (true) {
            case $command instanceof KeeperWorkerInfoCommand:
                $attribute = $command->getAttribute();
                return $this->getAttribute($attribute);
                break;
            case $command instanceof CountCommand:
                $criteria = $command->getCriteria();
                return $this->count($criteria);
            case $command instanceof FindCommand:
                $criteria = $command->getCriteria();
                $limit = $command->getLimit();
                $offset = $command->getOffset();
                $orderBy = $command->getOrderBy();
                return $this->find($criteria, $limit, $offset, $orderBy);
            case $command instanceof GetCommand:
                $id = $command->getId();
                return $this->get($id);
            case $command instanceof SaveCommand:
                /** @var EntityInterface $entity */
                $entity = $command->getEntity();
                $isExisting = $entity->isExistingEntity();
                $data = $command->getData();
                return $this->save($data, $isExisting);
            case $command instanceof DeleteCommand:
                $id = $command->getEntityId();
                return $this->delete($id);
            case $command instanceof LoadCommand:
                return $this->load($command->getEntity(), $command->getData());
            case $command instanceof ReserveCommand:
                return $this->reserve($command->getEntity());
            case $command instanceof MergeCommand:
                $entityA = $command->getEntity();
                $entityB = $command->getEntityMerged();
                return $this->merge($entityA, $entityB);
            case $command instanceof GenerateIdCommand:
                return $this->genId();
            case $command instanceof CreateCriteriaCommand:
                return $this->createCriteria();
            case $command instanceof CreateSelectCommand:
                return $this->createSelect();
            case $command instanceof SelectCommand:
                return $this->select($command->getParams("select"));
            case $command instanceof PreCommandInterface:
                $parentCommand = $command->getParentCommand();
                switch (true) {
                    case $parentCommand instanceof FindCommand:
                    case $parentCommand instanceof CountCommand:
                        $criteria = $parentCommand->getCriteria();
                        if (null !== $criteria && (!$criteria instanceof Criteria)) {
                            $criteria = $this->filterCriteria($criteria);
                            $parentCommand->setCriteria($criteria);
                        }
                        break;
                    case $parentCommand instanceof SaveCommand:
                        if (!$parentCommand->getEntity()->isExistingEntity()) {
                            $id = $parentCommand->getEntity()->getId();
                            if (!$id) {
                                $id = $this->delegate(new GenerateIdCommand($parentCommand->getEntityClass()));
                                $parentCommand->getEntity()->setId($id);
                                $data = $parentCommand->getData();
                                if ($data) {
                                    $data['id'] = $parentCommand->getEntity()->getId()->getInt();
                                    $parentCommand->setData($data);
                                }
                            }
                        }
                        break;
                }
                return $command;
            default:
                throw new NotSupportedCommandException($command);
        }
    }

    protected function findByCriteria(Criteria $criteria, $limit = null, $offset = null, $orderBy = null)
    {
        $adapter = $this->getAdapter();
        $table = $this->getTable();
        $query = (new Select($adapter))
            ->addTable($table)
            ->addField("__TABLE__.*", $table, true)
            ->setCriteria($criteria);
        $sql = $query->toSql();
        $data = $adapter->select($sql);
        $data = new Collection($data);
        return $data;
    }

    protected function findByArray(array $criteria, $limit = null, $offset = null, $orderBy = null)
    {
        $adapter = $this->getAdapter();
        $table = $this->getTable();
        $data = $adapter->selectBy($table, $criteria, $limit, $offset, $orderBy);
        $data = new Collection($data);
        return $data;
    }

    public function find($criteria, $limit = null, $offset = null, $orderBy = null)
    {
        if ($criteria instanceof Criteria) {
            return $this->findByCriteria($criteria, $limit, $offset, $orderBy);
        } else {
            return $this->findByArray($criteria, $limit, $offset, $orderBy);
        }
    }

    public function findOne($criteria)
    {
        $data = $this->find($criteria, 1);
        return $data->first();
    }

    protected function countByCriteria(Criteria $criteria = null)
    {
        $adapter = $this->getAdapter();
        $table = $this->getTable();
        $query = (new Select($adapter))
            ->addTable($table)
            ->addField("count(*)", null, true)
            ->setCriteria($criteria);
        $sql = $query->toSql();
        $data = $adapter->selectCell($sql);
        return (integer)$data;
    }

    protected function countByArray(array $criteria = null)
    {
        $adapter = $this->getAdapter();
        $table = $this->getTable();
        return (integer)$adapter->count($table, $criteria);
    }

    public function count($criteria)
    {
        if ($criteria instanceof Criteria) {
            return $this->countByCriteria($criteria);
        } else {
            return $this->countByArray($criteria);
        }
    }

    public function get($id)
    {
        if (null === $id) {
            return null;
        }
        return $this->findOne(["id" => $id]);
    }

    public function save(array $data, $isExisting)
    {
        $adapter = $this->getAdapter();
        $table = $this->getTable();

        //try remove unsaved fields
        $unsavedFields = $this->getUnsavedFields();
        if ($unsavedFields) {
            $unsavedFields = array_flip($unsavedFields);
            $data = array_diff_key($data, $unsavedFields);
        }

        if (!$isExisting) {
            return $adapter->insert($table, $data);
        } else {
            $id = $data["id"];
            unset($data["id"]);
            return $adapter->update($table, $data, ["id" => $id]);
        }
    }

    public function delete($id)
    {
        $adapter = $this->getAdapter();
        $table = $this->getTable();
        return $adapter->delete($table, ["id" => $id]);
    }

    public function load(EntityInterface $entity, array $data)
    {
        $fields = $this->getFields();
        $extEntityFields = $this->getExtEntityFields();
        foreach ($fields as $field) {
            $objectAttribute = StringUtils::lowDashToCamelCase($field);
            $valueExist = array_key_exists($objectAttribute, $data) ? true : ((array_key_exists($field, $data) ? true : false));
            if ($valueExist) {
                $value = isset($data[$objectAttribute]) ? $data[$objectAttribute] : $data[$field];
                if (in_array($field, $extEntityFields)) {
                    if (is_string($value)) {
                        $value = Uuid::normalize($value);
                    }
                }
                $method = "set" . ucfirst($objectAttribute);
                if (method_exists($entity, $method)) {
                    $entity->{$method}($value);
                }
            }
        }
        return $entity;
    }

    public function reserve(EntityInterface $entity)
    {
        $fields = $this->getFields();
        $data = [];
        foreach ($fields as $field) {
            $objectField = StringUtils::lowDashToCamelCase($field);
            $getMethod = "get" . ucfirst($objectField);
            if (method_exists($entity, $getMethod) && is_callable([$entity, $getMethod])) {
                $fieldValue = $entity->$getMethod();
                $fieldValue = $this->filterFieldToPostgresType($fieldValue, $field, $entity);
                $data[$field] = $fieldValue;
            } else {
                $isMethod = "is" . ucfirst($objectField);
                if (method_exists($entity, $isMethod) && is_callable([$entity, $isMethod])) {
                    $fieldValue = $entity->$isMethod();
                    $fieldValue = $this->filterFieldToPostgresType($fieldValue, $field, $entity);
                    $data[$field] = $fieldValue;
                }
            }
        }
        return $data;
    }

    public function filterFieldToPostgresType($value, $fieldName = null, EntityInterface $entity = null)
    {
        if ($value instanceof EntityInterface) {
            $id = $value->getId();
            if ($id instanceof IntegerableInterface) {
                return $id->getValue();
            } elseif (is_numeric($id)) {
                return (int)$id;
            } elseif (is_scalar($id)) {
                return $id;
            } else {
                return null;
            }
        } elseif ($value instanceof \DateTime) {
            return $value->format("Y-m-d H:i:s");
        } elseif (is_bool($value)) {
            return $value ? 't' : 'f';
        } elseif ($value instanceof IntegerableInterface) {
            return $value->getInt();
        } elseif ($value instanceof StringableInterface) {
            return $value->__toString();
        } else {
            return $value;
        }
    }

    public function merge(EntityInterface $entityA, EntityInterface $entityB)
    {
        $fields = $this->getFields();
        $unmergedFields = $this->getUnmergedFields();
        $mergedFields = array_diff($fields, $unmergedFields);

        foreach ($mergedFields as $field) {
            $methodSet = "set" . ucfirst($field);
            $methodGet = "get" . ucfirst($field);
            $methodIs = "is" . ucfirst($field);
            if (method_exists($entityA, $methodSet)) {
                if (method_exists($entityB, $methodGet)) {
                    $entityA->{$methodSet}($entityB->{$methodGet}());
                } elseif (method_exists($entityB, $methodIs)) {
                    $entityA->{$methodSet}((bool)$entityB->{$methodIs}());
                }
            }
        }
        return $entityA;
    }

//TODO Check use tableId
    public function genId()
    {
        $tableIdRaw = $this->getTableId();
        $tableId = filter_var($tableIdRaw, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 512]]);
        if (false === $tableId) {
            throw  new \InvalidArgumentException("Table id {$tableIdRaw} not in range");
        }
        $sql = "select uuid_short_complex_tables({$tableId})";
        $adapter = $this->getAdapter();
        $result = $adapter->selectCell($sql);
        return $result;
    }

    public function filterCriteria(array $criteria)
    {
        foreach ($criteria as $key => $value) {
            $criteria[$key] = $this->filterFieldToPostgresType($value, $key);
        }
        return $criteria;
    }

    public function getAttribute($attribute, array $params = [])
    {
        switch ($attribute) {
            case KeeperWorkerInfoCommand::ATTRIBUTE_TABLE_ID :
                {
                    return $this->getTableId();
                    break;
                }
            case KeeperWorkerInfoCommand::ATTRIBUTE_TABLE_NAME :
                {
                    return $this->getTable();
                    break;
                }
            case KeeperWorkerInfoCommand::ATTRIBUTE_FIELDS :
                {
                    return $this->getFields();
                    break;
                }
            default:
                throw new \InvalidArgumentException(sprintf('"%s" command attribute "%d" not supported', KeeperWorkerInfoCommand::class, $attribute));
        }
    }

    public function createCriteria()
    {
        $criteria = new Criteria();
        $criteria->setAdapter($this->getAdapter());
        return $criteria;
    }

    public function createSelect()
    {
        $select = new Select();
        $select->setAdapter($this->getAdapter());
        $select->addTable($this->getTable());
        return $select;
    }

    public function select(Select $select)
    {
        $select->addTable($this->getTable());
        $result = $this->getAdapter()->selectAndSmartFetch($select->toSql());
        return $result;
    }
}
