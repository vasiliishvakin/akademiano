<?php


namespace Akademiano\Messages\Model;


use Akademiano\Delegating\Command\CommandInterface;

class SendEmailCommand implements CommandInterface
{
    /** @var Message */
    protected $message;


    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }
}
