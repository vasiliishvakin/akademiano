<?php


namespace EntityOperator\Worker;


use DeltaUtils\Object\Collection;
use EntityOperator\Command\AfterCommandInterface;
use EntityOperator\Command\CommandInterface;
use EntityOperator\Command\CreateCommand;
use EntityOperator\Command\EntityOperatedCommandInterface;
use EntityOperator\Command\LoadCommand;
use EntityOperator\Worker\Exception\NotSupportedCommand;
use EntityOperator\Operator\DelegatingInterface;
use EntityOperator\Operator\DelegatingTrait;

class TranslatorDataToObjectWorker  implements WorkerInterface, DelegatingInterface
{
    const COMMAND_AFTER_FIND = AfterCommandInterface::PREFIX_COMMAND_AFTER . CommandInterface::COMMAND_FIND;

    use DelegatingTrait;

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case self::COMMAND_AFTER_FIND :
                /** @var AfterCommandInterface $command */
                $result = $this->translate($command);
                $command-> addResult($result);
                return $result;
            default:
                throw new NotSupportedCommand($command);
        }
    }

    public function translate(AfterCommandInterface $command)
    {
        $result = $command->extractResult();
        $class = $command->hasClass() ? $command->getClass() : EntityOperatedCommandInterface::DEFAULT_CLASS;
        if ($result instanceof Collection) {
            $items = clone $result;
            $items->map(function ($itemData) use ($class)  {
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
        $createCommand = new CreateCommand($entityClass);
        $entity = $this->getOperator()->execute($createCommand);

        $loadCommand = new LoadCommand($entity, $entityData);
        $entity = $this->getOperator()->execute($loadCommand);
        return $entity;
    }
}
