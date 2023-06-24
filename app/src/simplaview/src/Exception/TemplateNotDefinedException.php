<?php

namespace Akademiano\SimplaView\Exception;

use Akademiano\Utils\Exception\AkademianoException;
use Throwable;

class TemplateNotDefinedException extends AkademianoException
{
    private const MESSAGE = "Template not defined.";

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if (empty($message)) {
            $message = self::MESSAGE;
        }
        parent::__construct($message, $code, $previous);
    }

}
