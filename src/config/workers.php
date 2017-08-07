<?php
use Akademiano\EntityOperator\Worker\WorkerInterface;
use Akademiano\Operator\WorkersContainerInterface;

return [
    "countriesWorker" => [
        \Akademiano\Content\Countries\Model\CountriesWorker::class,
        WorkerInterface::PARAM_ACTIONS_MAP => \Akademiano\Content\Countries\Model\Country::class,
        function (WorkersContainerInterface $s) {
            $w = new  \Akademiano\Content\Countries\Model\CountriesWorker();
            $adapter = $s->getOperator()->getDependency("dbAdapter");
            $w->setAdapter($adapter);
            return $w;
        },
    ],
];
