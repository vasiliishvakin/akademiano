<?php


namespace EntityOperator\Worker\Exception;


use EntityOperator\Command\CommandInterface;
use Exception;

class NotSupportedCommand extends \InvalidArgumentException
{
    public function __construct(CommandInterface $command, $code, Exception $previous)
    {
        $message = "Not Supported command " . $command->getName() . " (". get_class($command) . ")";
        parent::__construct($message, $code, $previous);
    }
}
