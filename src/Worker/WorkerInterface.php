<?php

namespace Akademiano\EntityOperator\Worker;


interface WorkerInterface extends \Akademiano\Operator\Worker\WorkerInterface
{
    const PARAM_TABLEID = "tableId";
    const PARAM_ACTIONS_MAP = "map";
}
