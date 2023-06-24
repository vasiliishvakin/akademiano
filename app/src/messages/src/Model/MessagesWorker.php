<?php


namespace Akademiano\Messages\Model;


use Akademiano\Entity\EntityInterface;
use Akademiano\EntityOperator\Worker\ContentEntitiesWorker;

class MessagesWorker extends ContentEntitiesWorker
{
    const TABLE_ID = 17;
    const TABLE_NAME = "messages";
    const FIELDS = ["to", "from", "status", "params", "transport"];
    const EXT_ENTITY_FIELDS = ['to', 'from'];

    public static function getEntityClassForMapFilter()
    {
        return Message::class;
    }

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
