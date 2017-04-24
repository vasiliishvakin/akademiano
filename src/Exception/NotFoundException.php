<?php

namespace Akademiano\HttpWarp\Exception;



class NotFoundException extends HttpUsableException
{
    public function __construct($message = "Not Found", $code = 404, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
