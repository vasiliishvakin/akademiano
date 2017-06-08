<?php


namespace Akademiano\EntityOperator\Worker\Exception;


use Akademiano\EntityOperator\Worker\RelationsWorker;

class BadRelatedClassException extends \InvalidArgumentException
{
    public function __construct(RelationsWorker $worker = null, $class = null, $code = 0, \Exception $previous = null)
    {
        if (!empty($worker) && !empty($class)) {
            $message = "In worker " . get_class($worker) . " bad class " . $class;
        }
        parent::__construct($message, $code, $previous);
    }
}
