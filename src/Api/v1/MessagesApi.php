<?php


namespace Akademiano\Messages\Api\v1;


use Akademiano\Api\v1\Entities\EntityApi;
use Akademiano\Messages\Model\Message;

class MessagesApi extends EntityApi
{
    const ENTITY_CLASS = Message::class;
    const API_ID = "messagesApi";
}
