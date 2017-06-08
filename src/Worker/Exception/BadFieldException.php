<?php


namespace Akademiano\EntityOperator\Worker\Exception;


use Akademiano\EntityOperator\Worker\RelationsWorker;

class BadFieldException extends \InvalidArgumentException
{
    public function __construct(RelationsWorker $worker, $field, $code = 0, \Exception $previous = null)
    {
        $message = "";
        parent::__construct($message, $code, $previous);
    }
}
