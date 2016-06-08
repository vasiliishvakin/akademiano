<?php


namespace DeltaPhp\Operator\Command;


use DeltaUtils\ArrayUtils;

class SubCommand extends Command implements SubCommandInterface
{
    const PREFIX_COMMAND_SUB = "sub.";
    /** @var  CommandInterface */
    protected $command;

    public function getPrefix()
    {
        return self::PREFIX_COMMAND_SUB;
    }


    public function __construct(CommandInterface $command)
    {
        $this->command = $command;
    }

    protected function getParentCommand()
    {
        return $this->command;
    }

    public function getName()
    {
        if (null === $this->name) {
            $this->name = $this->getPrefix() . $this->getParentCommand()->getName();
        }
        return $this->name;
    }

    public function getClass()
    {
        if (null === $this->class) {
            $this->class =(string) $this->getParentCommand()->getClass();
        }
        return $this->class;
    }

    public function getParams($path = null, $default = null)
    {
        if (null === $this->params) {
            $this->params = $this->getParentCommand()->getParams();
        }
        if (null === $path) {
            return $this->params;
        } else {
            return ArrayUtils::get($this->params, $path, $default);
        }
    }
}
