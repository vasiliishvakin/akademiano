<?php


namespace DeltaPhp\Operator\Command;


interface PreCommandInterface extends CommandInterface, PreAfterCommandInterface
{
    const PREFIX_COMMAND_PRE = "pre.";

    public function __construct(CommandInterface $command);

    public function addParams(array $params);

    /**
     * @return CommandInterface
     */
    public function extractParentCommand();
}
