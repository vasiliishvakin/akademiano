<?php


namespace Akademiano\Attach\Command;


use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\HttpWarp\Request;
use Akademiano\Utils\FileSystem;

class ParseRequestFilesCommand implements CommandInterface
{
    /** @var Request */
    protected $request;

    protected $fileTypes = [FileSystem::FST_IMAGE];

    /** @var int|null */
    protected $maxFileSize;


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return array
     */
    public function getFileTypes(): array
    {
        return $this->fileTypes;
    }

    /**
     * @param array $fileTypes
     */
    public function setFileTypes(array $fileTypes): void
    {
        $this->fileTypes = $fileTypes;
    }

    /**
     * @return int|null
     */
    public function getMaxFileSize(): ?int
    {
        return $this->maxFileSize;
    }

    /**
     * @param int|null $maxFileSize
     */
    public function setMaxFileSize(?int $maxFileSize): void
    {
        $this->maxFileSize = $maxFileSize;
    }
}
