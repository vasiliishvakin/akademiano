<?php


namespace Akademiano\Content\Files\Model;


use Akademiano\Delegating\Command\Command;

abstract  class MimeyCommand extends Command
{
    const COMMAND_NAME = 'mimey';

    public function __construct(File $file)
    {
        $class = get_class($file);
        $params['file'] = $file;
        parent::__construct($params, $class);
    }
}
