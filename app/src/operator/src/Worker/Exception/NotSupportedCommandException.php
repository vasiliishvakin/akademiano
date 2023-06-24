<?php


namespace Akademiano\Operator\Worker\Exception;


use Akademiano\Delegating\Command\CommandInterface;
use Exception;

class NotSupportedCommandException extends WorkerException
{
    public function __construct(CommandInterface $command, $code = 0, Exception $previous = null)
    {
        $message = sprintf('Not supported command "%s"', get_class($command));
        parent::__construct($message, $code, $previous);
    }
}
