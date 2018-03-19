<?php


namespace Akademiano\Messages\Model;


use Akademiano\Delegating\Command\CommandInterface;
use Akademiano\Operator\Worker\WorkerInterface;
use Akademiano\Operator\Worker\WorkerMappingTrait;
use Akademiano\Operator\Worker\WorkerSelfInstancedInterface;
use Akademiano\Operator\Worker\WorkerSelfMapCommandsInterface;
use Akademiano\Operator\WorkersContainer;
use Akademiano\SimplaView\ViewInterface;
use Akademiano\Utils\StringUtils;

class ParseMessageWorker implements WorkerInterface, WorkerSelfMapCommandsInterface, WorkerSelfInstancedInterface
{
    const WORKER_ID = 'parseMessageWorker';

    use WorkerMappingTrait;

    /** @var  ViewInterface */
    protected $view;

    public static function getSelfInstance(WorkersContainer $container): WorkerInterface
    {
        $worker = new static();
        $view = $container->getDependencies()["view"];
        $worker->setView($view);
        return $worker;
    }

    public static function getSupportedCommands(): array
    {
        return [
            ParseMessageCommand::class,
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
        if ($command instanceof ParseMessageCommand) {
            return $this->parse($command->getMessage(), $command->getTemplate());
        } else {
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
