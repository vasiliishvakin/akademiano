<?php

namespace Akademiano\Attach\Model;

use Akademiano\Entity\Entity;
use Akademiano\EntityOperator\Entity\RelationEntity;

class EntityFileRelation extends RelationEntity
{
    const FIRST_CLASS = Entity::class;
    const SECOND_CLASS = File::class;
}
