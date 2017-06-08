<?php


namespace Akademiano\EntityOperator;

use Akademiano\Operator\Operator;
use Akademiano\Utils\Object\Collection;
use Akademiano\EntityOperator\Command\CountCommand;
use Akademiano\EntityOperator\Command\CreateCommand;
use Akademiano\EntityOperator\Command\DeleteCommand;
use Akademiano\EntityOperator\Command\FindCommand;
use Akademiano\EntityOperator\Command\GenerateIdCommand;
use Akademiano\EntityOperator\Command\GetCommand;
use Akademiano\EntityOperator\Command\LoadCommand;
use Akademiano\EntityOperator\Command\SaveCommand;
use Akademiano\Entity\EntityInterface;
use Akademiano\Entity\Entity;
use Akademiano\Db\Adapter\D2QL\Criteria;

class EntityOperator extends Operator implements EntityOperatorInterface
{

    public function create($class = null, array $params = [])
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
     * @param Criteria|array $criteria
     * @param null $limit
     * @param null $offset
     * @param array|string|null $orderBy
     * @return Collection|EntityInterface[]
     */
    public function find($class = null, $criteria = null, $limit = null, $offset = null, $orderBy = null)
    {
        $command = new FindCommand($class, $criteria, $limit, $offset, $orderBy);
        $data = $this->execute($command);
        return $data;
    }

    /**
     * @param null $class
     * @param $id
     * @return EntityInterface
     */
    public function get($class = null, $id)
    {
        $command = new GetCommand((string)$id, $class);
        $data = $this->execute($command);
        return $data;
    }

    public function save(EntityInterface $entity)
    {
        if (null === $entity->getId() && method_exists($entity, "setId")) {
            $id = $this->genId(get_class($entity));
            $entity->setId($id);
        }

        $command = new SaveCommand($entity);
        $result = $this->execute($command);
        return $result;
    }

    public function delete(EntityInterface $entity)
    {
        $command = new DeleteCommand($entity);
        return $this->execute($command);
    }

    public function count($class = null, $criteria = null)
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
