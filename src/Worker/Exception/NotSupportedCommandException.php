<?php


namespace Akademiano\Operator\Worker\Exception;


use Akademiano\Delegating\Command\CommandInterface;
use Exception;

class NotSupportedCommandException extends \InvalidArgumentException
{
    public function __construct(CommandInterface $command, $code = 0, Exception $previous = null)
    {
        $message = "Not Supported command " . $command->getName() . " (" . get_class($command) . ")";
        parent::__construct($message, $code, $previous);
    }
}
