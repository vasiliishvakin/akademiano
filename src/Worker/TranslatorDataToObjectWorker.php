<?php


namespace Akademiano\EntityOperator\Worker;


use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Command\FindCommand;
use Akademiano\EntityOperator\Command\GetCommand;
use Akademiano\Utils\Object\Collection;
use Akademiano\Operator\Command\AfterCommandInterface;
use Akademiano\Operator\Command\CommandInterface;
use Akademiano\EntityOperator\Command\CreateCommand;
use Akademiano\EntityOperator\Command\EntityOperatedCommandInterface;
use Akademiano\EntityOperator\Command\LoadCommand;
use Akademiano\Operator\Worker\Exception\NotSupportedCommand;
use Akademiano\Operator\DelegatingInterface;
use Akademiano\Operator\DelegatingTrait;
use Akademiano\Operator\Worker\WorkerMetaMapPropertiesTrait;

class TranslatorDataToObjectWorker implements WorkerInterface, DelegatingInterface
{
    use WorkerMetaMapPropertiesTrait;
    use DelegatingTrait;

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case AfterCommandInterface::PREFIX_COMMAND_AFTER . FindCommand::COMMAND_NAME :
            case AfterCommandInterface::PREFIX_COMMAND_AFTER . GetCommand::COMMAND_NAME:
                /** @var AfterCommandInterface $command */
                $result = $this->translate($command);
                $command->addResult($result);
                return $result;
            default:
                throw new NotSupportedCommand($command);
        }
    }

    public function translate(AfterCommandInterface $command)
    {
        $result = $command->extractResult();
        if (null === $result) {
            return null;
        }
        $class = $command->hasClass() ? $command->getClass() : EntityOperatedCommandInterface::DEFAULT_CLASS;
        if ($result instanceof Collection) {
            $items = clone $result;
            $items->map(function ($itemData) use ($class) {
                if ($itemData instanceof EntityInterface) {
                    return $itemData;
                }
                $entity = $this->toEntity($itemData, $class);
                return $entity;
            });
            return $items;
        } else {
            return $this->toEntity($result, $class);
        }
    }

    public function toEntity(array $entityData, $entityClass)
    {
        if (empty($entityData)) {
            return null;
        }
        $createCommand = new CreateCommand($entityClass);
        $entity = $this->getOperator()->execute($createCommand);

        $loadCommand = new LoadCommand($entity, $entityData);
        $entity = $this->getOperator()->execute($loadCommand);
        return $entity;
    }
}
