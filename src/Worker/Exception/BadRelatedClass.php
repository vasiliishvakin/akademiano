<?php


namespace DeltaPhp\Operator\Worker\Exception;


use DeltaPhp\Operator\Worker\RelationsWorker;
use Exception;

class BadRelatedClass extends \InvalidArgumentException
{
    public function __construct(RelationsWorker $worker, $class, $code = 0, Exception $previous = null)
    {
        $message = "";
        parent::__construct($message, $code, $previous);
    }
}
