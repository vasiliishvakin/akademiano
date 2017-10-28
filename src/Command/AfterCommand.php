<?php


namespace Akademiano\Operator\Command;


use Akademiano\Delegating\Command\CommandInterface;

class AfterCommand extends SubCommand implements AfterCommandInterface
{
    /** @var  \SplStack */
    protected $results;

    public function __construct(CommandInterface $command, \SplStack $results)
    {
        parent::__construct($command);
            $this->results = $results;
    }

    public function getPrefix()
    {
        return self::PREFIX_COMMAND_AFTER;
    }

    /**
     * @return \SplStack
     */
    public function getResults()
    {
        return $this->results;
    }

    public function addResult($result)
    {
        $this->getResults()->push($result);
    }

    public function extractResult()
    {
        return $this->getResults()->top();
    }
}
