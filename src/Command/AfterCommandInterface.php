<?php


namespace DeltaPhp\Operator\Command;


interface AfterCommandInterface extends PreAfterCommandInterface
{
    const PREFIX_COMMAND_AFTER = "after.";
    const COMMAND_AFTER_FIND = self::PREFIX_COMMAND_AFTER . CommandInterface::COMMAND_FIND;
    const COMMAND_AFTER_GET = self::PREFIX_COMMAND_AFTER . CommandInterface::COMMAND_GET;
    /**
     * AfterCommandInterface constructor.
     * @param CommandInterface $command
     * @param mixed|\SplStack $result
     */
    public function __construct(CommandInterface $command, \SplStack $result);

    public function addResult($result);

    public function extractResult();
}
