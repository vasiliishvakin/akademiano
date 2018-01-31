<?php


namespace Akademiano\EntityOperator\Command;

class GetCommand extends EntityCommand
{
    protected $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id): GetCommand
    {
        $this->id = $id;
        return $this;
    }
}
