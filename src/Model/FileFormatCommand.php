<?php

namespace Akademiano\Content\Files\Model;

use Akademiano\Delegating\Command\Command;

class FileFormatCommand extends Command
{
    const COMMAND_NAME = 'file.format';

    public function __construct(File $file, $savePath, $extension, string $template = null, $isPublic = false)
    {
        $class = get_class($file);
        $params['file'] = $file;
        $params['savePath'] = $savePath;
        $params['extension'] = $extension;
        $params['template'] = $template;
        $params['isPublic'] = $isPublic;
        parent::__construct($params, $class);
    }
}
