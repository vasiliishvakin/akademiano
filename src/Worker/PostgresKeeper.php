<?php


namespace EntityOperator\Worker;


use DeltaDb\Adapter\PgsqlAdapter;
use EntityOperator\Command\CommandInterface;
use EntityOperator\Operator\FinderInterface;
use EntityOperator\Operator\KeeperInterface;

class PostgresKeeper implements WorkerInterface, KeeperInterface, FinderInterface
{
    /** @var  PgsqlAdapter */
    protected $adapter;

    protected $table = "objects";
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
        $this->fields[] = $field;
    }

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case CommandInterface::COMMAND_FIND : {
                $criteria = $command->getParams("criteria", null);
                $limit = $command->getParams("limit", null);
                $offset = $command->getParams("offset", null);
                $order = $command->getParams("order", null);
                return $this->find($criteria, $limit, $offset, $order);
            }
            case CommandInterface::COMMAND_GET: {
                $id = $command->getParams("id");
                return $this->get($id);
            }
            default:
                throw new \InvalidArgumentException("Command type \" {$command->getName()} not supported");
        }
    }

    public function find($criteria, $limit = null, $offset = null,  $orderBy = null)
    {
        $adapter = $this->getAdapter();
        $table = $this->getTable();
        return $adapter->selectBy($table, $criteria, $limit, $offset, $orderBy);
    }


    public function get($id)
    {
        if (null === $id) {
            return null;
        }
        return $this->find(["id" => $id], 1);
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