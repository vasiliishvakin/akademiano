<?php


namespace Akademiano\HeraldMessages\Model;


use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Worker\PostgresWorker;

class MessagesWorker extends PostgresWorker
{
    const TABLE_ID = 17;
    const TABLE_NAME = "herald_messages";
    const EXPAND_FIELDS = ["title", "description", "content", "to", "from" , "replayTo", "status", "data", "transport", "params"];

    public function filterFieldToPostgresType($value, $fieldName = null, EntityInterface $entity = null)
    {
        if ($fieldName === "data" || $fieldName === "params") {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            return parent::filterFieldToPostgresType($value, $fieldName, $entity);
        }
    }

    public function load(EntityInterface $entity, array $data)
    {
        if (isset($data["data"])) {
            $data["data"] = json_decode($data["data"], true);
        }
        if (isset($data["params"])) {
            $data["params"] = json_decode($data["params"], true);
        }

        return parent::load($entity, $data);
    }
}
