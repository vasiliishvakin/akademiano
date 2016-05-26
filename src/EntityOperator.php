<?php


namespace EntityOperator;


use DeltaUtils\Object\Collection;
use EntityOperator\Command\CountCommand;
use EntityOperator\Command\CreateCommand;
use EntityOperator\Command\FindCommand;
use EntityOperator\Command\GetCommand;
use EntityOperator\Command\LoadCommand;
use EntityOperator\Entity\EntityInterface;
use EntityOperator\Operator\Operator;

class EntityOperator extends Operator implements EntityOperatorInterface
{
    /**
     * @param null $class
     * @return EntityInterface
     */
    public function create($class = null)
    {
        $command = new CreateCommand($class);
        return $this->execute($command);
    }

    public function load(EntityInterface $entity, array $data)
    {
        $command = new LoadCommand($entity, $data);
        return $this->execute($command);
    }


    /**
     * @param null $class
     * @param array $criteria
     * @param null $limit
     * @param null $offset
     * @param array|string|null $orderBy
     * @return Collection
     */
    public function find($class = null, $criteria = [], $limit = null, $offset = null, $orderBy = null)
    {
        $command = new FindCommand(["criteria" => $criteria, "limit" => $limit, "offset" => $offset, "orderBy" => $orderBy], $class);
        $data =  $this->execute($command);
        return $data;
    }

    public function get($class = null, $id)
    {
        $command = new GetCommand(["id" => $id], $class);
        $data =  $this->execute($command);
        return $data;
    }

    public function save($entity)
    {
        // TODO: Implement save() method.
    }

    public function delete($entity)
    {
        // TODO: Implement delete() method.
    }

    public function count($class = null, $criteria = [])
    {
        $command = new CountCommand(["criteria" => $criteria], $class);
        return $this->execute($command);
    }
}
