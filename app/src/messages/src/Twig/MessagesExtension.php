<?php

namespace Akademiano\Messages\Twig;

use Akademiano\Messages\Api\v1\MessagesApi;
use Akademiano\Messages\Model\Status;
use Akademiano\Router\Router;
use Akademiano\User\AuthInterface;
use Akademiano\Utils\DIContainerIncludeInterface;
use Akademiano\Utils\Parts\DIContainerTrait;

class MessagesExtension extends \Twig_Extension implements DIContainerIncludeInterface
{
    use DIContainerTrait;

    public function getName()
    {
        return 'akademiano_messages';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'messages_in',
                [$this, 'currentUserMessages']
            ),
        ];
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->getDiContainer()["router"];
    }

    /**
     * @return AuthInterface
     */
    public function getCustodian()
    {
        return $this->getDiContainer()["custodian"];
    }

    public function getCurrentUser()
    {
        return $this->getCustodian()->getCurrentUser();
    }

    /**
     * @return MessagesApi
     */
    public function getMessagesApi()
    {
        return $this->getDiContainer()[MessagesApi::API_ID];

    }

    public function currentUserMessages()
    {
        $criteria = ["to" => $this->getCurrentUser(), "status" => Status::STATUS_NEW];
        $messages = $this->getMessagesApi()->find($criteria);
        return $messages;
    }
}
