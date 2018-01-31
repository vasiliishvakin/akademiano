<?php

namespace Akademiano\Operator\Command;

use Akademiano\Delegating\Command\CommandInterface;

class WorkerInfoCommand implements CommandInterface, OperatorSpecialCommandInterface
{
    protected $workerId;

    protected $attribute;

    public function __construct($workerId, $attribute)
    {
        $this->setWorkerId($workerId);
        $this->setAttribute($attribute);
    }

    /**
     * @param mixed $workerId
     */
    protected function setWorkerId($workerId): void
    {
        $this->workerId = $workerId;
    }

    /**
     * @param mixed $attribute
     */
    protected function setAttribute($attribute): void
    {
        $this->attribute = $attribute;
    }

    /**
     * @return mixed
     */
    public function getWorkerId()
    {
        return $this->workerId;
    }

    /**
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->attribute;
    }


}
