<?php


namespace EntityOperator\Worker\Exception;


use EntityOperator\Worker\RelationsWorker;
use Exception;

class BadField extends \InvalidArgumentException
{
    public function __construct(RelationsWorker $worker, $field, $code = 0, Exception $previous = null)
    {
        $message = "";
        parent::__construct($message, $code, $previous);
    }
}
