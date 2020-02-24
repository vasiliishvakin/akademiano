<?php


namespace Akademiano\Utils\Exception\FileSystem;


use Throwable;

class NotExistException extends AkademianoFileSystemException
{
    public function __construct(string $path, int $code = 0, Throwable $previous = null)
    {
        $message = sprintf('Path "%s" not exist', $path);
        parent::__construct($message, $code, $previous);
    }

}
