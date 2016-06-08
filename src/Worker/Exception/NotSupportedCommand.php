<?php


namespace DeltaPhp\Operator\Worker\Exception;


use DeltaPhp\Operator\Command\CommandInterface;
use Exception;

class NotSupportedCommand extends \InvalidArgumentException
{
    public function __construct(CommandInterface $command, $code = 0, Exception $previous = null)
    {
        $message = "Not Supported command " . $command->getName() . " (" . get_class($command) . ")";
        parent::__construct($message, $code, $previous);
    }
}
