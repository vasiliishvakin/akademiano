<?php


namespace Akademiano\Utils\Exception\FileSystem;


class NotWritableException extends AkademianoFileSystemException
{
    public function __construct(string $path, $code = 0, \Throwable $previous = null)
    {
        parent::__construct(sprintf('Not writable path "%s"', $path), $code, $previous);
    }
}
