<?php


namespace Akademiano\EntityOperator\Worker;

use Akademiano\EntityOperator\Command\DeleteCommand;
use Akademiano\EntityOperator\Command\SaveCommand;
use Akademiano\Operator\Command\PreCommandInterface;
use Akademiano\EntityOperator\Command\ReserveCommand;
use Akademiano\Entity\EntityInterface;
use Akademiano\Operator\DelegatingTrait;
use Akademiano\Operator\Command\CommandInterface;
use Akademiano\Operator\DelegatingInterface;
use Akademiano\Operator\Worker\Exception\NotSupportedCommandException;
use Akademiano\Operator\Worker\WorkerMetaMapPropertiesTrait;

class TranslatorObjectToDataWorker implements WorkerInterface, DelegatingInterface
{
    use WorkerMetaMapPropertiesTrait;
    use DelegatingTrait;

    const COMMAND_BEFORE_SAVE = PreCommandInterface::PREFIX_COMMAND_PRE . SaveCommand::COMMAND_NAME;
    const COMMAND_BEFORE_DELETE = PreCommandInterface::PREFIX_COMMAND_PRE . DeleteCommand::COMMAND_NAME;

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case self::COMMAND_BEFORE_SAVE :
                /** @var PreCommandInterface $command */
                $result = $this->translate($command);
                break;
            case self::COMMAND_BEFORE_DELETE :
                $result = ["id" => $this->getId($command)];
                break;
            default:
                throw new NotSupportedCommandException($command);
        }
        $command->addParams($result);
        return $result;
    }

    public function translate(PreCommandInterface $command)
    {
        $params = $command->getParams();
        $params["data"] = $this->toData($params["entity"]);
        return $params;
    }

    public function toData(EntityInterface $entity)
    {
        $command = new ReserveCommand($entity);
        return $this->delegate($command);
    }

    public function getId(CommandInterface $command)
    {
        $entity = $command->getParams("entity");
        if (is_object($entity)) {
            if ($entity instanceof EntityInterface) {
                return $entity->getId();
            } else {
                throw new \InvalidArgumentException("entity object not instance of EntityInterface");
            }
        }
        return $entity;
    }
}
