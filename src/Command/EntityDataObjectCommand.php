<?php


namespace Akademiano\EntityOperator\Command;


abstract class EntityDataObjectCommand extends EntityObjectCommand implements EntityDataCommandInterface
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

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }
}
