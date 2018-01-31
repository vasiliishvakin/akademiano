<?php


namespace Akademiano\Content\Files\Model;


use Akademiano\Delegating\Command\CommandInterface;

abstract  class MimeyCommand implements CommandInterface
{
    /** @var File */
    protected $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

}
