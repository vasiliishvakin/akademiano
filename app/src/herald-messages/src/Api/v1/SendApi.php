<?php


namespace Akademiano\HeraldMessages\Api\v1;


use Akademiano\Api\v1\AbstractApi;
use Akademiano\EntityOperator\EntityOperator;
use Akademiano\HeraldMessages\Model\Message;
use Akademiano\HeraldMessages\Model\SendEmailCommand;
use Akademiano\HeraldMessages\Model\TransportType;
use Akademiano\HttpWarp\Exception\NotFoundException;
use Akademiano\Operator\IncludeOperatorInterface;
use Akademiano\Operator\IncludeOperatorTrait;
use Akademiano\User\CustodianIncludeInterface;
use Akademiano\User\CustodianIncludeTrait;

class SendApi extends AbstractApi implements IncludeOperatorInterface, CustodianIncludeInterface
{
    const API_ID = "heraldSendEmailApi";

    use CustodianIncludeTrait;
    use IncludeOperatorTrait;

    /**
     * AbstractEntityApi constructor.
     * @param EntityOperator $operator
     */
    public function __construct(EntityOperator $operator = null)
    {
        if (null !== $operator) {
            $this->setOperator($operator);
        }
    }


    /** @var  MessagesApi */
    protected $messagesApi;

    /**
     * @return MessagesApi
     */
    public function getMessagesApi()
    {
        return $this->messagesApi;
    }

    /**
     * @param MessagesApi $messagesApi
     */
    public function setMessagesApi(MessagesApi $messagesApi)
    {
        $this->messagesApi = $messagesApi;
    }

    public function send($id):int
    {
        $this->getAclManager()->disableAccessCheck();

        /** @var Message $message */
        $message = $this->getMessagesApi()->get($id)->getOrThrow(new NotFoundException(sprintf('Message with id %s not found.', dechex($id))));

        switch ($message->getTransport()->getValue()) {
            case TransportType::EMAIL:
                $sendCommand = new SendEmailCommand($message);
                break;
            default:
                throw new \RuntimeException(sprintf('Message with id %s transport type %s, need transport type %s', $message->getId(), $message->getTransport(), TransportType::EMAIL));
        }
        $result = $this->getOperator()->execute($sendCommand);
        return $result;
    }
}
