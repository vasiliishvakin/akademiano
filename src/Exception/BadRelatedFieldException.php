<?php

namespace Akademiano\EntityOperator\Exception;


use Akademiano\Operator\Exception\OperatorException;

class BadRelatedFieldException extends OperatorException
{
    public function __construct($message = null, $code = 0, \Exception $previous = null, $field = null)
    {
        if (empty($message) && !empty($class)) {
            $message = sprintf('Field "%s" in not related field', $field);
        }
        parent::__construct($message, $code, $previous);
    }
}
