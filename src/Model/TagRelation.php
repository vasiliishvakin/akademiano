<?php


namespace Akademiano\Content\Tags\Model;


use Akademiano\Entity\RelationEntity;
use Akademiano\EntityOperator\Entity\RelationEntityFieldsTrait;
use Akademiano\UserEO\Model\Utils\OwneredTrait;

class TagRelation extends RelationEntity
{
    const FIRST_CLASS = Tag::class;

    use OwneredTrait;
    use RelationEntityFieldsTrait;
}
