<?php


namespace Akademiano\Messages\Model;


use Akademiano\Operator\Command\CommandInterface;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\Operator\Worker\WorkerMetaMapPropertiesTrait;
use Akademiano\SimplaView\ViewInterface;
use Akademiano\Utils\StringUtils;

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
        if (null === $template) {
            $messageTransport = $message->getTransport();
            $possibleTemplates = [];
            switch ($messageTransport->getInt()) {
                case TransportType::EMAIL:
                    $transportName = "email";
                    break;
                case TransportType::WEB:
                    $transportName = "web";
                    break;
                default:
                    $transportName = "default";
            }
            $params = $message->getParams();
            if (isset($params["class"])) {
                $class = strtolower($params["class"]);
                $classLong = str_replace("\\", "_", strtolower($class));
                $possibleTemplates[] = "Akademiano\\Messages\\MessageTemplates\\" . $transportName . "\\" . $classLong;

                $classShort = StringUtils::cutClassName($class);
                $possibleTemplates[] = "Akademiano\\Messages\\MessageTemplates\\" . $transportName . "\\" . $classShort;

                if ($transportName !== "default") {
                    $possibleTemplates[] = "Akademiano\\Messages\\MessageTemplates\\default" . "\\" . $classLong;
                    $possibleTemplates[] = "Akademiano\\Messages\\MessageTemplates\\default" . "\\" . $classShort;
                }
            }
            foreach ($possibleTemplates as $templateRow) {
                if ($this->getView()->exist($templateRow)) {
                    $template = $templateRow;
                    break;
                }
            }
        }
        if (empty($template)) {
            $template = "Akademiano\\Messages\\MessageTemplates\\default\\default";
        }

        return $this->getView()->render(["message" => $message, "params" => $message->getParams()], $template);
    }
}
