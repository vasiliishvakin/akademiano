<?php


namespace Akademiano\EntityOperator\Command;


use Akademiano\Delegating\Command\CommandInterface;

class GetTableIdCommand implements CommandInterface
{
    /** @var string */
    protected $workerId;

    public function __construct(string $workerId)
    {
        $this->setWorkerId($workerId);
    }

    /**
     * @return string
     */
    public function getWorkerId(): string
    {
        return $this->workerId;
    }

    /**
     * @param string $workerId
     */
    public function setWorkerId(string $workerId): void
    {
        $this->workerId = $workerId;
    }
}
