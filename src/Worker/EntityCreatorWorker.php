<?php


namespace Akademiano\EntityOperator\Worker;


use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Command\CreateCommand;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\EntityOperator\CreatorInterface;
use Akademiano\Operator\IncludeOperatorInterface;
use Akademiano\Operator\Worker\WorkerMetaMapPropertiesTrait;
use Akademiano\Operator\IncludeOperatorTrait;
use Akademiano\Operator\Command\CommandInterface;
use Akademiano\Entity\Entity;
use Carbon\Carbon;


class EntityCreatorWorker implements WorkerInterface, CreatorInterface, IncludeOperatorInterface
{
    use WorkerMetaMapPropertiesTrait;
    use IncludeOperatorTrait;

    protected static function getDefaultMapping()
    {
        return [
            CreateCommand::COMMAND_NAME => null,
        ];
    }

    public function create($class = null, array $params = [])
    {
        if (null === $class) {
            $class = Entity::class;
        }

        if ($class[0] !== "\\") {
            $class = "\\" . $class;
        }
        $entity = new $class();
        if ($entity instanceof IncludeOperatorInterface) {
            $entity->setOperator($this->getOperator());
        }
        if ($entity instanceof EntityInterface) {
            $entity->setCreated(new Carbon());
        }
        return $entity;
    }

    public function execute(CommandInterface $command)
    {
        if ($command->getName() !== CreateCommand::COMMAND_NAME) {
            throw new \InvalidArgumentException("Command type \" {$command->getName()} not supported");
        }
        return $this->create($command->getClass(), $command->getParams());
    }
}
