<?php

namespace Akademiano\Entity\Exception\Relations;


use Akademiano\Entity\Exception\EntityException;

class BadRelatedFieldException extends EntityException
{
    public function __construct($message = null, $code = 0, \Exception $previous = null, $field = null)
    {
        if (empty($message) && !empty($class)) {
            $message = sprintf('Field "%s" in not related field', $field);
        }
        parent::__construct($message, $code, $previous);
    }
}
