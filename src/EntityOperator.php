<?php


namespace DeltaPhp\Operator;


use DeltaUtils\Object\Collection;
use DeltaPhp\Operator\Command\CountCommand;
use DeltaPhp\Operator\Command\CreateCommand;
use DeltaPhp\Operator\Command\DeleteCommand;
use DeltaPhp\Operator\Command\FindCommand;
use DeltaPhp\Operator\Command\GenerateIdCommand;
use DeltaPhp\Operator\Command\GetCommand;
use DeltaPhp\Operator\Command\LoadCommand;
use DeltaPhp\Operator\Command\SaveCommand;
use DeltaPhp\Operator\Entity\EntityInterface;
use DeltaPhp\Operator\Entity\Entity;

class EntityOperator extends Operator implements OperatorInterface
{
   
    public function create($class = null, array $params= [])
    {
        $command = new CreateCommand($class, $params);
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
     * @return Collection|EntityInterface[]
     */
    public function find($class = null, $criteria = [], $limit = null, $offset = null, $orderBy = null)
    {
        $command = new FindCommand($class, $criteria, $limit, $offset, $orderBy);
        $data =  $this->execute($command);
        return $data;
    }

    /**
     * @param null $class
     * @param $id
     * @return EntityInterface
     */
    public function get($class = null, $id)
    {
        $command = new GetCommand((string) $id, $class);
        $data =  $this->execute($command);
        return $data;
    }

    public function save(EntityInterface $entity)
    {
        $command = new SaveCommand($entity);
        $result = $this->execute($command);
        return $result;
    }

    public function delete(EntityInterface $entity)
    {
        $command = new DeleteCommand($entity);
        return $this->execute($command);
    }

    public function count($class = null, $criteria = [])
    {
        $command = new CountCommand(["criteria" => $criteria], $class);
        return $this->execute($command);
    }

    public function genId($class = Entity::class)
    {
        $command = new GenerateIdCommand($class);
        return $this->execute($command);
    }
}
