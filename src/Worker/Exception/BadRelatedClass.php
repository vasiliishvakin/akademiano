<?php


namespace DeltaPhp\Operator\Worker\Exception;


use DeltaPhp\Operator\Worker\RelationsWorker;
use Exception;

class BadRelatedClass extends \InvalidArgumentException
{
    public function __construct(RelationsWorker $worker = null, $class = null, $code = 0, Exception $previous = null)
    {
        if (!empty($worker) && !empty($class)) {
            $message = "In worker " . get_class($worker) . " bad class " . $class;
        }
        parent::__construct($message, $code, $previous);
    }
}
