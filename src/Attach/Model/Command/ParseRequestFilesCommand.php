<?php


namespace Attach\Model\Command;


use DeltaPhp\Operator\Command\CommandInterface;
USE DeltaPhp\Operator\Command\Command;
use HttpWarp\Request;
use DeltaUtils\FileSystem;

class ParseRequestFilesCommand extends Command implements CommandInterface
{
    const COMMAND_PARSE_REQUEST_FILES = "parse.request.files";

    public function __construct(Request $request, $fileClass, array $fileTypes = [FileSystem::FST_IMAGE], $params = [])
    {
        $params["request"] = $request;
        $params["fileTypes"] = $fileTypes;
        parent::__construct($params, $fileClass, self::COMMAND_PARSE_REQUEST_FILES);
    }

}
