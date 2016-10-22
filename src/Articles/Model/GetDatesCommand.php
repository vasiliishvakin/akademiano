<?php


namespace Articles\Model;


use DeltaDb\D2QL\Criteria;
use DeltaPhp\Operator\Command\Command;
use DeltaPhp\Operator\Command\CommandInterface;

class GetDatesCommand extends Command  implements CommandInterface
{
    const COMMAND_GET_DATES = "get_dates";
    protected $name = self::COMMAND_GET_DATES;

    public function __construct(Criteria $criteria = null)
    {
        $this->class = Article::class;
        $this->params["criteria"] = $criteria;
    }
}
