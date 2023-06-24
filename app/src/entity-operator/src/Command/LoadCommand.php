<?php


namespace Akademiano\EntityOperator\Command;

use Akademiano\Entity\Entity;
use Akademiano\Operator\Command\Command;
use Akademiano\Entity\EntityInterface;

class LoadCommand extends EntityObjectCommand
{
    /** @var array */
    protected $data;

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): LoadCommand
    {
        $this->data = $data;
        return $this;
    }
}
