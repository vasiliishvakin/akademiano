<?php

namespace Akademiano\Entity\Exception;


class BadRelatedClassException extends \InvalidArgumentException
{
    public function __construct($message = null, $code = 0, \Exception $previous = null, $class = null)
    {
        if (empty($message) && !empty($class)) {
            $message = sprintf('Class "%s" in not related class', $class);
        }
        parent::__construct($message, $code, $previous);
    }
}
