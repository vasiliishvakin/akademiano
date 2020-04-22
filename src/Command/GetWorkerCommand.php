<?php


namespace Akademiano\Operator\Command;

use Akademiano\Delegating\Command\CommandInterface;

class GetWorkerCommand implements CommandInterface, OperatorSpecialCommandInterface
{
    protected CommandInterface $command;

    /**
     * GetWorkerIdCommand constructor.
     * @param CommandInterface $command
     */
    public function __construct(CommandInterface $command)
    {
        $this->command = $command;
    }

    /**
     * @return CommandInterface
     */
    public function getCommand(): CommandInterface
    {
        return $this->command;
    }

    /**
     * @param CommandInterface $command
     */
    public function setCommand(CommandInterface $command): void
    {
        $this->command = $command;
    }
}
