<?php

namespace Akademiano\Db\Adapter\WhereParams;


class Between 
{
    protected $start;
    protected $end;

    public function __construct($start, $end)
    {
        $this->setStart($start);
        $this->setEnd($end);
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param mixed $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param mixed $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }
}
