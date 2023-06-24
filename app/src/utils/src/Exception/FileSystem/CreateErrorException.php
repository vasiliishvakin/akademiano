<?php


namespace Akademiano\Utils\Exception\FileSystem;


class CreateErrorException extends AkademianoFileSystemException
{
    public function __construct(string $path, $code = 0, \Throwable $previous = null)
    {
        parent::__construct(sprintf('Could not create "%s"', $path), $code, $previous);
    }
}
