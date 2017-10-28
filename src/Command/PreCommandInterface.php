<?php


namespace Akademiano\Operator\Command;

use Akademiano\Delegating\Command\CommandInterface;

interface PreCommandInterface extends CommandInterface, PreAfterCommandInterface
{
    const PREFIX_COMMAND_PRE = "pre.";

    public function __construct(CommandInterface $command);

    /**
     * @return CommandInterface
     */
    public function extractParentCommand();
}
