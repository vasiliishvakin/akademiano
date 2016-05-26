<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace EntityOperator;


use EntityOperator\Worker\WorkerInterface;
use DeltaCore\Application;
use DeltaCore\ModuleManager;
use EntityOperator\Operator\Operator;
use Pimple\Container;

class Module
{
    public static function init(ModuleManager $moduleManager, Application $application)
    {
        $application->extend("EntityOperator", function (Operator $operator, Container $c) use ($moduleManager) {
            $workers = $moduleManager->getListArrayConfigs("workers");
            foreach ($workers as $workerName => $data) {
                $workerParams = [];
                if (is_callable($data)) {
                    $function = $data;
                } elseif (is_array($data)) {
                    foreach ($data as $key => $row) {
                        if (is_callable($row)) {
                            $function = $row;
                        } else {
                            switch ($key) {
                                case WorkerInterface::PARAM_TABLEID: {
                                    $operator->setWorkerTable($row, $workerName);
                                    break;
                                }
                                case WorkerInterface::PARAM_ACTIONS_MAP: {
                                    foreach ($row as $action=>$actionParam) {
                                        if (!is_array($actionParam)) {
                                            $operator->addAction($action, $workerName, $actionParam);
                                        } else {
                                            foreach ($actionParam as $class=>$order) {
                                                $operator->addAction($action, $workerName, $class, $order);
                                            }
                                        }
                                    }
                                    break;
                                }
                                default: {
                                    $workerParams[$key] = $row;
                                    break;
                                }
                            }
                        }
                    }
                }
                $operator->addWorker($workerName, $function);
                $operator->setWorkerParams($workerName, $workerParams);
            }
            return $operator;
        });
    }
}
