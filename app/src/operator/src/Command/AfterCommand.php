<?php


namespace Akademiano\Operator\Command;


use Akademiano\Delegating\Command\CommandInterface;

class AfterCommand extends SubCommand implements AfterCommandInterface
{
    /** @var  \SplStack */
    protected $results;

    public function __construct(CommandInterface $command, $results)
    {
        parent::__construct($command);
        if ($results instanceof \SplStack) {
            $this->results = $results;
        } else {
            $this->getResults()->push($results);
        }
    }

    /**
     * @return \SplStack
     */
    public function getResults(): \SplStack
    {
        if (null === $this->results) {
            $this->results = new \SplStack();
        }
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
