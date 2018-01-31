<?php


namespace Akademiano\Operator\Command;


use Akademiano\Utils\ArrayTools;
use Akademiano\Delegating\Command\Command;
use Akademiano\Delegating\Command\CommandInterface;

class SubCommand implements SubCommandInterface
{

    /** @var  CommandInterface */
    protected $command;

    public function __construct(CommandInterface $command)
    {
        $this->command = $command;
    }

    public function getParentCommand():CommandInterface
    {
        return $this->command;
    }
}
