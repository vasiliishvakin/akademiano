<?php


namespace Akademiano\Messages\Model;


use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Delegating\DelegatingInterface;
use Akademiano\Delegating\DelegatingTrait;
use Akademiano\EntityOperator\Command\SaveCommand;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\Operator\Worker\WorkerMappingTrait;
use Akademiano\Operator\Worker\WorkerSelfInstancedInterface;
use Akademiano\Operator\Worker\WorkerSelfInstanceTrait;
use Akademiano\Operator\Worker\WorkerSelfMapCommandsInterface;
use Akademiano\Operator\WorkersContainer;

class SendMessageEmailWorker implements WorkerInterface, DelegatingInterface, WorkerSelfInstancedInterface, WorkerSelfMapCommandsInterface
{
    const WORKER_ID = 'sendMessageEmailWorker';

    use DelegatingTrait;
    use WorkerMappingTrait;
    use WorkerSelfInstanceTrait;

    /** @var  \Swift_Mailer */
    protected $mailer;

    protected $from;

    public static function getSelfInstance(WorkersContainer $container): WorkerInterface
    {
        $worker = new static();
        $config = $container->getDependencies()["config"];
        $worker->setMailer($container->getDependencies()["mailer"]);
        $from = $config->getOrThrow(["email", "smtp", "from"]);
        $from = ($from instanceof \Akademiano\Config\Config) ? $from->toArray() : $from;
        $worker->setFrom($from);
        return $worker;
    }

    public static function getSupportedCommands(): array
    {
        return [
            SendEmailCommand::class,
        ];
    }

    public function execute(CommandInterface $command)
    {
        if ($command instanceof SendEmailCommand) {
            $message = $command->getMessage();
            return $this->send($message);
        } else {
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
        $this->delegate(new SaveCommand($message));
        try {
            $swiftMessage = $this->prepareMessage($message);
            $mailer = $this->getMailer();
            $result = $mailer->send($swiftMessage);
//            $result = 1;
            if ($result === 0) {
                throw new \RuntimeException(sprintf('Message id "%s" with theme "%s" to "%s" not send', $message->getId(), $message->getTitle(), $message->getTo()->getEmail()));
            }
            $message->setStatus(new Status(Status::STATUS_DONE));
            $this->delegate(new SaveCommand($message));
            return true;
        } catch (\Exception $e) {
            $message->setStatus(new Status(Status::STATUS_ERROR));
            $this->delegate(new SaveCommand($message));
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
            ->setBody(strip_tags($message->getContent()))
            ->addPart($message->getContent(), 'text/html');
    }
}
