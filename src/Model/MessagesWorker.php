<?php


namespace Akademiano\Messages\Model;


use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Worker\PostgresWorker;

class MessagesWorker extends PostgresWorker
{
    const TABLE_ID = 16;
    const TABLE_NAME = "messages";
    const EXPAND_FIELDS = ["title", "description", "content", "to", "from", "status", "params", "transport"];

    public function filterFieldToPostgresType($value, $fieldName = null, EntityInterface $entity = null)
    {
        if ($fieldName === "params") {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            return parent::filterFieldToPostgresType($value, $fieldName, $entity);
        }
    }

    public function load(EntityInterface $entity, array $data)
    {
        if (isset($data["params"])) {
            $data["params"] = json_decode($data["params"], true);
        }
        return parent::load($entity, $data);
    }
}
