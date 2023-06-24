<?php


namespace Akademiano\Content\Files\Model;


use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Operator\Worker\Exception\NotSupportedCommandException;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\Operator\Worker\WorkerMappingTrait;
use Akademiano\Operator\Worker\WorkerMetaMapPropertiesTrait;
use Akademiano\Operator\Worker\WorkerSelfInstancedInterface;
use Akademiano\Operator\Worker\WorkerSelfMapCommandsInterface;
use Akademiano\Operator\WorkersContainer;
use Mimey\MimeTypes;

class MimeyExtensionWorker implements WorkerInterface, WorkerSelfInstancedInterface, WorkerSelfMapCommandsInterface
{
    use WorkerMappingTrait;

    const WORKER_ID = 'mimeyExtensionWorker';
    const MIMEY_RESOURCE_ID = 'mimey';


    /** @var MimeTypes */
    protected $mimey;

    public static function getSelfInstance(WorkersContainer $container): WorkerInterface
    {
        $worker = new static();
        $mimey = $container->getOperator()->getDependencies()[self::MIMEY_RESOURCE_ID];
        $worker->setMimey($mimey);
        return $worker;
    }

    public static function getSupportedCommands(): array
    {
        return [
            MimeyExtensionCommand::class,
        ];
    }

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

    public function getExtension($mimeType)
    {
        return $this->getMimey()->getExtension($mimeType);
    }

    public function execute(CommandInterface $command)
    {
        if (!$command instanceof MimeyExtensionCommand) {
            throw new NotSupportedCommandException($command);
        }
        /** @var File $file */
        $file = $command->getFile();
        $mime = $file->getMimeType();
        return $this->getExtension($mime);
    }
}
