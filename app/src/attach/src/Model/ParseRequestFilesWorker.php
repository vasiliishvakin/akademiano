<?php


namespace Akademiano\Attach\Model;


use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Operator\Worker\Exception\NotSupportedCommandException;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\Operator\Worker\WorkerMappingTrait;
use Akademiano\Operator\Worker\WorkerSelfInstancedInterface;
use Akademiano\Operator\Worker\WorkerSelfInstanceTrait;
use Akademiano\Operator\Worker\WorkerSelfMapCommandsInterface;
use Akademiano\Attach\Command\ParseRequestFilesCommand;

class ParseRequestFilesWorker implements WorkerInterface, WorkerSelfInstancedInterface, WorkerSelfMapCommandsInterface
{
    const WORKER_ID = 'parseRequestFilesWorker';

    const REQUEST_FILES_DATA = 'filesData';

    use WorkerSelfInstanceTrait;
    use WorkerMappingTrait;

    public static function getSupportedCommands(): array
    {
        return [
            ParseRequestFilesCommand::class,
        ];
    }

    public function execute(CommandInterface $command)
    {
        if (!$command instanceof ParseRequestFilesCommand) {
            throw new NotSupportedCommandException($command);
        }

        return (new RequestFiles($command->getRequest()))
            ->setFilesTypes($command->getFileTypes())
            ->setMaxFilesSize($command->getMaxFileSize()
            );
    }
}
