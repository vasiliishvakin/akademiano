<?php


namespace Akademiano\Messages\Model;


use Akademiano\Delegating\Command\CommandInterface;

class ParseMessageCommand implements CommandInterface
{
    /** @var Message */
    protected $message;

    /** @var string */
    protected $template;

    public function __construct(Message $message, $template = null)
    {
        $this->message = $message;
        $this->template = $template;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getTemplate(): ?string
    {
        return $this->template;
    }
}
