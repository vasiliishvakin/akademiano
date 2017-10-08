<?php


namespace Akademiano\HeraldMessages\Model;


use Akademiano\EntityOperator\EntityOperator;
use Akademiano\Operator\Command\CommandInterface;
use Akademiano\Operator\DelegatingInterface;
use Akademiano\Operator\DelegatingTrait;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\Operator\Worker\WorkerMetaMapPropertiesTrait;

/**
 * Class SendMessageEmailWorker
 * @package Akademiano\Messages\Model
 * @method EntityOperator getOperator()
 */
class SendMessageEmailWorker implements WorkerInterface, DelegatingInterface
{
    use DelegatingTrait;
    use WorkerMetaMapPropertiesTrait;

    /** @var  \Swift_Mailer */
    protected $mailer;

    protected $from;

    protected static function getDefaultMapping()
    {
        return [
            SendEmailCommand::COMMAND_NAME => null,
        ];
    }

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case SendEmailCommand::COMMAND_NAME : {
                $message = $command->getParams(ParseMessageCommand::PARAM_MESSAGE);
                return $this->send($message);
            }
            default:
                throw new \InvalidArgumentException(sprintf('Command type "%s" ("%s") not supported in worker "%s"', $command->getName(), get_class($command), get_class($this)));
        }
    }

    /**
     * @return \Swift_Mailer
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * @param \Swift_Mailer $mailer
     */
    public function setMailer(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param mixed $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    public function send(Message $message)
    {
        $message->setStatus(new Status(Status::STATUS_DO));
        $this->getOperator()->save($message);
        try {
            $swiftMessage = $this->prepareMessage($message);
            $mailer = $this->getMailer();
            $result = $mailer->send($swiftMessage);
//            $result = 1;
            if ($result === 0) {
                throw new \RuntimeException(sprintf('Message id "%s" with theme "%s" to "%s" not send', $message->getId(), $message->getTitle(), $message->getTo()->getEmail()));
            }
            $message->setStatus(new Status(Status::STATUS_DONE));
            $this->getOperator()->save($message);
            return true;
        } catch (\Exception $e) {
            $message->setStatus(new Status(Status::STATUS_ERROR));
            $this->getOperator()->save($message);
            return false;
        }
    }

    /**
     * @param Message $message
     * @return \Swift_Message
     */
    public function prepareMessage(Message $message)
    {
        return (new \Swift_Message($message->getTitle()))
            ->setFrom($this->getFrom())
            ->setTo($message->getTo()->getEmail(), $message->getTo()->getTitle())
            ->setBody($message->getContent());
    }
}
