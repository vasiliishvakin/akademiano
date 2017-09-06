<?php


namespace Akademiano\Messages\Model;


use Akademiano\Operator\Command\Command;

class SendEmailCommand extends Command
{
    const COMMAND_NAME = "send.email";
    const PARAM_MESSAGE = "message";

    public function __construct(Message $message)
    {
        $params[self::PARAM_MESSAGE] = $message;
        parent::__construct($params, get_class($message));
    }
}
