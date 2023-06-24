<?php


namespace Akademiano\Utils\Exception\FileSystem;

class NotAllowedPathException extends AkademianoFileSystemException
{
    public function __construct(string $checkedPath, string $allowedPath, $code = 0, \Throwable $previous = null)
    {
        parent::__construct(sprintf('Do not allow use "%s", only allowed paths in "%s"', $checkedPath, $allowedPath), $code, $previous);
    }
}
