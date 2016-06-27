<?php


namespace DeltaPhp\Operator\Command;


use DeltaUtils\ArrayUtils;

class PreCommand extends SubCommand implements PreCommandInterface
{
    /** @var  \SplStack */
    protected $paramsStack;
    protected $params = null;

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
        if (null === $this->params) {
            $this->params = $this->getParamsStack()->top();
        }
        return parent::getParams($path, $default);
    }

    public function hasParam($path)
    {
        if (null === $this->params) {
            $this->params = $this->getParamsStack()->top();
        }
        return parent::hasParam($path);
    }


    public function addParams($params, $path = null)
    {
        $paramsFull = $this->getParams();
        $paramsFull = ArrayUtils::set($paramsFull, $path, $params);
        $this->getParamsStack()->push($paramsFull);
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
