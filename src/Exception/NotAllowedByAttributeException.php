<?php

namespace Akademiano\Entity\Exception;

use Akademiano\HttpWarp\Exception\AccessDeniedException;
use Akademiano\Entity\EntityInterface;
use Akademiano\Entity\UuidableInterface;

class NotAllowedByAttributeException extends AccessDeniedException
{

    public function __construct(string $attribute, EntityInterface $entity, UuidableInterface $currentValue, UuidableInterface $needValue, $code = 0, \Exception $previous = null)
    {
        $message = sprintf('Item id %s not allowed %s (%s) in Entity is not equal to need %s (%s)',
            $entity->getUuid()->getHex(),
            $attribute,
            $currentValue->getUuid()->getHex(),
            $attribute,
            $needValue->getUuid()->getHex(),
        );
        parent::__construct($message, $code, $previous);
    }
}
