<?php

namespace Akademiano\Content\Files\Model;

use Akademiano\Delegating\Command\CommandInterface;

class FileFormatCommand implements CommandInterface
{
    /** @var File */
    protected $file;

    protected $savePath;

    protected $extension;

    protected $template;

    protected $isPublic = false;

    public function __construct(File $file, $savePath, $extension)
    {
        $this->file = $file;
        $this->savePath = $savePath;
        $this->extension = $extension;
    }

    public function setTemplate($template): self
    {
        $this->template = $template;
        return $this;
    }

    public function setPublic(bool $isPublic = true): self
    {
        $this->isPublic = $isPublic;
        return $this;
    }


    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @return mixed
     */
    public function getSavePath()
    {
        return $this->savePath;
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->isPublic;
    }
}
