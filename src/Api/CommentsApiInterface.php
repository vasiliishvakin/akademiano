<?php

namespace Akademiano\Content\Comments\Api;

use Akademiano\Api\v1\Entities\EntityApiInterface;
use Akademiano\Entity\EntityInterface;

interface CommentsApiInterface extends EntityApiInterface
{
    public function saveBound(EntityInterface $entity, array $data);
}
