<?php


namespace Akademiano\HeraldMessages\Model;


use Akademiano\EntityOperator\EntityOperator;
use Akademiano\HeraldMessages\Model\Exception\SendException;
use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Delegating\DelegatingInterface;
use Akademiano\Delegating\DelegatingTrait;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\Operator\Worker\WorkerMetaMapPropertiesTrait;
use Akademiano\Utils\ArrayTools;
use GuzzleHttp\Client;
use function GuzzleHttp\Promise\settle;
use Psr\Http\Message\ResponseInterface;

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

    /** @var Client */
    protected $httpClient;

    protected static function getDefaultMapping()
    {
        return [
            SendEmailCommand::COMMAND_NAME => null,
        ];
    }

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case SendEmailCommand::COMMAND_NAME :
                {
                    $message = $command->getParams(SendEmailCommand::PARAM_MESSAGE);
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

    /**
     * @return Client
     */
    public function getHttpClient(): Client
    {
        if (null === $this->httpClient) {
            $this->httpClient = new Client();
        }
        return $this->httpClient;
    }

    /**
     * @param mixed $httpClient
     */
    public function setHttpClient(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function send(Message $message): int
    {
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
            return $result;
        } catch (\Exception $e) {
            $message->setStatus(new Status(Status::STATUS_ERROR));
            $this->getOperator()->save($message);
            throw new SendException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param Message $message
     * @return \Swift_Message
     */
    public function prepareMessage(Message $message)
    {
        $type = ArrayTools::get($message->getParams(), 'type', 'text/html');
        $emailMessage = (new \Swift_Message($message->getTitle()))
            ->setFrom($message->getFrom())
            ->setReplyTo($message->getFrom())
            ->setTo($message->getTo())
            ->setBody($message->getContent(), $type);
        if ($type === 'text/html') {
            $emailMessage->addPart(strip_tags($message->getContent()), 'text/plain');
        }
        //add files
        $files = ArrayTools::lists($message->getParams(), 'file');
        $promises = [];
        foreach ($files as $file) {
            $scheme = parse_url($file, PHP_URL_SCHEME);
            if (null === $scheme) {
                if (!file_exists($file)) {
                    $file = null;
                }
                $attach = \Swift_Attachment::fromPath($file);
                $emailMessage->attach($attach);
            } else {
                //check by guzzle
                $httpClient = $this->getHttpClient();
                $promise = $httpClient->requestAsync('HEAD', $file);
                $promise->then(
                    function (ResponseInterface $res) use ($file, $emailMessage) {
                        $code = $res->getStatusCode();
                        if ($code === 200) {
                            $attach = \Swift_Attachment::fromPath($file);
                            $emailMessage->attach($attach);
                        }
                    }
                );
                $promises[] = $promise;
            }
        }
        if (!empty($promises)) {
            settle($promises)->wait();
        }
        return $emailMessage;
    }
}
