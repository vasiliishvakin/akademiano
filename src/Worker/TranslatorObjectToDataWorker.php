<?php


namespace DeltaPhp\Operator\Worker;

use DeltaPhp\Operator\Command\PreCommandInterface;
use DeltaPhp\Operator\Command\ReserveCommand;
use DeltaPhp\Operator\Entity\EntityInterface;
use DeltaPhp\Operator\DelegatingTrait;
use DeltaPhp\Operator\Command\CommandInterface;
use DeltaPhp\Operator\DelegatingInterface;
use DeltaPhp\Operator\Worker\Exception\NotSupportedCommand;

class TranslatorObjectToDataWorker implements WorkerInterface, DelegatingInterface
{
    use DelegatingTrait;

    const COMMAND_BEFORE_SAVE = PreCommandInterface::PREFIX_COMMAND_PRE . CommandInterface::COMMAND_SAVE;
    const COMMAND_BEFORE_DELETE = PreCommandInterface::PREFIX_COMMAND_PRE . CommandInterface::COMMAND_DELETE;

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
                throw new NotSupportedCommand($command);
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
