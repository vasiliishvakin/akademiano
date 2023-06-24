<?php


namespace Akademiano\Api\v1\Entities;


use Akademiano\Entity\RelationEntity;
use Akademiano\Entity\RelationsBetweenTrait;

class RelationEntityApi extends EntityApi
{
    const ENTITY_CLASS = RelationEntity::class;
    const API_ID = "relationsApi";

    private const RELATION_ENTITY_PARAM_FIRST_CLASS = 'FIRST_CLASS';
    private const RELATION_ENTITY_PARAM_SECOND_CLASS = 'SECOND_CLASS';

    private const RELATION_ENTITY_PARAM_FIRST_FIELD = 'FIRST_FIELD';
    private const RELATION_ENTITY_PARAM_SECOND_FIELD = 'SECOND_FIELD';

    use RelationsBetweenTrait;

    public function getFirstClass()
    {
        return constant(static::ENTITY_CLASS.'::'.self::RELATION_ENTITY_PARAM_FIRST_CLASS);
    }

    public function getSecondClass()
    {
        return constant(static::ENTITY_CLASS.'::'.self::RELATION_ENTITY_PARAM_SECOND_CLASS);
    }

    public function getFirstField()
    {
        return constant(static::ENTITY_CLASS.'::'.self::RELATION_ENTITY_PARAM_FIRST_FIELD);
    }

    public function getSecondField()
    {
        return constant(static::ENTITY_CLASS.'::'.self::RELATION_ENTITY_PARAM_SECOND_FIELD);
    }
}
