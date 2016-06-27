<?php


namespace DeltaPhp\Operator\Worker;


use DeltaPhp\Operator\Entity\EntityInterface;
use DeltaUtils\Object\Collection;
use DeltaPhp\Operator\Command\AfterCommandInterface;
use DeltaPhp\Operator\Command\CommandInterface;
use DeltaPhp\Operator\Command\CreateCommand;
use DeltaPhp\Operator\Command\EntityOperatedCommandInterface;
use DeltaPhp\Operator\Command\LoadCommand;
use DeltaPhp\Operator\Worker\Exception\NotSupportedCommand;
use DeltaPhp\Operator\DelegatingInterface;
use DeltaPhp\Operator\DelegatingTrait;

class TranslatorDataToObjectWorker implements WorkerInterface, DelegatingInterface
{
    use DelegatingTrait;

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case AfterCommandInterface::COMMAND_AFTER_FIND :
            case AfterCommandInterface::COMMAND_AFTER_GET:
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
