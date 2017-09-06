<?php


namespace Akademiano\Messages\Api\v1;


use Akademiano\Api\v1\AbstractApi;
use Akademiano\EntityOperator\EntityOperator;
use Akademiano\Messages\Model\SendEmailCommand;
use Akademiano\Messages\Model\Status;
use Akademiano\Messages\Model\TransportType;
use Akademiano\Operator\IncludeOperatorInterface;
use Akademiano\Operator\IncludeOperatorTrait;
use Akademiano\User\CustodianIncludeInterface;
use Akademiano\User\CustodianIncludeTrait;

class SendEmailsApi extends AbstractApi implements IncludeOperatorInterface, CustodianIncludeInterface
{
    const API_ID = "sendEmailApi";

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

    public function processQueue($count = 10)
    {
        $this->getAclManager()->disableAccessCheck();
        $messages = $this->getMessagesApi()->find(
            [
                "transport" => new TransportType(TransportType::EMAIL),
                "status" => new Status(Status::STATUS_NEW)
            ],
            1, "id", $count
        );

        $resultList = [];
        foreach ($messages as $message) {
            $sendCommand = new SendEmailCommand($message);
            $result = $this->getOperator()->execute($sendCommand);
            if ($result) {
                $resultList[Status::STATUS_DONE] [] = $message;
            } else {
                $resultList[Status::STATUS_ERROR][] = $message;
            }
        }
        $this->getAclManager()->enableAccessCheck();
        return $resultList;
    }
}
