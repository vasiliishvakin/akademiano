<?php


namespace DeltaPhp\Operator\Worker;


use DeltaPhp\Operator\Command\AfterCommandInterface;
use DeltaPhp\Operator\Command\CommandInterface;
use DeltaPhp\Operator\Command\GenerateIdCommandInterface;
use DeltaPhp\Operator\Command\PreAfterCommandInterface;
use DeltaPhp\Operator\DelegatingInterface;
use DeltaPhp\Operator\DelegatingTrait;
use UUID\Model\Command\CreateUuidCommand;

class IntIdToUuidObjectWorker implements WorkerInterface, DelegatingInterface
{
    use DelegatingTrait;

    const COMMAND_AFTER_GENERATE_ID = AfterCommandInterface::PREFIX_COMMAND_AFTER . GenerateIdCommandInterface::COMMAND_GENERATE_ID;

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case self::COMMAND_AFTER_GENERATE_ID :
                /** @var AfterCommandInterface $command */
                $result = $this->toId($command);
                $command->addResult($result);
                return $result;
            default:
                throw new NotSupportedCommand($command);
        }
    }

    public function toId(AfterCommandInterface $command)
    {
        $result = $command->extractResult();
        $command = new CreateUuidCommand($result);
        return $this->delegate($command);
    }
}
