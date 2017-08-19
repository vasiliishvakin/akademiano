<?php


namespace Akademiano\Messages\Model;


use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Worker\PostgresWorker;

class MessagesWorker extends PostgresWorker
{
    const TABLE_ID = 14;
    const TABLE_NAME = "messages";
    const EXPAND_FIELDS = ["title", "description", "content", "to", "from", "status", "params"];

}
