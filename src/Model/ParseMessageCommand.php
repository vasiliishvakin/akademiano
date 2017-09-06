<?php


namespace Akademiano\Messages\Model;


use Akademiano\Operator\Command\Command;

class ParseMessageCommand extends Command
{
    const COMMAND_NAME = "parse.message.template";

    const PARAM_MESSAGE = "message";
    const PARAM_TEMPLATE = "template";

    public function __construct(Message $message, $template = null)
    {
        $params[self::PARAM_MESSAGE] = $message;
        if (null !== $template) {
            $params[self::PARAM_TEMPLATE] = $template;
        }
        parent::__construct($params, get_class($message));
    }
}
