<?php


namespace EntityOperator;


use EntityOperator\Command\FindCommand;
use EntityOperator\Operator\EntityOperatorInterface;
use EntityOperator\Operator\Operator;

class EntityOperator extends Operator implements EntityOperatorInterface
{
    public function create()
    {
        // TODO: Implement create() method.
    }

    public function find($criteria, $limit = null, $offset = null)
    {
        $command = new FindCommand(["criteria" => $criteria, "limit" => $limit, "offset" => $offset]);
        return $this->execute($command);
    }

    public function get($id)
    {
        // TODO: Implement get() method.
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