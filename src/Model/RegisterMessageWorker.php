<?php

namespace Akademiano\HeraldMessages\Model;


use Akademiano\EntityOperator\Command\SaveCommand;
use Akademiano\EntityOperator\EntityOperator;
use Akademiano\Operator\Command\AfterCommand;
use Akademiano\Operator\Command\AfterCommandInterface;
use Akademiano\Operator\Command\CommandInterface;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\Operator\Worker\WorkerMetaMapPropertiesTrait;
use Pheanstalk\Pheanstalk;

/**
 * @method EntityOperator getOperator()
 */
class RegisterMessageWorker implements WorkerInterface
{
    const COMMAND_NAME = AfterCommand::PREFIX_COMMAND_AFTER . SaveCommand::COMMAND_NAME;
    const WORKER_NAME = 'registerMessageWorker';
    const TUBE_NAME = 'messages_send_queue';

    use WorkerMetaMapPropertiesTrait;

    protected static function getDefaultMapping()
    {
        return [
            self::COMMAND_NAME => null,
        ];
    }

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case self::COMMAND_NAME : {
                if ($command instanceof AfterCommandInterface) {
                    return $this->createNotify($command);
                }
                break;
            }
            default:
                throw new \InvalidArgumentException(sprintf('Command type "%s" ("%s") not supported in worker "%s"', $command->getName(), get_class($command), get_class($this)));
        }
    }

    public function createNotify(AfterCommandInterface $command)
    {
        if ($command->extractResult() !== true) {
            return false;
        }
        /** @var Message $message */
        $message = $command->getParams("entity");


        $pheanstalk = new Pheanstalk('127.0.0.1');

// ----------------------------------------
// producer (queues jobs)

        $pheanstalk
            ->useTube(self::TUBE_NAME)
            ->put(json_encode(['id'=>$message->getId()]));
        return $message;
    }
}
