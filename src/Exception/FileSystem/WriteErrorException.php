<?php


namespace Akademiano\Utils\Exception\FileSystem;


class WriteErrorException extends AkademianoFileSystemException
{
    public function __construct(string $path, string $params = null, $code = 0, \Throwable $previous = null)
    {
        parent::__construct(sprintf('Could not write "%s" %s', $path, $params ? sprintf(' with params(%s)', $params) : ''), $code, $previous);
    }
}
