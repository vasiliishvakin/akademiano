<?php


namespace Akademiano\Content\Files\Model;


use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\Operator\Worker\WorkerMetaMapPropertiesTrait;
use Mimey\MimeTypes;

class MimeyExtensionWorker implements WorkerInterface
{
    const WORKER_NAME = 'mimeyExtensionWorker';

    use WorkerMetaMapPropertiesTrait;

    /** @var MimeTypes */
    protected $mimey;

    /**
     * @return MimeTypes
     */
    public function getMimey(): MimeTypes
    {
        return $this->mimey;
    }

    /**
     * @param MimeTypes $mimey
     */
    public function setMimey(MimeTypes $mimey): void
    {
        $this->mimey = $mimey;
    }

    protected static function getDefaultMapping()
    {
        return [
            MimeyExtensionCommand::COMMAND_NAME => null,
        ];
    }

    public function getExtension($mimeType)
    {
        return $this->getMimey()->getExtension($mimeType);
    }

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case MimeyExtensionCommand::COMMAND_NAME:
                /** @var File $file */
                $file = $command->getParams('file');
                $mime = $file->getMimeType();
                return $this->getExtension($mime);
            default:
                throw new \InvalidArgumentException("Command type \" {$command->getName()} not supported");
        }
    }
}
