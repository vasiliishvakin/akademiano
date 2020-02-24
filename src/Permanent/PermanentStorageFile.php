<?php


namespace Akademiano\Config\Permanent;


class PermanentStorageFile implements PermanentStorageInterface
{
    /** @var string */
    protected $file;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }
}
