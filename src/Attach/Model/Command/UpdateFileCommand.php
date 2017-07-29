<?php


namespace Akademiano\Attach\Model\Command;

use DeltaPhp\Operator\Command\Command;
use DeltaPhp\Operator\Command\CommandInterface;

class UpdateFileCommand extends Command implements CommandInterface
{
    const COMMAND_UPDATE_FILE = "update.file";

    public function __construct(FileEntity $file, $params)
    {
        $params["file"] = $file;
        parent::__construct($params, null, self::COMMAND_UPDATE_FILE);
    }
}
