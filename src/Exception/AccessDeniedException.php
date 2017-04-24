<?php

namespace Akademiano\HttpWarp\Exception;



class AccessDeniedException extends HttpUsableException
{
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        $code = 403;
        parent::__construct($message, $code, $previous);
    }
}
