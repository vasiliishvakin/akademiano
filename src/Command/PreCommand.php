<?php


namespace EntityOperator\Command;


class PreCommand extends SubCommand implements PreCommandInterface
{
    /** @var  \SplStack */
    protected $paramsStack;

    public function getPrefix()
    {
        return self::PREFIX_COMMAND_PRE;
    }

    public function getParamsStack()
    {
        if (null === $this->paramsStack) {
            $this->paramsStack = new \SplStack();
            if ($this->getParentCommand() instanceof CommandInterface) {
                $this->paramsStack->push($this->getParentCommand()->getParams());
            }
        }
        return $this->paramsStack;
    }
    
    public function getParams($path = null, $default = null)
    {
        if (null == $this->params) {
            $this->params = $this->getParamsStack()->top();
        }
        return parent::getParams($path, $default);
    }


    public function addParams(array $params)
    {
        $this->getParamsStack()->push($params);
        $this->params = null;
    }

    /**
     * @return CommandInterface
     */
    public function extractParentCommand()
    {
        /** @var Command $command */
        $command = $this->getParentCommand();
        $command->setParams($this->getParams());
        return $command;
    }

}
