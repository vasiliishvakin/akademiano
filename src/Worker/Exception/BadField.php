<?php


namespace DeltaPhp\Operator\Worker\Exception;


use DeltaPhp\Operator\Worker\RelationsWorker;
use Exception;

class BadField extends \InvalidArgumentException
{
    public function __construct(RelationsWorker $worker, $field, $code = 0, Exception $previous = null)
    {
        $message = "";
        parent::__construct($message, $code, $previous);
    }
}
