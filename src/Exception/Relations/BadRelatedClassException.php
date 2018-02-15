<?php

namespace Akademiano\Entity\Exception\Relations;


use Akademiano\Entity\Exception\EntityException;

class BadRelatedClassException extends EntityException
{
    public function __construct($message = null, $code = 0, \Exception $previous = null, $class = null)
    {
        if (empty($message) && !empty($class)) {
            $message = sprintf('Class "%s" in not related class', $class);
        }
        parent::__construct($message, $code, $previous);
    }
}
