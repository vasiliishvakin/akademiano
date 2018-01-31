<?php


namespace Akademiano\Operator\Command;

use Akademiano\Delegating\Command\CommandInterface;

interface AfterCommandInterface extends SubCommandInterface
{
    public function __construct(CommandInterface $command, $result);

    public function addResult($result);

    public function extractResult();
}
