<?php


namespace Akademiano\Messages\Model;


use Akademiano\Operator\Command\CommandInterface;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\Operator\Worker\WorkerMetaMapPropertiesTrait;
use Akademiano\SimplaView\ViewInterface;

class ParseMessageWorker implements WorkerInterface
{
    use WorkerMetaMapPropertiesTrait;

    /** @var  ViewInterface */
    protected $view;

    protected static function getDefaultMapping()
    {
        return [
            ParseMessageCommand::COMMAND_NAME => null,
        ];
    }

    /**
     * @return ViewInterface
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param ViewInterface $view
     */
    public function setView(ViewInterface $view)
    {
        $this->view = $view;
    }

    public function execute(CommandInterface $command)
    {
        switch ($command->getName()) {
            case ParseMessageCommand::COMMAND_NAME : {
                $message = $command->getParams(ParseMessageCommand::PARAM_MESSAGE);
                $template = $command->getParams(ParseMessageCommand::PARAM_TEMPLATE);
                return $this->parse($message, $template);
            }
            default:
                throw new \InvalidArgumentException(sprintf('Command type "%s" ("%s") not supported in worker "%s"', $command->getName(), get_class($command), get_class($this)));
        }
    }

    public function parse(Message $message, $template = null)
    {
        $params = $message->getParams();
        if (null !== $template) {
            $messageTransport = $message->getTransport();
        }
        return json_encode($params, JSON_PRETTY_PRINT || JSON_UNESCAPED_UNICODE);
    }
}
