<?php


namespace EntityOperator\Worker;


use DeltaDb\Adapter\PgsqlAdapter;
use DeltaUtils\Object\Collection;
use EntityOperator\Command\CommandInterface;
use EntityOperator\Operator\FinderInterface;
use EntityOperator\Operator\KeeperInterface;

class PostgresWorker implements WorkerInterface, KeeperInterface, FinderInterface
{
    /** @var  PgsqlAdapter */
    protected $adapter;

    protected $table = "entities";
    /** @var array  */
    protected $fields= [
        "id",
        "created",
        "changed",
        "owner",
    ];

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
        return $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    public function addField($field)
    {
        $this->fields[$field] = $field;
    }

    public function addFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case CommandInterface::COMMAND_FIND : {
                $criteria = $command->getParams("criteria", []);
                $limit = $command->getParams("limit", null);
                $offset = $command->getParams("offset", null);
                $order = $command->getParams("order", null);
                return $this->find($command->getClass(), $criteria, $limit, $offset, $order);
            }
            case CommandInterface::COMMAND_GET: {
                $id = $command->getParams("id");
                return $this->get($command->getClass(), $id);
            }
            case CommandInterface::COMMAND_COUNT: {
                $criteria = $command->getParams("criteria", []);
                return $this->count($command->getClass(), $criteria);
            }
            default:
                throw new \InvalidArgumentException("Command type \" {$command->getName()} not supported");
        }
    }

    public function find($class = null, $criteria, $limit = null, $offset = null,  $orderBy = null)
    {
        $adapter = $this->getAdapter();
        $table = $this->getTable();
        $data = $adapter->selectBy($table, $criteria, $limit, $offset, $orderBy);
        $data = new Collection($data);
        return $data;
    }

    public function findOne($criteria)
    {
        $adapter = $this->getAdapter();
        $table = $this->getTable();
        $data = $adapter->selectBy($table, $criteria, 1);
        if (empty($data)) {
            return null;
        }
        return reset($data);
    }

    public function count($class = null, $criteria)
    {
        $adapter = $this->getAdapter();
        $table = $this->getTable();
        return $adapter->count($table, $criteria);
    }

    public function get($class = null, $id)
    {
        if (null === $id) {
            return null;
        }
        return $this->findOne(["id" => $id]);
    }

    public function save($entity)
    {
        // TODO: Implement save() method.
    }

    public function delete($entity)
    {
        // TODO: Implement delete() method.
    }

}
